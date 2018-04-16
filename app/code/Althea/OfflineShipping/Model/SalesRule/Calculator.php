<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/04/2018
 * Time: 5:32 PM
 */

namespace Althea\OfflineShipping\Model\SalesRule;

use \Magento\OfflineShipping\Model\SalesRule\Rule;

class Calculator extends \Magento\OfflineShipping\Model\SalesRule\Calculator {

	/**
	 * @inheritDoc
	 */
	public function processFreeShipping(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
	{
		$address = $item->getAddress();
		$item->setFreeShipping(false);
		// althea:
		// - fix free shipping not reset
		$address->setFreeShipping(false);

		foreach ($this->_getRules($address) as $rule) {
			/* @var $rule \Magento\SalesRule\Model\Rule */
			if (!$this->validatorUtility->canProcessRule($rule, $address)) {
				continue;
			}

			if (!$rule->getActions()->validate($item)) {
				continue;
			}

			switch ($rule->getSimpleFreeShipping()) {
				case Rule::FREE_SHIPPING_ITEM:
					$item->setFreeShipping($rule->getDiscountQty() ? $rule->getDiscountQty() : true);
					break;

				case Rule::FREE_SHIPPING_ADDRESS:
					$address->setFreeShipping(true);
					break;
			}
			if ($rule->getStopRulesProcessing()) {
				break;
			}
		}
		return $this;
	}

}