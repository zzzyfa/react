<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/03/2018
 * Time: 11:01 AM
 */

namespace Althea\ProductFilter\Plugin\Model\ResourceModel\Product;

class CollectionPlugin {

	public function aroundGetSelectCountSql(\Magento\Catalog\Model\ResourceModel\Product\Collection $subject, \Closure $proceed)
	{
		/* @var \Magento\Framework\DB\Select $result */
		$result = $proceed();

		$result->reset(\Magento\Framework\Db\Select::GROUP);

		return $result;
	}

}