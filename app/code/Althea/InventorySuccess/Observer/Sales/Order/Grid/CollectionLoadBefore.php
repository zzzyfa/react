<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/04/2018
 * Time: 4:22 PM
 */

namespace Althea\InventorySuccess\Observer\Sales\Order\Grid;

use Magento\Framework\Event\Observer as EventObserver;

class CollectionLoadBefore extends \Magestore\InventorySuccess\Observer\Sales\Order\Grid\CollectionLoadBefore {

	/**
	 * @inheritDoc
	 */
	public function execute(EventObserver $observer)
	{
		/** @var  \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection */
		$collection = $observer->getEvent()->getCollection();
		if ($collection->getMainTable() != $this->orderGridCollection->getMainTable())
			return $this;

		$orderProcessService = \Magento\Framework\App\ObjectManager::getInstance()
           ->create('Magestore\InventorySuccess\Api\OrderProcess\OrderProcessServiceInterface');
		if ($orderProcessService->canChangeOrderWarehouse())
			return $this;
		$warehouseCollection = $orderProcessService->getViewWarehouseList();
		$warehouseIds = $warehouseCollection->getAllIds();

		// althea:
		// - apply filter if only there is warehouse ID(s)
		if (!empty($warehouseIds)) {

			$collection->addFieldToFilter('warehouse_id', $warehouseIds);
		}

		return $this;
	}

}