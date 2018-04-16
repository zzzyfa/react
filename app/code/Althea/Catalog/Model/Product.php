<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/08/2017
 * Time: 2:55 PM
 */

namespace Althea\Catalog\Model;

class Product extends \Magento\Catalog\Model\Product {

	public function getStorePrice()
	{
		return $this->getPriceModel()->getStorePrice($this);
	}

	public function getStoreSpecialPrice()
	{
		return $this->getPriceModel()->getStoreSpecialPrice($this);
	}

    /**
     * @inheritDoc
     */
    public function toArray(array $arrAttributes = [])
    {
        $data = parent::toArray($arrAttributes);

        if (!empty($data['brand_althea'])) {

            $attr = $this->getResource()->getAttribute('brand_althea');

            if ($attr->usesSource()) {

                $data['brand_id'] = $data['brand_althea'];
                $data['brand_name'] = $attr->getSource()->getOptionText($data['brand_althea']);

                unset($data['brand_althea']);
            }
        }

        return $data;
    }

	/**
	 * @inheritDoc
	 */
	public function getPrice()
	{
		// althea:
		// - get final price for zero price product
		$price = parent::getPrice();

		if ($price < 0.01) {

			$price = $this->getPriceInfo()
			              ->getPrice('final_price')
			              ->getAmount()
			              ->getValue();
		}

		return $price;
	}

}