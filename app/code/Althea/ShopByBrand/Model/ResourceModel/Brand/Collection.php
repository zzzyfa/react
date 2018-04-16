<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/11/2017
 * Time: 5:37 PM
 */

namespace Althea\ShopByBrand\Model\ResourceModel\Brand;

class Collection extends \TemplateMonster\ShopByBrand\Model\ResourceModel\Brand\Collection {

	/**
	 * @inheritDoc
	 */
	public function addWebsiteFilter($websiteId = null)
	{
		return $this->addFieldToFilter('website_ids', ['finset' => $this->_storeManager->getWebsite($websiteId)->getId()]);
	}

	/**
	 * Add missing store filter.
	 *
	 * @return $this
	 */
	public function addStoreFilter($storeId = null)
	{
		return $this->addFieldToFilter('website_ids', ['finset' => $this->_storeManager->getStore($storeId)->getWebsiteId()]);
	}

}