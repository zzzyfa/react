<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 20/02/2018
 * Time: 2:20 PM
 */

namespace Althea\Quote\Plugin\Model;

class Quote {

	protected $_extensionAttributesFactory;
	protected $_orderCollectionFactory;

	/**
	 * Quote constructor.
	 *
	 * @param \Magento\Quote\Api\Data\CartItemExtensionFactory           $extensionAttributesFactory
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
	 */
	public function __construct(
		\Magento\Quote\Api\Data\CartItemExtensionFactory $extensionAttributesFactory,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
	)
	{
		$this->_extensionAttributesFactory = $extensionAttributesFactory;
		$this->_orderCollectionFactory     = $orderCollectionFactory;
	}

	public function aroundGetAllVisibleItems(\Magento\Quote\Model\Quote $subject, \Closure $proceed)
	{
		/* @var \Magento\Quote\Model\Quote\Item[] $result */
		$result = $proceed();

		foreach ($result as $key => $val) {

			$extAttributes = $val->getExtensionAttributes();

			if (!$extAttributes) {

				$extAttributes = $this->_extensionAttributesFactory->create();
			}

			$extAttributes->setHasError($val->getHasError() === true);
			$extAttributes->setErrorTexts($this->_getQuoteItemErrorTexts($val));
			$val->setExtensionAttributes($extAttributes);
		}

		return $result;
	}

	protected function _getQuoteItemErrorTexts(\Magento\Quote\Model\Quote\Item $item)
	{
		$errors = [];

		foreach ($item->getErrorInfos() as $key => $val) {

			if (!array_key_exists($val['code'], $errors)) {

				$errors[$val['code']] = $val['message'];
			}
		}

		return $errors;
	}

	/**
	 * Refresh reserved order ID if order ID has already been used
	 *
	 * @param \Magento\Quote\Model\Quote $subject
	 * @param \Closure                   $proceed
	 * @return \Magento\Quote\Model\Quote
	 */
	public function aroundReserveOrderId(\Magento\Quote\Model\Quote $subject, \Closure $proceed)
	{
		/* @var \Magento\Quote\Model\Quote $result */
		$result     = $proceed();
		$collection = $this->_orderCollectionFactory->create()
		                                            ->addFieldToFilter('increment_id', ['eq' => $result->getReservedOrderId()]);

		if ($collection->getSize() > 0) {

			$result->setReservedOrderId($result->getResource()->getReservedOrderId($result));
		}

		return $result;
	}

}