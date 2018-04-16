<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 19/09/2017
 * Time: 11:44 AM
 */

namespace Althea\PaymentFilter\Model\Rate;

use Althea\PaymentFilter\Model\ResourceModel\Rule\Collection;
use Althea\PaymentFilter\Model\Validator;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class Result extends \Magento\Shipping\Model\Rate\Result {

	protected $_validator;

	/**
	 * @inheritDoc
	 */
	public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, Validator $validator)
	{
		$this->_validator = $validator;

		parent::__construct($storeManager);
	}

	/**
	 * @inheritDoc
	 */
	public function append($result)
	{
		/* @var Collection $rules */
		$rules = $this->_validator->getAppliedRules();

		if (!$rules) {

			return parent::append($result);
		}

		if ($rules != false) {

			$disabledMethods = array();

			foreach ($rules as $rule) {

				$disabledMethods = array_merge($disabledMethods, $rule->getShippingMethods());
			}

			if (!$result instanceof \Magento\Shipping\Model\Rate\Result) {

				return parent::append($result);
			}

			/* @var Method[] $rates */
			$rates = $result->getAllRates();

			foreach ($rates as $rate) {

				$method = $rate ? $rate->getCarrier() . '_' . $rate->getMethod() : NULL;

				if (!in_array($method, $disabledMethods)) {

					$this->append($rate);
				}
			}
		}

		return $this;
	}

}