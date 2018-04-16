<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/04/2018
 * Time: 2:34 PM
 */

namespace Althea\Rules\Model\Rule\Action\Discount;

class SetofPercent extends \Amasty\Rules\Model\Rule\Action\Discount\SetofPercent {

	/**
	 * @inheritDoc
	 */
	protected function _calculate($rule, $item)
	{
		/** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
		$discountData = $this->discountFactory->create();
		$address = $item->getAddress();
		list ($qtySkus, $qtyCats, $sortedProducts) = $this->prepareSetRule($rule, $address);
		$discountArray = [];
		$discountedQtyByItem = [];
		$promoCats = $this->rulesDataHelper->getRuleCats($rule);
		$promoSku = $this->rulesDataHelper->getRuleSkus($rule);
		$minQty = $this->getMinQty($rule, $qtySkus, $qtyCats);
		if ($minQty < $rule->getDiscountStep()) {
			return $discountData;
		}
		$discountedQty = 0;
		foreach ($sortedProducts as $itemId => $allItem) {
			$itemQty = $this->getItemQty($allItem);
			if ($rule->getAmrulesRule()->getPromoSkus() && in_array($allItem->getSku(), $promoSku)) {
				$discountedQty = min($itemQty, $qtySkus[$allItem->getSku()]);
				$discountedQtyByItem[$itemId] = $discountedQty;
				$qtySkus[$allItem->getSku()] -= $discountedQty;
			}

			if ($rule->getAmrulesRule()->getPromoCats()
				&& array_intersect($allItem->getProduct()->getCategoryIds(), $promoCats)
			) {
				foreach (array_intersect($allItem->getProduct()->getCategoryIds(), $promoCats) as $category) {
					if (isset($qtyCats[$category])) {
						$discountedQty = min($itemQty, $qtyCats[$category]);
						$discountedQtyByItem[$itemId] = $discountedQty;
						$qtyCats[$category] -= $discountedQty;
					}
				}
			}

			// althea:
			// - skip non-applicable cart items
			if (empty($discountedQtyByItem[$itemId])) {

				continue;
			}

			$percent = min(100, $rule->getDiscountAmount());

			$itemPrice = $this->rulesProductHelper->getItemPrice($allItem);
			$itemBasePrice = $this->rulesProductHelper->getItemBasePrice($allItem);
			$itemOriginalPrice = $this->rulesProductHelper->getItemOriginalPrice($allItem);
			$itemBaseOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($allItem);
			$discountArray[$allItem->getAmrulesId()]['discount'] = $itemPrice * ($percent / 100) * $discountedQty;
			$discountArray[$allItem->getAmrulesId()]['original_discount'] = $itemOriginalPrice * ($percent / 100)
				* $discountedQty;
			$discountArray[$allItem->getAmrulesId()]['base_discount'] = $itemBasePrice * ($percent / 100)
				* $discountedQty;
			$discountArray[$allItem->getAmrulesId()]['base_item_original_discount'] = $itemBaseOriginalPrice
				* ($percent / 100) * $discountedQty;
			$discountArray[$allItem->getAmrulesId()]['percent'] = $percent;
		}

		if (isset($discountArray[$item->getAmrulesId()])) {
			$discountData->setAmount($discountArray[$item->getAmrulesId()]['discount']);
			$discountData->setBaseAmount($discountArray[$item->getAmrulesId()]['base_discount']);
			$discountData->setOriginalAmount($discountArray[$item->getAmrulesId()]['original_discount']);
			$discountData->setBaseOriginalAmount($discountArray[$item->getAmrulesId()]['base_item_original_discount']);
		}

		return $discountData;
	}

}