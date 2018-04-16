<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/07/2017
 * Time: 2:30 PM
 */

namespace Althea\CouponMessage\Observer;

use Althea\CouponMessage\Helper\Config;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Model\Quote;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\RuleFactory;
use Magento\SalesRule\Model\Rule\CustomerFactory;
use Magento\SalesRule\Model\Utility;

class ApplyCouponObserver implements ObserverInterface {

	protected $helperConfig;
	protected $couponFactory;
	protected $ruleFactory;
	protected $customerFactory;
	protected $dateTime;
	protected $validatorUtility;

	protected function _initRule($couponCode)
	{
		$coupon = $this->couponFactory->create();
		$rule   = $this->ruleFactory->create();

		$coupon->load($couponCode, 'code');

		return $rule->load($coupon->getRuleId());
	}

	protected function _isExpired(Rule $rule)
	{
		$expired = false;

		if (!$rule->getToDate()) {

			return $expired;
		}

		$now    = new \DateTime($this->dateTime->date('Y-m-d H:i:s'));
		$toDate = new \DateTime($this->dateTime->date('Y-m-d 23:59:59', $rule->getToDate()));

		if ($now > $toDate) {

			$expired = true;
		}

		return $expired;
	}

	public function _isValidCustomerGroup(Rule $rule, $customer)
	{
		/* @var \Magento\Customer\Model\Data\Customer $customer */
		$valid            = false;
		$custGroupId      = $customer->getGroupId();
		$ruleCustGroupIds = $rule->getCustomerGroupIds();

		if (in_array($custGroupId, $ruleCustGroupIds)) {

			$valid = true;
		}

		return $valid;
	}

	protected function _isOverlimit(Rule $rule, $couponCode)
	{
		$overlimit     = false;
		$couponModel   = $this->couponFactory->create();
		$coupon        = $couponModel->getCollection()
		                             ->addRuleToFilter($rule)
		                             ->addFieldToFilter('code', ['eq' => $couponCode])
		                             ->getFirstItem();
		$usesPerCoupon = intval($coupon->getUsageLimit());
		$timesUsed     = intval($coupon->getTimesUsed());

		if ($usesPerCoupon > 0 && $timesUsed >= $usesPerCoupon) { // ensure uses per coupon is not 0

			$overlimit = true;
		}

		return $overlimit;
	}

	protected function _isOverlimitCustomer(Rule $rule, $customer)
	{
		/* @var \Magento\Customer\Model\Data\Customer $customer */
		$overlimit       = false;
		$usesPerCustomer = intval($rule->getUsesPerCustomer());
		$history         = $this->customerFactory->create();
		$timesUsed       = intval($history->getCollection()
		                                  ->addFieldToFilter('rule_id', ['eq' => $rule->getId()])
		                                  ->addFieldToFilter('customer_id', ['eq' => $customer->getId()])
		                                  ->getFirstItem()
		                                  ->getTimesUsed());

		if ($usesPerCustomer > 0 && $timesUsed >= $usesPerCustomer) { // ensure uses per customer is not 0

			$overlimit = true;
		}

		return $overlimit;
	}

	public function __construct(
		Config $helperConfig,
		CouponFactory $couponFactory,
		RuleFactory $ruleFactory,
		CustomerFactory $customerFactory,
		DateTime $dateTime,
		Utility $validatorUtility
	)
	{
		$this->helperConfig     = $helperConfig;
		$this->couponFactory    = $couponFactory;
		$this->ruleFactory      = $ruleFactory;
		$this->customerFactory  = $customerFactory;
		$this->dateTime         = $dateTime;
		$this->validatorUtility = $validatorUtility;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$couponCode = $observer->getData('coupon_code');
		$quote      = $observer->getData('quote');

		if (!$this->helperConfig->getGenerableEnable() // skip if module not enabled
			|| !$couponCode // skip if cancel coupon
			|| !$quote instanceof Quote // skip if invalid quote
			|| !$quote->getId()
			|| $couponCode == $quote->getCouponCode() // skip if coupon code is applied
		) {

			return false;
		}

		$rule    = $this->_initRule($couponCode);
		$address = ($quote->getIsVirtual()) ? $quote->getBillingAddress() : $quote->getShippingAddress();
		$msg     = $this->helperConfig->getMsgDefault($couponCode);

		if (!$rule instanceof Rule || !$rule->getId()) { // check coupon code rule

			$msg = $this->helperConfig->getMsgNotExist($couponCode);
		} else if ($this->_isExpired($rule)) { // check expiry

			$msg = $this->helperConfig->getMsgExpired($couponCode);
		} else if (!$rule->getIsActive()) { // check rule status

			$msg = $this->helperConfig->getMsgDefault($couponCode);
		} else if (!$this->_isValidCustomerGroup($rule, $quote->getCustomer())) { // check customer group

			$msg = $this->helperConfig->getMsgInvalidCustGroup($couponCode);
		} else if ($this->_isOverlimit($rule, $couponCode)) { // check coupon uses over limit

			$msg = $this->helperConfig->getMsgOverlimit($couponCode);
		} else if ($this->_isOverlimitCustomer($rule, $quote->getCustomer())) { // check customer coupon uses over limit

			$msg = $this->helperConfig->getMsgOverlimitCustGroup($couponCode);
		} else if (!$this->validatorUtility->canProcessRule($rule, $address)) { // check cart rule

			$msg = $this->helperConfig->getMsgInvalidRule($couponCode);
		} else { // check product rule

			$count     = 0;
			$itemCount = $quote->getItemsCount();

			foreach ($quote->getAllVisibleItems() as $item) {

				if (!$rule->getActions()->validate($item)) {

					$count++;
				}
			}

			if ($count == $itemCount) {

				$msg = $this->helperConfig->getMsgInvalidRule($couponCode);
			}
		}

		$quote->setData('coupon_error_message', $msg);
	}

}