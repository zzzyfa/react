<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 23/08/2017
 * Time: 6:21 PM
 */

namespace Althea\CurrencyManager\Block\Adminhtml\Product\Helper\Form;

use Althea\CurrencyManager\Helper\Data;

class Price extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price {

	protected $_helper;

	public function __construct(
		Data $helper,
		\Magento\Framework\Data\Form\Element\Factory $factoryElement,
		\Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
		\Magento\Framework\Escaper $escaper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Locale\CurrencyInterface $localeCurrency,
		\Magento\Tax\Helper\Data $taxData,
		array $data = []
	)
	{
		$this->_helper = $helper;

		parent::__construct($factoryElement, $factoryCollection, $escaper, $storeManager, $localeCurrency, $taxData, $data);
	}


	/**
	 * @param null|int|string $index
	 * @return null|string
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function getEscapedValue($index = null)
	{
		$options = $this->_helper->getOptions([]);
		$value   = $this->getValue();

		if (!is_numeric($value)) {

			return null;
		}

		if ($attribute = $this->getEntityAttribute()) {

			// honor the currency format of the store
			$store    = $this->getStore($attribute);
			$currency = $this->_localeCurrency->getCurrency($store->getBaseCurrencyCode());
			$value    = $currency->toCurrency($value, ['display' => \Magento\Framework\Currency::NO_SYMBOL]);
		} else if (isset($options["input_admin"]) && isset($options['precision'])) {

			// default format:  1234.56
			$value = number_format($value, $options['precision'], null, '');
		}

		return $value;
	}

}