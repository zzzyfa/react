<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/11/2017
 * Time: 11:08 AM
 */

namespace Althea\ShopByBrand\Model;

use Althea\ShopByBrand\Api\FollowerRepositoryInterface;
use TemplateMonster\ShopByBrand\Model\BrandFactory;

class FollowerRepository implements FollowerRepositoryInterface {

	protected $_followerFactory;
	protected $_brandFactory;

	/**
	 * FollowerRepository constructor.
	 *
	 * @param FollowerFactory $followerFactory
	 * @param BrandFactory    $brandFactory
	 */
	public function __construct(FollowerFactory $followerFactory, BrandFactory $brandFactory)
	{
		$this->_followerFactory = $followerFactory;
		$this->_brandFactory    = $brandFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function getFollowingBrands($customerId)
	{
		$result       = [];
		$followingObj = $this->_followerFactory->create();
		$brandIds     = $followingObj->getCollection()
		                             ->addActiveFilter()
		                             ->addFieldToFilter('customer_id', ['eq' => $customerId])
		                             ->getBrandIds();
		$brandObj     = $this->_brandFactory->create();
		$brands       = $brandObj->getCollection()
		                         ->addFieldToFilter('brand_id', ['in' => $brandIds]);

		/* @var \TemplateMonster\ShopByBrand\Model\Brand $brand */
		foreach ($brands as $brand) {

			if ($brand->getLogo()) { // get image logo url

				$brand->setLogo($brand->getImageLogoUrl());
			}

			if ($brand->getBrandBanner()) { // get brand banner url

				$brand->setBrandBanner($brand->getImageBrandBannerUrl());
			}

			if ($brand->getProductBanner()) { // get product banner url

				$brand->setProductBanner($brand->getImageProductBannerUrl());
			}

			$result[] = $brand;
		}

		return $result;
	}

}