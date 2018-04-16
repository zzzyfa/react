<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/12/2017
 * Time: 10:09 AM
 */

namespace Althea\Customer\Model\ResourceModel;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ImageProcessorInterface;

class CustomerRepository extends \Magento\Customer\Model\ResourceModel\CustomerRepository {

	protected $_typeFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		TypeFactory $typeFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Model\Data\CustomerSecureFactory $customerSecureFactory,
		\Magento\Customer\Model\CustomerRegistry $customerRegistry,
		\Althea\Customer\Model\ResourceModel\AddressRepository $addressRepository, // althea: set explicitly
		\Magento\Customer\Model\ResourceModel\Customer $customerResourceModel,
		CustomerMetadataInterface $customerMetadata,
		\Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
		DataObjectHelper $dataObjectHelper,
		ImageProcessorInterface $imageProcessor,
		\Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
	)
	{
		$this->_typeFactory = $typeFactory;

		parent::__construct($customerFactory, $customerSecureFactory, $customerRegistry, $addressRepository, $customerResourceModel, $customerMetadata, $searchResultsFactory, $eventManager, $storeManager, $extensibleDataObjectConverter, $dataObjectHelper, $imageProcessor, $extensionAttributesJoinProcessor);
	}

	/**
	 * @inheritDoc
	 */
	public function save(\Magento\Customer\Api\Data\CustomerInterface $customer, $passwordHash = null)
	{
		$prevCustomerData = null;
		if ($customer->getId()) {
			$prevCustomerData = $this->getById($customer->getId());
		}
		$customer = $this->imageProcessor->save(
			$customer,
			CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
			$prevCustomerData
		);

		$origAddresses = $customer->getAddresses();
		$customer->setAddresses([]);
		$customerData = $this->extensibleDataObjectConverter->toNestedArray(
			$customer,
			[],
			'\Magento\Customer\Api\Data\CustomerInterface'
		);

		$customer->setAddresses($origAddresses);
		$customerModel = $this->customerFactory->create(['data' => $customerData]);
		$storeId = $customerModel->getStoreId();
		if ($storeId === null) {
			$customerModel->setStoreId($this->storeManager->getStore()->getId());
		}
		$customerModel->setId($customer->getId());

		// Need to use attribute set or future updates can cause data loss
		if (!$customerModel->getAttributeSetId()) {

			// althea: do not hard code attribute set, otherwise custom attribute cannot be saved
			//
			// https://github.com/magento/magento2/issues/4677
			//
//			$customerModel->setAttributeSetId(
//				\Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER
//			);

			// althea: get default attribute set ID from entity type instead
			$entityType = $this->_typeFactory->create()->loadByCode(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);

			$customerModel->setAttributeSetId($entityType->getDefaultAttributeSetId());
		}

		// Populate model with secure data
		if ($customer->getId()) {
			$customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
			$customerModel->setRpToken($customerSecure->getRpToken());
			$customerModel->setRpTokenCreatedAt($customerSecure->getRpTokenCreatedAt());
			$customerModel->setPasswordHash($customerSecure->getPasswordHash());
			$customerModel->setFailuresNum($customerSecure->getFailuresNum());
			$customerModel->setFirstFailure($customerSecure->getFirstFailure());
			$customerModel->setLockExpires($customerSecure->getLockExpires());
		} else {
			if ($passwordHash) {
				$customerModel->setPasswordHash($passwordHash);
			}
		}

		// If customer email was changed, reset RpToken info
		if ($prevCustomerData
			&& $prevCustomerData->getEmail() !== $customerModel->getEmail()
		) {
			$customerModel->setRpToken(null);
			$customerModel->setRpTokenCreatedAt(null);
		}
		$customerModel->save();
		$this->customerRegistry->push($customerModel);
		$customerId = $customerModel->getId();

		if ($customer->getAddresses() !== null) {
			if ($customer->getId()) {
				$existingAddresses = $this->getById($customer->getId())->getAddresses();
				$getIdFunc = function ($address) {
					return $address->getId();
				};
				$existingAddressIds = array_map($getIdFunc, $existingAddresses);
			} else {
				$existingAddressIds = [];
			}

			$savedAddressIds = [];
			foreach ($customer->getAddresses() as $address) {

				// althea: update address based on is_update flag
				/* @var \Magento\Customer\Api\Data\AddressInterface $address */
				/* @var \Magento\Customer\Api\Data\AddressExtensionInterface $extAttr */
				$extAttr = $address->getExtensionAttributes();

				if ($extAttr && $extAttr->getIsUpdate()) {

					$address->setCustomerId($customerId)
					        ->setRegion($address->getRegion());
					$this->addressRepository->save($address);

					if ($address->getId()) {
						$savedAddressIds[] = $address->getId();
					}
				}
			}

			// althea: delete address based on is_delete flag
//			$addressIdsToDelete = array_diff($existingAddressIds, $savedAddressIds);
			$addressIdsToDelete = [];
			foreach ($customer->getAddresses() as $address) {

				if (in_array($address->getId(), $savedAddressIds)) {

					continue;
				}

				/* @var \Magento\Customer\Api\Data\AddressExtensionInterface $extAttr */
				$extAttr = $address->getExtensionAttributes();

				if ($extAttr && $extAttr->getIsDelete()) {

					$addressIdsToDelete[] = $address->getId();
				}
			}

			foreach ($addressIdsToDelete as $addressId) {
				$this->addressRepository->deleteById($addressId);
			}
		}

		$savedCustomer = $this->get($customer->getEmail(), $customer->getWebsiteId());
		$this->eventManager->dispatch(
			'customer_save_after_data_object',
			['customer_data_object' => $savedCustomer, 'orig_customer_data_object' => $customer]
		);
		return $savedCustomer;
	}
}