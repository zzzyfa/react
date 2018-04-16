<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 23/10/2017
 * Time: 5:05 PM
 */

namespace Althea\Customer\Model;


use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLogger;

class AccountManagement extends \Magento\Customer\Model\AccountManagement {

	protected $_tokenFactory;
	protected $_customerRegistry;
	protected $_customerRepository;
	protected $_customerSession;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		CustomerFactory $customerFactory,
		ManagerInterface $eventManager,
		StoreManagerInterface $storeManager,
		Random $mathRandom,
		Validator $validator,
		ValidationResultsInterfaceFactory $validationResultsDataFactory,
		AddressRepositoryInterface $addressRepository,
		CustomerMetadataInterface $customerMetadataService,
		CustomerRegistry $customerRegistry,
		PsrLogger $logger,
		Encryptor $encryptor,
		ConfigShare $configShare,
		StringHelper $stringHelper,
		CustomerRepositoryInterface $customerRepository,
		ScopeConfigInterface $scopeConfig,
		TransportBuilder $transportBuilder,
		DataObjectProcessor $dataProcessor,
		Registry $registry,
		CustomerViewHelper $customerViewHelper,
		DateTime $dateTime,
		CustomerModel $customerModel,
		ObjectFactory $objectFactory,
		ExtensibleDataObjectConverter $extensibleDataObjectConverter,
		TokenFactory $tokenFactory,
		Session $customerSession
	)
	{
		parent::__construct($customerFactory, $eventManager, $storeManager, $mathRandom, $validator, $validationResultsDataFactory, $addressRepository, $customerMetadataService, $customerRegistry, $logger, $encryptor, $configShare, $stringHelper, $customerRepository, $scopeConfig, $transportBuilder, $dataProcessor, $registry, $customerViewHelper, $dateTime, $customerModel, $objectFactory, $extensibleDataObjectConverter);

		$this->_tokenFactory       = $tokenFactory;
		$this->_customerRegistry   = $customerRegistry;
		$this->_customerRepository = $customerRepository;
		$this->_customerSession    = $customerSession;
	}

	/**
	 * Authenticate customer (register to customer session) via customer access token
	 *
	 * @param $customerToken
	 * @return \Magento\Customer\Model\Customer
	 * @throws AuthenticationException
	 */
	public function authenticateUserToken($customerToken)
	{
		$token    = $this->_tokenFactory->create()->loadByToken($customerToken);
		$userType = $token->getUserType();

		if ($userType != UserContextInterface::USER_TYPE_CUSTOMER) {

			throw new AuthenticationException(__('Incorrect user type.'));
		}

		$customerId = $token->getCustomerId();

		$this->_customerSession->loginById($customerId);

		$customer = $this->_customerSession->getCustomer();

		return $customer;
	}

	/**
	 * @inheritDoc
	 */
	public function changePassword($email, $currentPassword, $newPassword)
	{
		try {
			$customer = $this->_customerRepository->get($email);
		} catch (NoSuchEntityException $e) {
			throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
		}
		return $this->_altheaChangePasswordForCustomer($customer, $currentPassword, $newPassword);
	}

	/**
	 * @inheritDoc
	 */
	public function changePasswordById($customerId, $currentPassword, $newPassword)
	{
		try {
			$customer = $this->_customerRepository->getById($customerId);
		} catch (NoSuchEntityException $e) {
			throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
		}
		return $this->_altheaChangePasswordForCustomer($customer, $currentPassword, $newPassword, false);
	}

	/**
	 * Change customer password
	 *
	 * @param      $customer
	 * @param      $currentPassword
	 * @param      $newPassword
	 * @param bool $showAuthError
	 * @return bool
	 * @throws InputException
	 * @throws InvalidEmailOrPasswordException
	 * @throws NoSuchEntityException
	 * @throws UserLockedException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @throws \Magento\Framework\Exception\State\InputMismatchException
	 */
	protected function _altheaChangePasswordForCustomer($customer, $currentPassword, $newPassword, $showAuthError = true)
	{
		try {

			/* @var \Magento\Customer\Api\Data\CustomerInterface $customer */
			$customerSecure = $this->_customerRegistry->retrieveSecureData($customer->getId());

			// althea:
			// - authenticate customer only if he / she has current password
			if ($customerSecure->getPasswordHash()) {

				if (!($this->authentication instanceof AuthenticationInterface)) {

					$this->authentication = \Magento\Framework\App\ObjectManager::getInstance()->get(
						\Magento\Customer\Model\AuthenticationInterface::class
					);
				}

				$this->authentication->authenticate($customer->getId(), $currentPassword);
			}
		} catch (InvalidEmailOrPasswordException $e) {

			// althea:
			// - avoid returning 401 error
			// - otherwise user will be logged out from mobile app
			if ($showAuthError) {

				throw new InvalidEmailOrPasswordException(__('The password doesn\'t match this account.'));
			}

			throw new InputException(__('The password doesn\'t match this account.'));
		}
		$customerSecure->setRpToken(null);
		$customerSecure->setRpTokenCreatedAt(null);
		$this->checkPasswordStrength($newPassword);
		$customerSecure->setPasswordHash($this->createPasswordHash($newPassword));
		$this->_customerRepository->save($customer);
		return true;
	}

}