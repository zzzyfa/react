<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 15/02/2018
 * Time: 11:52 AM
 */

namespace Althea\Customer\Plugin\Model;

use Althea\Nexmo\Model\ResourceModel\Verification\CollectionFactory;
use Althea\Nexmo\Model\Verification;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomerPlugin {

	protected $_extensionAttributeFactory;
	protected $_verificationCollectionFactory;
	protected $_attributeRepository;
	protected $_customerRegistry;

	/**
	 * CustomerRepository constructor.
	 *
	 * @param \Magento\Customer\Api\Data\CustomerExtensionFactory              $extensionAttributeFactory
	 * @param \Althea\Nexmo\Model\ResourceModel\Verification\CollectionFactory $collectionFactory
	 * @param \Magento\Eav\Api\AttributeRepositoryInterface                    $attributeRepository
	 * @param \Magento\Customer\Model\CustomerRegistry                         $customerRegistry
	 */
	public function __construct(
		CustomerExtensionFactory $extensionAttributeFactory,
		CollectionFactory $collectionFactory,
		AttributeRepositoryInterface $attributeRepository,
		CustomerRegistry $customerRegistry
	)
	{
		$this->_extensionAttributeFactory     = $extensionAttributeFactory;
		$this->_verificationCollectionFactory = $collectionFactory;
		$this->_attributeRepository           = $attributeRepository;
		$this->_customerRegistry              = $customerRegistry;
	}

	public function aroundGetDataModel(\Magento\Customer\Model\Customer $subject, \Closure $proceed)
	{
		/* @var \Magento\Customer\Api\Data\CustomerInterface $result */
		$result = $proceed();
		// althea:
		// - check if customer has been verified
		$verified = false;
		/* @var Verification $verification */
		$verification = $this->_verificationCollectionFactory->create()
		                                                     ->addFieldToFilter('customer_id', ['eq' => $result->getId()])
		                                                     ->addFieldToFilter('status', ['eq' => Verification::STATUS_VERIFIED])
		                                                     ->getFirstItem();

		if ($verification->getId()) {

			$verified = true;
		}

		$extAttributes = $result->getExtensionAttributes();

		if (!$extAttributes) {

			$extAttributes = $this->_extensionAttributeFactory->create();
		}

		$extAttributes->setIsAllowCheckout($verified);

		// althea:
		// - check if customer has password
		// - customer signed up via social login doesn't have password by default
		try {

			$customerSecure = $this->_customerRegistry->retrieveSecureData($result->getId());

			$extAttributes->setHasPassword($customerSecure->getPasswordHash() != null);
		} catch (NoSuchEntityException $e) {

			$extAttributes->setHasPassword(false);
		}

		$result->setExtensionAttributes($extAttributes);

		// set default value for custom attributes
		$customAttributes = [
			'customer_attribute_age',
			'customer_attribute_skinconcern',
			'customer_attribute_skintone',
			'customer_attribute_skintype',
		];

		array_map(function ($code) use ($result) {

			if ($result->getCustomAttribute($code)) {

				return;
			}

			// althea
			// - custom attribute cannot be NULL when saved
			$result->setCustomAttribute($code, "");

			try {

				$attribute = $this->_attributeRepository->get('customer', $code);

				if ($attribute->getFrontendInput() == \Magento\Framework\Setup\Option\MultiSelectConfigOption::FRONTEND_WIZARD_MULTISELECT) {

					$result->setCustomAttribute($code, []);
				}
			} catch (NoSuchEntityException $e) {
			}
		}, $customAttributes);

		return $result;
	}

}