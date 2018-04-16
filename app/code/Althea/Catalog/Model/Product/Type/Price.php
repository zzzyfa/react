<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/08/2017
 * Time: 5:49 PM
 */

namespace Althea\Catalog\Model\Product\Type;

class Price extends \Magento\Catalog\Model\Product\Type\Price {

	public function getStorePrice($product)
	{
		return $this->priceCurrency->format($product->getPrice(), false);
	}

	public function getStoreSpecialPrice($product)
	{
		return $this->priceCurrency->format($product->getFinalPrice(), false);
	}

	/**
	 * @inheritDoc
	 */
	public function calculateSpecialPrice(
		$finalPrice,
		$specialPrice,
		$specialPriceFrom,
		$specialPriceTo,
		$store = null
	)
	{
		if ($specialPrice !== null
			&& $specialPrice != false
			&& $specialPrice > 0.01 // althea: skip zero price data
		) {

			if ($this->_localeDate->isScopeDateInInterval($store, $specialPriceFrom, $specialPriceTo)) {

				$finalPrice = min($finalPrice, $specialPrice);
			}
		}

		return $finalPrice;
	}

}