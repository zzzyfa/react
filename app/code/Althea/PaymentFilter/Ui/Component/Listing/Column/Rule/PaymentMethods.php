<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/09/2017
 * Time: 12:25 PM
 */

namespace Althea\PaymentFilter\Ui\Component\Listing\Column\Rule;

use Althea\PaymentFilter\Helper\Data;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class PaymentMethods implements OptionSourceInterface {

	protected $_helper;
	protected $_storeManager;

	/**
	 * PaymentMethods constructor.
	 *
	 * @param $_helper
	 */
	public function __construct(Data $helper, StoreManagerInterface $storeManager)
	{
		$this->_helper       = $helper;
		$this->_storeManager = $storeManager;
	}

	/**
	 * @inheritDoc
	 */
	public function toOptionArray()
	{
		$stores  = $this->_storeManager->getStores();
		$methods = array();

		foreach ($stores as $storeId => $storeItem) {

			$methodsOfStore = $this->_helper->getStorePaymentMethods($storeId);

			foreach ($methodsOfStore as $mt) {

				if (!array_key_exists($mt->getCode(), $methods)) {

					$methods[$mt->getCode()] = $mt;
				}
			}
		}

		$methodValues = array();

		foreach ($methods as $method) {

			try {

				$methodValues[] = array(
					'label' => __($method->getTitle()),
					'value' => $method->getCode(),
				);

			} catch (\Exception $ex) {

			}
		}

		return $methodValues;
	}

}