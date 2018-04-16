<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 19/01/2018
 * Time: 10:14 AM
 */

namespace Althea\AdyenPayment\Model\Resource\Billing\Agreement;

class Collection extends \Adyen\Payment\Model\Resource\Billing\Agreement\Collection {

	protected $_storeManager;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Data\Collection\EntityFactory $entityFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Customer\Model\ResourceModel\Customer $customerResource,
		\Magento\Eav\Helper\Data $eavHelper,
		$connection = null,
		\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
	)
	{
		$this->_storeManager = $storeManager;

		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $customerResource, $eavHelper, $connection, $resource);
	}

	/**
	 * Allow billing agreement to be shared across multiple stores under one website
	 *
	 * @param $storeId
	 * @return $this
	 */
	public function addWebsiteFilter($storeId)
	{
		$storeIds = $this->_storeManager->getStore($storeId)
		                                ->getWebsite()
		                                ->getStoreIds(true);

		$this->addFieldToFilter('store_id', ['in' => $storeIds]);

		return $this;
	}

}