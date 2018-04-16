<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 23/08/2017
 * Time: 6:16 PM
 */

namespace Althea\CurrencyManager\Block\Adminhtml\Product\Edit\Tab\Options;

use Althea\CurrencyManager\Helper\Data;
use Magento\Catalog\Model\Product;

class Option extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option {

	protected $_helper;

	public function __construct(
		Data $helper,
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Config\Model\Config\Source\Yesno $configYesNo,
		\Magento\Catalog\Model\Config\Source\Product\Options\Type $optionType,
		Product $product,
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig,
		array $data = []
	)
	{
		$this->_helper = $helper;

		parent::__construct($context, $configYesNo, $optionType, $product, $registry, $productOptionConfig, $data);
	}


	/**
	 * @param float  $value
	 * @param string $type
	 * @return string
	 */
	public function getPriceValue($value, $type)
	{
		$options = $this->_helper->getOptions([]);

		if (isset($options["input_admin"]) && isset($options['precision'])) {

			if ($type == 'percent') {

				return number_format($value, $options['precision'], null, '');
			} elseif ($type == 'fixed') {

				return number_format($value, $options['precision'], null, '');
			}
		}
	}

}