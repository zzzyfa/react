<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/03/2018
 * Time: 3:56 PM
 */

namespace Althea\Rewards\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderLoadAfterObserver implements ObserverInterface {

	protected $_collectionFactory;
	protected $_priceCurrency;

	/**
	 * SalesOrderLoadAfterObserver constructor.
	 *
	 * @param \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $collectionFactory
	 * @param \Magento\Framework\Pricing\PriceCurrencyInterface                   $priceCurrency
	 */
	public function __construct(
		\Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $collectionFactory,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
	)
	{
		$this->_collectionFactory = $collectionFactory;
		$this->_priceCurrency     = $priceCurrency;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var \Magento\Sales\Model\Order $order */
		$order = $observer->getEvent()->getData('order');

		if ($order->getState() === \Magento\Sales\Model\Order::STATE_CLOSED
			|| $order->isCanceled()
			|| $order->canUnhold()
		) {

			return $this;
		}

		/* @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection $collection */
		$collection = $this->_collectionFactory->create()
		                                       ->addFieldToFilter('customer_id', ['eq' => $order->getCustomerId()])
		                                       ->addFieldToFilter('code', ['eq' => sprintf('order_spend-%s', $order->getEntityId())]);

		if (!$collection->getSize()) {

			return $this;
		}

		// force allow credit memo for orders paid full solely using rewardpoints
		if (is_null($order->getTotalRefunded())
			&& (abs($this->_priceCurrency->round($order->getSubtotalInvoiced()) + $order->getDiscountInvoiced()) < .0001)
		) {

			$order->setForcedCanCreditmemo(true);

			return $this;
		}
	}

}