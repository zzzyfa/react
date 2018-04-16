<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 19/09/2017
 * Time: 11:58 AM
 */

namespace Althea\PaymentFilter\Observer;

use Althea\PaymentFilter\Helper\Config;
use Althea\PaymentFilter\Model\ResourceModel\Rule\Collection;
use Althea\PaymentFilter\Model\Validator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class PaymentMethodIsActiveObserver implements ObserverInterface {

	protected $_configHelper;
	protected $_validator;

	/**
	 * PaymentMethodIsActiveObserver constructor.
	 *
	 * @param $_configHelper
	 */
	public function __construct(Config $configHelper, Validator $validator)
	{
		$this->_configHelper = $configHelper;
		$this->_validator    = $validator;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if (!$this->_configHelper->getGeneralEnabled()) {

			return;
		}

		$checkResult = $observer->getEvent()->getResult();
		$quote       = $observer->getEvent()->getQuote();
		/* @var AbstractMethod $method */
		$method          = $observer->getEvent()->getMethodInstance();
		$disabledMethods = array();

		if ($checkResult->getData('is_available')) {

			/* @var Collection $rules */
			$rules = $this->_validator->getAppliedRules($quote);

			if ($rules) {

				foreach ($rules as $rule) {

					$disabledMethods = array_merge($disabledMethods, $rule->getPaymentMethods());
				}

				if (in_array($method->getCode(), $disabledMethods)) {

					$checkResult->setData('is_available', false);
					$method->setData('is_filtered', true); // althea: set custom flag to show method has been filtered by module
				}
			}
		}
	}

}