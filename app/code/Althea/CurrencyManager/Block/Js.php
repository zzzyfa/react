<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/08/2017
 * Time: 6:06 PM
 */

namespace Althea\CurrencyManager\Block;

use Althea\CurrencyManager\Helper\Data;
use Magento\Framework\View\Element\Template;

class Js extends Template {

	protected $_helper;
	protected $_jsonHelper;

	public function __construct(
		Data $helper,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		Template\Context $context,
		array $data = []
	)
	{
		$this->_helper     = $helper;
		$this->_jsonHelper = $jsonHelper;

		parent::__construct($context, $data);
	}

	public function getJsonConfig()
	{
		if (method_exists(\Magento\Framework\Json\Helper\Data::class, 'jsonEncode')) {

			return $this->_jsonHelper->jsonEncode(
				$this->_helper->getOptions(array(), false, $this->_storeManager->getStore()->getCurrentCurrencyCode()
				));
		} else {

			return \Zend_Json::encode(
				$this->_helper->getOptions(array(), false, $this->_storeManager->getStore()->getCurrentCurrencyCode()
				));
		}
	}

}