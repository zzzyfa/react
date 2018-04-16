<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/03/2018
 * Time: 5:35 PM
 */

namespace Althea\Rewards\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditMemoRefundObserver implements ObserverInterface {

	protected $_collectionFactory;

	/**
	 * SalesOrderLoadAfterObserver constructor.
	 *
	 * @param \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $collectionFactory
	 */
	public function __construct(
		\Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $collectionFactory
	)
	{
		$this->_collectionFactory = $collectionFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var \Magento\Sales\Model\Order\Creditmemo $creditMemo */
		$creditMemo = $observer->getEvent()->getData('creditmemo');
		$order      = $creditMemo->getOrder();
		/* @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection $collection */
		$collection = $this->_collectionFactory->create()
		                                       ->addFieldToFilter('customer_id', ['eq' => $order->getCustomerId()])
		                                       ->addFieldToFilter('code', ['eq' => sprintf('order_spend-%s', $order->getEntityId())]);

		if ($collection->getSize()
			&& $order->hasForcedCanCreditmemo()
			&& $order->getForcedCanCreditmemo()
		) {

			$order->setForcedCanCreditmemo(false);

			return $this;
		}
	}

}