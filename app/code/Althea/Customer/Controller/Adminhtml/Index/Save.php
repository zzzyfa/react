<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/04/2018
 * Time: 6:17 PM
 */

namespace Althea\Customer\Controller\Adminhtml\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Customer\Controller\Adminhtml\Index\Save {

	protected $_extensionAttributeFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Customer\Api\Data\AddressExtensionFactory $addressExtensionFactory,
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Model\AddressFactory $addressFactory,
		\Magento\Customer\Model\Metadata\FormFactory $formFactory,
		\Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
		\Magento\Customer\Helper\View $viewHelper,
		\Magento\Framework\Math\Random $random,
		CustomerRepositoryInterface $customerRepository,
		\Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
		Mapper $addressMapper,
		AccountManagementInterface $customerAccountManagement,
		AddressRepositoryInterface $addressRepository,
		CustomerInterfaceFactory $customerDataFactory,
		AddressInterfaceFactory $addressDataFactory,
		\Magento\Customer\Model\Customer\Mapper $customerMapper,
		\Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
		DataObjectHelper $dataObjectHelper,
		ObjectFactory $objectFactory,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
		\Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	)
	{
		$this->_extensionAttributeFactory = $addressExtensionFactory;

		parent::__construct($context, $coreRegistry, $fileFactory, $customerFactory, $addressFactory, $formFactory, $subscriberFactory, $viewHelper, $random, $customerRepository, $extensibleDataObjectConverter, $addressMapper, $customerAccountManagement, $addressRepository, $customerDataFactory, $addressDataFactory, $customerMapper, $dataObjectProcessor, $dataObjectHelper, $objectFactory, $layoutFactory, $resultLayoutFactory, $resultPageFactory, $resultForwardFactory, $resultJsonFactory);
	}

	/**
	 * @var EmailNotificationInterface
	 */
	private $emailNotification;

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		$returnToEdit = false;
		$originalRequestData = $this->getRequest()->getPostValue();

		$customerId = $this->getCurrentCustomerId();

		if ($originalRequestData) {
			try {
				// optional fields might be set in request for future processing by observers in other modules
				$customerData = $this->_extractCustomerData();
				$addressesData = $this->_extractCustomerAddressData($customerData);

				if ($customerId) {
					$currentCustomer = $this->_customerRepository->getById($customerId);
					$customerData = array_merge(
						$this->customerMapper->toFlatArray($currentCustomer),
						$customerData
					);
					$customerData['id'] = $customerId;
				}

				/** @var CustomerInterface $customer */
				$customer = $this->customerDataFactory->create();
				$this->dataObjectHelper->populateWithArray(
					$customer,
					$customerData,
					'\Magento\Customer\Api\Data\CustomerInterface'
				);
				$addresses = [];
				$preserve  = [];
				foreach ($addressesData as $addressData) {
					$region = isset($addressData['region']) ? $addressData['region'] : null;
					$regionId = isset($addressData['region_id']) ? $addressData['region_id'] : null;
					$addressData['region'] = [
						'region' => $region,
						'region_id' => $regionId,
					];
					$addressDataObject = $this->addressDataFactory->create();
					$this->dataObjectHelper->populateWithArray(
						$addressDataObject,
						$addressData,
						'\Magento\Customer\Api\Data\AddressInterface'
					);

					// althea:
					// - CustomerRepository was extended to update address only with is_update ext. attribute for API
					// - add is_update extension attribute to wanted address
					$extAttr = $addressDataObject->getExtensionAttributes();

					if (!$extAttr) {

						$extAttr = $this->_extensionAttributeFactory->create();
					}

					$extAttr->setIsUpdate(true);
					$addressDataObject->setExtensionAttributes($extAttr);

					$preserve[]  = $addressDataObject->getId();
					$addresses[] = $addressDataObject;
				}

				// althea:
				// - CustomerRepository was extended to update address only with is_delete ext. attribute for API
				// - add is_delete extension attribute to unwanted address
				foreach ($currentCustomer->getAddresses() as $address) {

					if (in_array($address->getId(), $preserve)) {

						continue;
					}

					$extAttr = $address->getExtensionAttributes();

					if (!$extAttr) {

						$extAttr = $this->_extensionAttributeFactory->create();
					}

					$extAttr->setIsDelete(true);
					$address->setExtensionAttributes($extAttr);

					$addresses[] = $address;
				}

				$this->_eventManager->dispatch(
					'adminhtml_customer_prepare_save',
					['customer' => $customer, 'request' => $this->getRequest()]
				);
				$customer->setAddresses($addresses);
				if (isset($customerData['sendemail_store_id'])) {
					$customer->setStoreId($customerData['sendemail_store_id']);
				}

				// Save customer
				if ($customerId) {
					$this->_customerRepository->save($customer);

					$this->getEmailNotification()->credentialsChanged($customer, $currentCustomer->getEmail());
				} else {
					$customer = $this->customerAccountManagement->createAccount($customer);
					$customerId = $customer->getId();
				}

				$isSubscribed = null;
				if ($this->_authorization->isAllowed(null)) {
					$isSubscribed = $this->getRequest()->getPost('subscription');
				}
				if ($isSubscribed !== null) {
					if ($isSubscribed !== 'false') {
						$this->_subscriberFactory->create()->subscribeCustomerById($customerId);
					} else {
						$this->_subscriberFactory->create()->unsubscribeCustomerById($customerId);
					}
				}

				// After save
				$this->_eventManager->dispatch(
					'adminhtml_customer_save_after',
					['customer' => $customer, 'request' => $this->getRequest()]
				);
				$this->_getSession()->unsCustomerFormData();
				// Done Saving customer, finish save action
				$this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
				$this->messageManager->addSuccess(__('You saved the customer.'));
				$returnToEdit = (bool)$this->getRequest()->getParam('back', false);
			} catch (\Magento\Framework\Validator\Exception $exception) {
				$messages = $exception->getMessages();
				if (empty($messages)) {
					$messages = $exception->getMessage();
				}
				$this->_addSessionErrorMessages($messages);
				$this->_getSession()->setCustomerFormData($originalRequestData);
				$returnToEdit = true;
			} catch (LocalizedException $exception) {
				$this->_addSessionErrorMessages($exception->getMessage());
				$this->_getSession()->setCustomerFormData($originalRequestData);
				$returnToEdit = true;
			} catch (\Exception $exception) {
				$this->messageManager->addException($exception, __('Something went wrong while saving the customer.'));
				$this->_getSession()->setCustomerFormData($originalRequestData);
				$returnToEdit = true;
			}
		}
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($returnToEdit) {
			if ($customerId) {
				$resultRedirect->setPath(
					'customer/*/edit',
					['id' => $customerId, '_current' => true]
				);
			} else {
				$resultRedirect->setPath(
					'customer/*/new',
					['_current' => true]
				);
			}
		} else {
			$resultRedirect->setPath('customer/index');
		}
		return $resultRedirect;
	}

	/**
	 * Get email notification
	 *
	 * @return EmailNotificationInterface
	 * @deprecated
	 */
	private function getEmailNotification()
	{
		if (!($this->emailNotification instanceof EmailNotificationInterface)) {
			return \Magento\Framework\App\ObjectManager::getInstance()->get(
				EmailNotificationInterface::class
			);
		} else {
			return $this->emailNotification;
		}
	}

	/**
	 * Retrieve current customer ID
	 *
	 * @return int
	 */
	private function getCurrentCustomerId()
	{
		$originalRequestData = $this->getRequest()->getPostValue(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);

		$customerId = isset($originalRequestData['entity_id'])
			? $originalRequestData['entity_id']
			: null;

		return $customerId;
	}

}