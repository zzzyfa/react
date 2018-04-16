<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 20/02/2018
 * Time: 3:05 PM
 */

namespace Althea\Quote\Plugin\Model\Cart\Totals;

class ItemConverterPlugin {

	protected $_extensionAttributesFactory;

	/**
	 * ItemConverterPlugin constructor.
	 *
	 * @param \Magento\Quote\Api\Data\TotalsItemExtensionFactory $extensionAttributesFactory
	 */
	public function __construct(\Magento\Quote\Api\Data\TotalsItemExtensionFactory $extensionAttributesFactory)
	{
		$this->_extensionAttributesFactory = $extensionAttributesFactory;
	}

	public function aroundModelToDataObject(\Magento\Quote\Model\Cart\Totals\ItemConverter $subject, \Closure $proceed, \Magento\Quote\Model\Quote\Item $item)
	{
		/* @var \Magento\Quote\Api\Data\TotalsItemInterface $result */
		$result        = $proceed($item);
		$extAttributes = $result->getExtensionAttributes();

		if (!$extAttributes) {

			$extAttributes = $this->_extensionAttributesFactory->create();
		}

		$extAttributes->setProductId($item->getProduct()->getId());
		$result->setExtensionAttributes($extAttributes);

		return $result;
	}

}