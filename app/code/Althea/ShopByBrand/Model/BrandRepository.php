<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/11/2017
 * Time: 6:27 PM
 */

namespace Althea\ShopByBrand\Model;

use TemplateMonster\ShopByBrand\Model\Brand;

class BrandRepository extends \TemplateMonster\ShopByBrand\Model\BrandRepository {

	/**
	 * @inheritDoc
	 */
	public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
	{
		$searchResults = $this->searchResultsFactory->create();
		$searchResults->setSearchCriteria($criteria);

		$collection = $this->brandCollectionFactory->create();
		foreach ($criteria->getFilterGroups() as $filterGroup) {
			foreach ($filterGroup->getFilters() as $filter) {
				if ($filter->getField() === 'store_id') {
					$collection->addStoreFilter($filter->getValue(), false);
					continue;
				}
				$condition = $filter->getConditionType() ?: 'eq';
				$collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
			}
		}
		$searchResults->setTotalCount($collection->getSize());
		$sortOrders = $criteria->getSortOrders();
		if ($sortOrders) {
			/** @var SortOrder $sortOrder */
			foreach ($sortOrders as $sortOrder) {
				$collection->addOrder(
					$sortOrder->getField(),
					($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
				);
			}
		}
		$collection->setCurPage($criteria->getCurrentPage());
		$collection->setPageSize($criteria->getPageSize());
		$brands = [];
		/** @var Brand $brandModel */
		foreach ($collection as $brandModel) {
			$brandData = $this->dataBrandFactory->create();
			$this->dataObjectHelper->populateWithArray(
				$brandData,
				$brandModel->getData(),
				'TemplateMonster\ShopByBrand\Api\Data\BrandInterface'
			);
			$result = $this->dataObjectProcessor->buildOutputDataArray(
				$brandData,
				'TemplateMonster\ShopByBrand\Api\Data\BrandInterface'
			);

			/* @var Brand $brandModel */
			$result['id']         = $brandModel->getId(); // althea: get brand id
			$result['page_title'] = $brandModel->getPageTitle(); // althea: get page title

			// althea: get image logo url
			if ($brandData->getLogo() && ($url = $brandData->getImageLogoUrl())) {

				$result['logo'] = $url;
			}

			// althea: get image brand banner url
			if ($brandData->getBrandBanner() && ($url = $brandData->getImageBrandBannerUrl())) {

				$result['brand_banner'] = $url;
			}

			// althea: get image product banner url
			if ($brandData->getProductBanner() && ($url = $brandData->getImageProductBannerUrl())) {

				$result['product_banner'] = $url;
			}

			$brands[] = $result;
		}
		$searchResults->setItems($brands);
		return $searchResults;
	}

}