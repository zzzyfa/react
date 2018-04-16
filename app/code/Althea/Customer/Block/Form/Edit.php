<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 12/03/2018
 * Time: 12:59 PM
 */

namespace Althea\Customer\Block\Form;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Magento\Customer\Block\Form\Edit {

	protected $_customerRegistry;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Customer\Model\CustomerRegistry $customerRegistry,
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
		CustomerRepositoryInterface $customerRepository,
		AccountManagementInterface $customerAccountManagement,
		array $data = []
	)
	{
		$this->_customerRegistry = $customerRegistry;

		parent::__construct($context, $customerSession, $subscriberFactory, $customerRepository, $customerAccountManagement, $data);
	}

	public function customerHasPassword()
	{
		try {

			$customerSecure = $this->_customerRegistry->retrieveSecureData($this->customerSession->getCustomerId());
			$hasPassword    = ($hash = $customerSecure->getPasswordHash()) ? $hash : false;
		} catch (NoSuchEntityException $e) {

			$hasPassword = false;
		}

		return $hasPassword;
	}

}