<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 4:16 PM
 */

namespace Althea\Cms\Model\ResourceModel\Alert\Grid;

use Althea\Cms\Model\ResourceModel\Alert\Collection as AlertCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;

class Collection extends AlertCollection implements SearchResultInterface {

	/**
	 * @var AggregationInterface
	 */
	protected $aggregations;

	/**
	 * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
	 * @param \Psr\Log\LoggerInterface                                     $logger
	 * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
	 * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
	 * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
	 * @param \Magento\Framework\EntityManager\MetadataPool                $metadataPool
	 * @param string                                                       $mainTable
	 * @param string                                                       $eventPrefix
	 * @param string                                                       $eventObject
	 * @param string                                                       $resourceModel
	 * @param string                                                       $model
	 * @param string|null                                                  $connection
	 * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
	 */
	public function __construct(
		\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\EntityManager\MetadataPool $metadataPool,
		$mainTable,
		$eventPrefix,
		$eventObject,
		$resourceModel,
		$model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
		$connection = null,
		\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
	)
	{
		parent::__construct(
			$entityFactory,
			$logger,
			$fetchStrategy,
			$eventManager,
			$storeManager,
			$metadataPool,
			$connection,
			$resource
		);
		$this->_eventPrefix = $eventPrefix;
		$this->_eventObject = $eventObject;
		$this->_init($model, $resourceModel);
		$this->setMainTable($mainTable);
	}

	public function setItems(array $items = null)
	{
		return $this;
	}

	public function getAggregations()
	{
		return $this->aggregations;
	}

	public function setAggregations($aggregations)
	{
		$this->aggregations = $aggregations;
	}

	public function getSearchCriteria()
	{
		return null;
	}

	public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
	{
		return $this;
	}

	public function getTotalCount()
	{
		return $this->getSize();
	}

	public function setTotalCount($totalCount)
	{
		return $this;
	}

	/**
	 * Retrieve all ids for collection
	 * Backward compatibility with EAV collection
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAllIds($limit = null, $offset = null)
	{
		return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
	}

}