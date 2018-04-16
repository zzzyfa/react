<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 17/10/2017
 * Time: 11:47 AM
 */

namespace Althea\ShopByBrand\Model\ResourceModel\Follower;

use Althea\ShopByBrand\Api\Data\FollowerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Magento\Store\Model\StoreManagerInterface;

class Collection extends AbstractCollection {

	/**
	 * @var string
	 */
	protected $_idFieldName = 'follower_id';

	/**
	 * @var StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Althea\ShopByBrand\Model\Follower', 'Althea\ShopByBrand\Model\ResourceModel\Follower');
	}

	public function __construct(
		StoreManagerInterface $storeManager,
		\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
		\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
	)
	{
		$this->_storeManager = $storeManager;
		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
	}

	/**
	 * Add active filter.
	 *
	 * @return $this
	 */
	public function addActiveFilter($isActive = true)
	{
		return $this->addFieldToFilter('is_active', ['eq' => intval($isActive === true)]);
	}

	/**
	 * Retrieve brand ids for collection
	 *
	 * @return array
	 */
	public function getBrandIds()
	{
		$idsSelect = clone $this->getSelect();

		$idsSelect->columns(FollowerInterface::BRAND_ID, 'main_table');

		return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
	}

}