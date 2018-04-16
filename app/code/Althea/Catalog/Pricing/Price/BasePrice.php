<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 01/04/2018
 * Time: 4:58 PM
 */

namespace Althea\Catalog\Pricing\Price;

use Magento\Framework\Pricing\Price\BasePriceProviderInterface;

class BasePrice extends \Magento\Catalog\Pricing\Price\BasePrice {

	/**
	 * @inheritDoc
	 */
	public function getValue()
	{
		if ($this->value === null) {

			$this->value = false;

			foreach ($this->priceInfo->getPrices() as $price) {

				if ($price instanceof BasePriceProviderInterface
					&& $price->getValue() !== false
					&& $price->getValue() > 0.01 // althea: skip zero price data
				) {

					$this->value = min($price->getValue(), $this->value ?: $price->getValue());
				}
			}
		}

		return $this->value;
	}

}