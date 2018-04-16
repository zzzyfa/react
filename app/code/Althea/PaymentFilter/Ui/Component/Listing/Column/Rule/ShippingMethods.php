<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/09/2017
 * Time: 2:39 PM
 */

namespace Althea\PaymentFilter\Ui\Component\Listing\Column\Rule;

use Althea\PaymentFilter\Helper\Data;
use Magento\Framework\Data\OptionSourceInterface;

class ShippingMethods implements OptionSourceInterface {

	protected $_helper;

	/**
	 * ShippingMethods constructor.
	 */
	public function __construct(Data $helper)
	{
		$this->_helper = $helper;
	}

	/**
	 * @inheritDoc
	 */
	public function toOptionArray()
	{
		return $this->_helper->getAllShippingOptions();
	}

}