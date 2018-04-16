<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 3:21 PM
 */

namespace Althea\Cms\Model;

use Althea\Cms\Api\BannerRepositoryInterface;
use Althea\Cms\Api\Data\BannerInterface;
use Althea\Cms\Api\Data\BannerInterfaceFactory;
use Althea\Cms\Api\Data\BannerSearchResultsInterfaceFactory;
use Althea\Cms\Model\ResourceModel\Banner as ResourceBanner;
use Althea\Cms\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class BannerRepository implements BannerRepositoryInterface {

	/**
	 * @var ResourceBanner
	 */
	protected $resource;

	/**
	 * @var BannerFactory
	 */
	protected $bannerFactory;

	/**
	 * @var BannerCollectionFactory
	 */
	protected $bannerCollectionFactory;

	/**
	 * @var BannerSearchResultsInterfaceFactory;
	 */
	protected $searchResultsFactory;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var DataObjectProcessor
	 */
	protected $dataObjectProcessor;

	/**
	 * @var BannerInterfaceFactory
	 */
	protected $dataBannerFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	private $storeManager;

	/**
	 * @param ResourceBanner                      $resource
	 * @param BannerFactory                       $bannerFactory
	 * @param BannerInterfaceFactory              $dataBannerFactory
	 * @param BannerCollectionFactory             $bannerCollectionFactory
	 * @param BannerSearchResultsInterfaceFactory $searchResultsFactory
	 * @param DataObjectHelper                    $dataObjectHelper
	 * @param DataObjectProcessor                 $dataObjectProcessor
	 * @param StoreManagerInterface               $storeManager
	 */
	public function __construct(
		ResourceBanner $resource,
		BannerFactory $bannerFactory,
		BannerInterfaceFactory $dataBannerFactory,
		BannerCollectionFactory $bannerCollectionFactory,
		BannerSearchResultsInterfaceFactory $searchResultsFactory,
		DataObjectHelper $dataObjectHelper,
		DataObjectProcessor $dataObjectProcessor,
		StoreManagerInterface $storeManager
	)
	{
		$this->resource                = $resource;
		$this->bannerFactory           = $bannerFactory;
		$this->bannerCollectionFactory = $bannerCollectionFactory;
		$this->searchResultsFactory    = $searchResultsFactory;
		$this->dataObjectHelper        = $dataObjectHelper;
		$this->dataBannerFactory       = $dataBannerFactory;
		$this->dataObjectProcessor     = $dataObjectProcessor;
		$this->storeManager            = $storeManager;
	}

	/**
	 * Save Banner data
	 *
	 * @param BannerInterface $banner
	 * @return Banner
	 * @throws CouldNotSaveException
	 */
	public function save(BannerInterface $banner)
	{
		$storeId = $this->storeManager->getStore()->getId();

		$banner->setStoreId($storeId);

		try {

			$this->resource->save($banner);
		} catch (\Exception $exception) {

			throw new CouldNotSaveException(__($exception->getMessage()));
		}

		return $banner;
	}

	/**
	 * Load Banner data by given Banner Identity
	 *
	 * @param string $bannerId
	 * @return Banner
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getById($bannerId)
	{
		$banner = $this->bannerFactory->create();

		$this->resource->load($banner, $bannerId);

		if (!$banner->getId()) {

			throw new NoSuchEntityException(__('Banner with id / code "%1" does not exist.', $bannerId));
		}

		return $banner;
	}

	/**
	 * Load formatted Banner data by given Banner Identity
	 *
	 * @param string $bannerId
	 * @return Banner
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getFormattedById($bannerId)
	{
		$banner = $this->getById($bannerId);

		$this->_contentToJson($banner);

		return $banner;
	}

	/**
	 * Load Banner data collection by given search criteria
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
	 * @return \Althea\Cms\Model\ResourceModel\Banner\Collection
	 */
	public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
	{
		$searchResults = $this->searchResultsFactory->create();

		$searchResults->setSearchCriteria($criteria);

		$collection = $this->bannerCollectionFactory->create();

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

			foreach ($sortOrders as $sortOrder) {

				$collection->addOrder(
					$sortOrder->getField(),
					($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
				);
			}
		}

		$collection->setCurPage($criteria->getCurrentPage());
		$collection->setPageSize($criteria->getPageSize());

		$banners = [];

		/** @var Banner $bannerModel */

		foreach ($collection as $bannerModel) {

			$this->_contentToJson($bannerModel);

			$bannerData = $this->dataBannerFactory->create();

			$this->dataObjectHelper->populateWithArray(
				$bannerData,
				$bannerModel->getData(),
				'Althea\Cms\Api\Data\BannerInterface'
			);

			$banners[] = $this->dataObjectProcessor->buildOutputDataArray(
				$bannerData,
				'Althea\Cms\Api\Data\BannerInterface'
			);
		}

		$searchResults->setItems($banners);

		return $searchResults;
	}

	/**
	 * Delete Banner
	 *
	 * @param \Althea\Cms\Api\Data\BannerInterface $banner
	 * @return bool
	 * @throws CouldNotDeleteException
	 */
	public function delete(BannerInterface $banner)
	{
		try {

			$this->resource->delete($banner);
		} catch (\Exception $exception) {

			throw new CouldNotDeleteException(__($exception->getMessage()));
		}

		return true;
	}

	/**
	 * Delete Banner by given Banner Identity
	 *
	 * @param string $bannerId
	 * @return bool
	 * @throws CouldNotDeleteException
	 * @throws NoSuchEntityException
	 */
	public function deleteById($bannerId)
	{
		return $this->delete($this->getById($bannerId));
	}

	/**
	 * Convert JSON content to array
	 *
	 * @param BannerInterface $banner
	 */
	protected function _contentToJson(\Althea\Cms\Api\Data\BannerInterface &$banner)
	{
		$content = $banner->getContent();

		if (is_array($content)) {

			return;
		}

		$formatted = json_decode($content, true);

		if ($formatted && JSON_ERROR_NONE === json_last_error()) {

			$banner->setContent($formatted);
		}
	}

}