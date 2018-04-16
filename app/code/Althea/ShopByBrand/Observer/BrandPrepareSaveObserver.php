<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/11/2017
 * Time: 4:55 PM
 */

namespace Althea\ShopByBrand\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use TemplateMonster\ShopByBrand\Model\Brand;

class BrandPrepareSaveObserver implements ObserverInterface {

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var Brand $brand */
		$brand      = $observer->getData('brand');
		$websiteIds = $brand->getData('website_ids');

		if (is_array($websiteIds)) {

			$brand->setData('website_ids', implode(",", $websiteIds));
		}
	}

}