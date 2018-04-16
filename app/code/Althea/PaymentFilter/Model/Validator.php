<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 19/09/2017
 * Time: 11:26 AM
 */

namespace Althea\PaymentFilter\Model;

use Magento\Checkout\Model\Session;

class Validator {

	protected $_checkoutSession;
	protected $_customerSession;
	protected $_ruleFactory;
	protected $_quoteCollectionFactory;
	protected $_appliedRules = null;

	/**
	 * Validator constructor.
	 *
	 * @param Session                                                    $checkoutSession
	 * @param \Magento\Customer\Model\Session                            $customerSession
	 * @param RuleFactory                                                $ruleFactory
	 * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
	 */
	public function __construct(
		Session $checkoutSession,
		\Magento\Customer\Model\Session $customerSession,
		RuleFactory $ruleFactory,
		\Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
	)
	{
		$this->_checkoutSession        = $checkoutSession;
		$this->_customerSession        = $customerSession;
		$this->_ruleFactory            = $ruleFactory;
		$this->_quoteCollectionFactory = $collectionFactory;
	}

	public function getAppliedRules(\Magento\Quote\Api\Data\CartInterface $quote = null)
	{
		if ($this->_appliedRules != null) {

			return $this->_appliedRules;
		}

		// althea:
		// - checkout session was not initialized manually for API
		// - use quote from observer for API
		// - getting quote from checkout session will cause infinite loop due to Amasty Shipping Table Rates
		if (!$quote) {

			$quote = $this->_quoteCollectionFactory->create()
			                                       ->addFieldToFilter('entity_id', ['eq' => $this->_checkoutSession->getQuoteId()])
			                                       ->getFirstItem();
		}

		/* @var \Althea\PaymentFilter\Model\ResourceModel\Rule\Collection $rules */
		$rules = $this->_ruleFactory->create()
		                            ->getCollection()
		                            ->addFieldToFilter('status', ['eq' => Rule::STATUS_ENABLED]);

		$rules->getSelect()
		      ->order('priority ASC');

		$appliedRules = array();

		foreach ($rules as $rule) {

			$currentStoreId = $quote->getStoreId();

			if ($rule->getStores() && !in_array($currentStoreId, $rule->getStores())) {

				continue;
			}

			$address  = $quote->getShippingAddress();
			$customer = $this->_customerSession->getCustomer();

			if ($customer->getId()) {

				$address->setData('customer_group', $customer->getGroupId());
			} else {

				$address->setData('customer_group', 0);
			}

			if ($rule->validate($address)) {

				$appliedRules[] = $rule;
			}

			if ($rule->getStopRulesProcessing()) {

				break;
			}
		}

		if (count($appliedRules)) {

			$this->_appliedRules = $appliedRules;
		} else {

			$this->_appliedRules = false;
		}

		return $this->_appliedRules;
	}

}