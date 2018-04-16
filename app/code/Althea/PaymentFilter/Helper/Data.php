<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/09/2017
 * Time: 12:17 PM
 */

namespace Althea\PaymentFilter\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Payment\Model\Method\Adapter;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper {

	protected $_configHelper;
	protected $_paymentHelper;
	protected $_storeManager;
	protected $_shippingConfig;
	protected $_shippingOptions;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Context $context,
		Config $configHelper,
		\Magento\Payment\Helper\Data $paymentHelper,
		StoreManagerInterface $storeManager,
		\Magento\Shipping\Model\Config $config
	)
	{
		$this->_configHelper   = $configHelper;
		$this->_paymentHelper  = $paymentHelper;
		$this->_storeManager   = $storeManager;
		$this->_shippingConfig = $config;

		parent::__construct($context);
	}

	public function getStorePaymentMethods($store = null, $quote = null)
	{
		$res = array();

		foreach ($this->getPaymentMethods($store) as $code => $methodConfig) {

			try {

				if (!$this->scopeConfig->isSetFlag(sprintf('payment/%s/active', $code), ScopeInterface::SCOPE_STORE, $store)) {

					continue;
				}

				$prefix = sprintf('%s/%s/', Config::XML_PATH_PAYMENT_METHODS, $code);

				if (!$model = $this->scopeConfig->getValue(sprintf('%smodel', $prefix), ScopeInterface::SCOPE_STORE, $store)) {

					continue;
				}

				$methodInstance = $this->_paymentHelper->getMethodInstance($code);

				if ($methodInstance instanceof Adapter) {

					continue;
				}

				$methodInstance->setStore($store);

				if ($quote) {

					if (!$methodInstance->isAvailable($quote)) {

						/* if the payment method cannot be used at this time */
						continue;
					}
				}

				$sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);

				$methodInstance->setSortOrder($sortOrder);

				$res[] = $methodInstance;
			} catch (\Exception $ex) {

			}
		}

		return $res;
	}

	public function getPaymentMethods($store = null)
	{
		if ($store == null) {

			$stores  = $this->_storeManager->getStores();
			$methods = array();

			foreach ($stores as $storeId => $storeItem) {

				$methods = array_merge($methods, $this->_configHelper->getPaymentMethods(), $storeId);
			}

			return $methods;
		} else {

			return $this->_configHelper->getPaymentMethods($store);
		}
	}

	public function getAllShippingOptions()
	{
		if ($this->_shippingOptions == null) {

			$shippingMethodOptions = array();
			$shippingMethods       = $this->_shippingConfig->getAllCarriers();

			uasort($shippingMethods, array($this, 'sortShippingMethods'));

			foreach ($shippingMethods as $code => $shippingMethod) {

				if (!$title = $this->scopeConfig->getValue("carriers/{$code}/title")) {

					$title = $code;
				}

				try {

					if ($childMethods = $shippingMethod->getAllowedMethods()) {

						foreach ($childMethods as $childCode => $childMethod) {

							$shippingMethodOptions[] = array(
								'value' => $code . '_' . $childCode,
								'label' => __(sprintf("[%s] - %s", $title, $childMethod)),
							);
						}
					} else {

						$value                   = $code;
						$shippingMethodOptions[] = array(
							'value' => $value,
							'label' => __($title),
						);
					}
				} catch (\Exception $ex) {

				}
			}

			$this->_shippingOptions = $shippingMethodOptions;
		}

		return $this->_shippingOptions;
	}

	public function sortShippingMethods($a, $b)
	{
		$aCode  = $a->getId();
		$bCode  = $b->getId();
		$aAtice = (int)$this->scopeConfig->getValue("carriers/{$aCode}/active");
		$bAtice = (int)$this->scopeConfig->getValue("carriers/{$bCode}/active");

		return $aAtice <= $bAtice;
	}

}