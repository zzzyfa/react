<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 18/07/2017
 * Time: 12:22 PM
 */

namespace Althea\SalesRule\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\SalesRule\Model\RuleFactory;
use Magento\SalesRule\Model\Rule\CustomerFactory;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\ResourceModel\Coupon\Usage;

class SalesOrderAfterCancelObserver implements ObserverInterface {

	protected $ruleFactory;
	protected $ruleCustomerFactory;
	protected $coupon;
	protected $couponUsage;

	/**
	 * SalesOrderAfterCancelObserver constructor.
	 *
	 * @param $ruleFactory
	 * @param $ruleCustomerFactory
	 * @param $coupon
	 * @param $couponUsage
	 */
	public function __construct(
		RuleFactory $ruleFactory,
		CustomerFactory $ruleCustomerFactory,
		Coupon $coupon,
		Usage $couponUsage
	)
	{
		$this->ruleFactory         = $ruleFactory;
		$this->ruleCustomerFactory = $ruleCustomerFactory;
		$this->coupon              = $coupon;
		$this->couponUsage         = $couponUsage;
	}

	/**
	 * Revert process done in Magento/SalesRule/Observer/SalesOrderAfterPlaceObserver.php
	 *
	 * @param Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();

		if (!$order || $order->getDiscountAmount() == 0) {

			return $this;
		}

		// lookup rule ids
		$ruleIds      = explode(',', $order->getAppliedRuleIds());
		$ruleIds      = array_unique($ruleIds);
		$ruleCustomer = null;
		$customerId   = $order->getCustomerId();

		// use each rule (and apply to customer, if applicable)
		foreach ($ruleIds as $ruleId) {

			if (!$ruleId) {

				continue;
			}

			/** @var \Magento\SalesRule\Model\Rule $rule */
			$rule = $this->ruleFactory->create();

			$rule->load($ruleId);

			if ($rule->getId()) {

				$rule->loadCouponCode();
				$rule->setTimesUsed($rule->getTimesUsed() - 1);
				$rule->save();

				if ($customerId) {

					/** @var \Magento\SalesRule\Model\Rule\Customer $ruleCustomer */
					$ruleCustomer = $this->ruleCustomerFactory->create();

					$ruleCustomer->loadByCustomerRule($customerId, $ruleId);

					if ($ruleCustomer->getId()) {

						$ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed() - 1);
						$ruleCustomer->save();
					}
				}
			}
		}

		$this->coupon->loadByCode($order->getCouponCode());

		if ($this->coupon->getId()) {

			$this->coupon->setTimesUsed($this->coupon->getTimesUsed() - 1);
			$this->coupon->save();

			if ($customerId) {

				$this->couponUsage->updateCustomerCouponTimesUsed($customerId, $this->coupon->getId(), true);
			}
		}

		return $this;
	}

}