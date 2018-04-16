<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/12/2017
 * Time: 10:03 AM
 */

namespace Althea\Freeshippinglabel\Plugin;

use Aheadworks\Freeshippinglabel\Model\Source\ContentType;

class Label {

	const RESTRICT_FREE_SHIPPING = 'restrict_free_shipping';

	protected $_checkoutSession;
	protected $_priceCurrency;
	protected $_storeResolver;

	/**
	 * Label constructor.
	 *
	 * @param \Magento\Checkout\Model\Session                   $checkoutSession
	 * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
	 * @param \Magento\Store\Api\StoreResolverInterface         $storeResolver
	 */
	public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\Magento\Store\Api\StoreResolverInterface $storeResolver
	)
	{
		$this->_checkoutSession = $checkoutSession;
		$this->_priceCurrency   = $priceCurrency;
		$this->_storeResolver   = $storeResolver;
	}

	public function aroundGetMessage(\Althea\Freeshippinglabel\Model\Label $subject, \Closure $proceed)
	{
		$freeshippingItemsTotal = 0;
		$messageType            = ContentType::EMPTY_CART;
		$goal                   = $this->_priceCurrency->convertAndRound($subject->getGoal());
		$leftToGoal             = $goal;
		$minItemQty             = $subject->getMinItemQty();
		$leftToMinItemQty       = $minItemQty;
		$quote                  = $this->_checkoutSession->getQuote();
		$itemsCount             = $quote->getItemsCount();

		foreach ($quote->getAllAddresses() as $address) {

			/* @var \Magento\Quote\Model\Quote\Address\Item $item */
			foreach ($address->getAllItems() as $item) {

				if ($this->_initItem($item) && $item->getQty() > 0) {

					$freeshippingItemsTotal += ($item->getRowTotalWithDiscount() > 0) ? $item->getRowTotalWithDiscount() : $item->getRowTotal(); // calculate non-freeshipping items total
					$itemsCount             -= $item->getQty();
				}
			}
		}

		if ($quote && $quote->getItemsCount()) {

			$subTotalExcludeFs = $quote->getSubtotalWithDiscount() - $freeshippingItemsTotal; // use subtotal w/ discount to exclude shipping fee
			$leftToGoal        = $goal - $subTotalExcludeFs;
			$leftToMinItemQty  = $minItemQty - $itemsCount;

			if ($goal <= $subTotalExcludeFs && $minItemQty <= $itemsCount) {

				$messageType = ContentType::GOAL_REACHED;
			} else {

				$messageType = ContentType::NOT_EMPTY_CART;
			}
		}

		$storeId         = $this->_storeResolver->getCurrentStoreId();
		$messageTemplate = $subject->getResource()->getMessageTemplate($subject->getId(), $messageType, $storeId);
		$variables       = [
			'ruleGoal'           => $goal,
			'ruleGoalLeft'       => $leftToGoal,
			'ruleMinItemQty'     => $minItemQty,
			'ruleMinItemQtyLeft' => $leftToMinItemQty,
		];

		return $this->_processVars($messageTemplate, $variables);
	}

	/**
	 * Process message variables
	 *
	 * @param string $messageTemplate
	 * @param array  $variables
	 *
	 * @return string
	 */
	protected function _processVars($messageTemplate, $variables)
	{
		$processedMessage = $messageTemplate;
		$currencySign     = $this->_priceCurrency->getCurrencySymbol();

		foreach ($variables as $varName => $value) {

			if (in_array($varName, ['ruleGoal', 'ruleGoalLeft'])) {

				$value = $currencySign . $value;
			}

			$processedMessage = str_replace(
				'{{' . $varName . '}}',
				'<span class="goal">' . $value . '</span>',
				$processedMessage
			);
		}

		return $processedMessage;
	}

	/**
	 * Address item initialization
	 *
	 * @param \Magento\Quote\Model\Quote\Address\Item|\Magento\Quote\Model\Quote\Item $item
	 *
	 * @return bool
	 */
	protected function _initItem($item)
	{
		if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {

			$quoteItem = $item->getAddress()
			                  ->getQuote()
			                  ->getItemById($item->getQuoteItemId());
		} else {

			$quoteItem = $item;
		}

		$product = $quoteItem->getProduct();

		$product->setCustomerGroupId($quoteItem->getQuote()->getCustomerGroupId());

		/**
		 * Quote super mode flag mean what we work with quote without restriction
		 */
		if ($item->getQuote()->getIsSuperMode() && !$product) {

			return false;
		} else if (!$product
			|| !$product->isVisibleInCatalog()
			|| !$product->getResource()->getAttributeRawValue($product->getId(), self::RESTRICT_FREE_SHIPPING, $product->getStoreId()) // althea: skip non-freeshipping item
		) {

			return false;
		}

		$quoteItem->setConvertedPrice(null);

		$originalPrice = $product->getPrice();

		if ($quoteItem->getParentItem() && $quoteItem->isChildrenCalculated()) {

			$finalPrice = $quoteItem->getParentItem()
			                        ->getProduct()
			                        ->getPriceModel()
			                        ->getChildFinalPrice(
				                        $quoteItem->getParentItem()->getProduct(),
				                        $quoteItem->getParentItem()->getQty(),
				                        $product,
				                        $quoteItem->getQty()
			                        );

			$this->_calculateRowTotal($item, $finalPrice, $originalPrice);
		} elseif (!$quoteItem->getParentItem()) {

			$finalPrice = $product->getFinalPrice($quoteItem->getQty());

			$this->_calculateRowTotal($item, $finalPrice, $originalPrice);
		}

		return true;
	}

	/**
	 * Processing calculation of row price for address item
	 *
	 * @param \Magento\Quote\Model\Quote\Address\Item|\Magento\Quote\Model\Quote\Item $item
	 * @param int                                                                     $finalPrice
	 * @param int                                                                     $originalPrice
	 * @return $this
	 */
	protected function _calculateRowTotal($item, $finalPrice, $originalPrice)
	{
		if (!$originalPrice) {

			$originalPrice = $finalPrice;
		}

		$item->setPrice($finalPrice)
		     ->setBaseOriginalPrice($originalPrice);
		$item->calcRowTotal();

		return $this;
	}

}