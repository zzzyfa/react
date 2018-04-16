<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 3:21 PM
 */

namespace Althea\Cms\Model;

use Althea\Cms\Api\AlertRepositoryInterface;
use Althea\Cms\Api\Data\AlertInterface;
use Althea\Cms\Api\Data\AlertInterfaceFactory;
use Althea\Cms\Api\Data\AlertSearchResultsInterfaceFactory;
use Althea\Cms\Model\ResourceModel\Alert as ResourceAlert;
use Althea\Cms\Model\ResourceModel\Alert\CollectionFactory as AlertCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class AlertRepository implements AlertRepositoryInterface {

	/**
	 * @var ResourceAlert
	 */
	protected $resource;

	/**
	 * @var AlertFactory
	 */
	protected $alertFactory;

	/**
	 * @var AlertCollectionFactory
	 */
	protected $alertCollectionFactory;

	/**
	 * @var AlertSearchResultsInterfaceFactory;
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
	 * @var AlertInterfaceFactory
	 */
	protected $dataAlertFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	private $storeManager;

	/**
	 * @var CouponFactory
	 */
	protected $couponFactory;

	/**
	 * @var RuleFactory
	 */
	protected $ruleFactory;

	/**
	 * @param ResourceAlert                      $resource
	 * @param AlertFactory                       $alertFactory
	 * @param AlertInterfaceFactory              $dataAlertFactory
	 * @param AlertCollectionFactory             $alertCollectionFactory
	 * @param AlertSearchResultsInterfaceFactory $searchResultsFactory
	 * @param DataObjectHelper                   $dataObjectHelper
	 * @param DataObjectProcessor                $dataObjectProcessor
	 * @param StoreManagerInterface              $storeManager
	 */
	public function __construct(
		ResourceAlert $resource,
		AlertFactory $alertFactory,
		AlertInterfaceFactory $dataAlertFactory,
		AlertCollectionFactory $alertCollectionFactory,
		AlertSearchResultsInterfaceFactory $searchResultsFactory,
		DataObjectHelper $dataObjectHelper,
		DataObjectProcessor $dataObjectProcessor,
		StoreManagerInterface $storeManager,
		CouponFactory $couponFactory,
		RuleFactory $ruleFactory
	)
	{
		$this->resource               = $resource;
		$this->alertFactory           = $alertFactory;
		$this->alertCollectionFactory = $alertCollectionFactory;
		$this->searchResultsFactory   = $searchResultsFactory;
		$this->dataObjectHelper       = $dataObjectHelper;
		$this->dataAlertFactory       = $dataAlertFactory;
		$this->dataObjectProcessor    = $dataObjectProcessor;
		$this->storeManager           = $storeManager;
		$this->couponFactory          = $couponFactory;
		$this->ruleFactory            = $ruleFactory;
	}

	/**
	 * Save Alert data
	 *
	 * @param AlertInterface $alert
	 * @return Alert
	 * @throws CouldNotSaveException
	 */
	public function save(AlertInterface $alert)
	{
		$storeId = $this->storeManager->getStore()->getId();

		$alert->setStoreId($storeId);

		try {

			$this->resource->save($alert);
		} catch (\Exception $exception) {

			throw new CouldNotSaveException(__($exception->getMessage()));
		}

		return $alert;
	}

	/**
	 * Load Alert data by given Alert Identity
	 *
	 * @param string $alertId
	 * @return Alert
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getById($alertId)
	{
		$alert = $this->alertFactory->create();

		$this->resource->load($alert, $alertId);
		$this->_contentToJson($alert);

		if (!$alert->getId()) {

			throw new NoSuchEntityException(__('Alert with id / code "%1" does not exist.', $alertId));
		}

		return $alert;
	}

	/**
	 * Load store-specific Alert data by given Alert Identity
	 *
	 * @param string $alertId
	 * @param int    $storeId
	 * @return Alert
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getStoreSpecificById($alertId, $storeId)
	{
		$alert = $this->alertFactory->create();

		$alert->setStoreId($storeId);
		$this->resource->load($alert, $alertId);
		$this->_contentToJson($alert);

		if (!$alert->getId()) {

			throw new NoSuchEntityException(__('Alert with id / code "%1" does not exist in store with id "%2".', $alertId, $storeId));
		}

		return $alert;
	}

	/**
	 * Load store-specific new sign up Alert
	 *
	 * @param int $storeId
	 * @return Alert
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getStoreSpecificNewSignUp($storeId)
	{
		/* @var Store $store */
		$store   = $this->storeManager->getStore($storeId);
		$website = $store->getWebsite();
		$coupon  = $this->couponFactory->create();
		$code    = sprintf("WELCOME-%s", strtoupper($website->getCode()));

		$coupon->load($code, 'code');

		/* @var Rule $rule */
		$rule = $this->ruleFactory->create();

		$rule->load($coupon->getRuleId());

		if (null === $rule || !$rule->getIsActive()) {

			throw new NoSuchEntityException(__('New sign up alert does not exist in store with id "%1".', $storeId));
		}

		/* @var Alert $alert */
		$alert = $this->alertFactory->create();

		$alert->setStoreId($storeId);

		return $this->getStoreSpecificById(strtolower($code), $storeId);
	}

	/**
	 * Load Alert data collection by given search criteria
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
	 * @return \Althea\Cms\Model\ResourceModel\Alert\Collection
	 */
	public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
	{
		$searchResults = $this->searchResultsFactory->create();

		$searchResults->setSearchCriteria($criteria);

		$collection = $this->alertCollectionFactory->create();

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

		$alerts = [];

		/** @var Alert $alertModel */

		foreach ($collection as $alertModel) {

			$this->_contentToJson($alertModel);

			$alertData = $this->dataAlertFactory->create();

			$this->dataObjectHelper->populateWithArray(
				$alertData,
				$alertModel->getData(),
				'Althea\Cms\Api\Data\AlertInterface'
			);

			$alerts[] = $this->dataObjectProcessor->buildOutputDataArray(
				$alertData,
				'Althea\Cms\Api\Data\AlertInterface'
			);
		}

		$searchResults->setItems($alerts);

		return $searchResults;
	}

	/**
	 * Delete Alert
	 *
	 * @param \Althea\Cms\Api\Data\AlertInterface $alert
	 * @return bool
	 * @throws CouldNotDeleteException
	 */
	public function delete(AlertInterface $alert)
	{
		try {

			$this->resource->delete($alert);
		} catch (\Exception $exception) {

			throw new CouldNotDeleteException(__($exception->getMessage()));
		}

		return true;
	}

	/**
	 * Delete Alert by given Alert Identity
	 *
	 * @param string $alertId
	 * @return bool
	 * @throws CouldNotDeleteException
	 * @throws NoSuchEntityException
	 */
	public function deleteById($alertId)
	{
		return $this->delete($this->getById($alertId));
	}

	/**
	 * Convert JSON content to array
	 *
	 * @param AlertInterface $alert
	 */
	protected function _contentToJson(\Althea\Cms\Api\Data\AlertInterface &$alert)
	{
		$content = $alert->getContent();

		if (is_array($content)) {

			return;
		}

		$formatted = json_decode($content, true);

		if ($formatted && JSON_ERROR_NONE === json_last_error()) {

			$alert->setContent($formatted);
		}
	}

}