<?php

namespace Althea\AutoCancelOrder\Model;

use MOLPay\Seamless\Model\Ui\ConfigProvider;

class Cancel extends \Magento\Framework\Model\AbstractModel
{
	protected $_configHelper;
	protected $_logger;
	protected $_autoCancelOrderCollectionFactory;
	protected $_checkoutSession;
	protected $_order;
	protected $_orderCollectionFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Althea\AutoCancelOrder\Helper\Config $config,
		\Althea\AutoCancelOrder\Logger\AutoCancelOrderLogger $logger,
		\Althea\AutoCancelOrder\Model\ResourceModel\Cancel\CollectionFactory $autoCancelOrderCollectionFactory,
		\Magento\Checkout\Model\Session $session,
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_configHelper                     = $config;
		$this->_logger                           = $logger;
		$this->_autoCancelOrderCollectionFactory = $autoCancelOrderCollectionFactory;
		$this->_checkoutSession                  = $session;

		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}

	protected function _construct()
    {
        $this->_init('Althea\AutoCancelOrder\Model\ResourceModel\Cancel');
    }

    public function registerCancel(\Magento\Sales\Api\Data\OrderInterface $order)
    {
	    $paymentMethod             = $this->_getPaymentMethod($order);
	    $leadTimeValue             = $this->_configHelper->getPaymentStatuses();
	    $unserializedLeadTimeValue = unserialize($leadTimeValue);
	    $leadTime                  = null;

	    if ($unserializedLeadTimeValue) {

		    foreach ($unserializedLeadTimeValue as $value) {

		    	if ($value['payment_id'] != $paymentMethod) {

		    		continue;
			    }

			    $leadTime = $value['schedule'];
			    break;
		    }
	    }

	    if (!is_null($leadTime)) {

		    $leadTime       = $leadTime * 60;
		    $autoCancelDate = date("Y-m-d H:i:s", strtotime($order->getCreatedAt()) + $leadTime);
		    $data           = array(
			    'order_id'        => $order->getEntityId(),
			    'autocancel_date' => $autoCancelDate,
		    );

		    $this->setData($data);

		    try {

		    	if ($this->_configHelper->getIsLogEnabled()) {

					$this->_logger->debug(sprintf("method: %s, data: %s", $paymentMethod, json_encode($data)));
			    }

			    $this->save();
		    } catch (\Exception $e) {

		    	if ($this->_configHelper->getIsLogEnabled()) {

				    $this->_logger->debug($e->getMessage());
			    }
		    }
	    }

        return $this;
    }

    public function processCancel()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $orders = $objectManager->get('Magento\Sales\Model\Order')->getCollection()
            ->addAttributeToSelect(
                'entity_id'
            )
            ->addAttributeToFilter(
                'status',
                array('in' => $this->_getOrderStatuses())
            );

        $ordersIds = [];
        foreach ($orders as $order) {
            $ordersIds[] = $order->getId();

            $this->_logger->debug("processCancel in Cancel: " . $order->getId());
        }

        $ordersToCancel = $this->_autoCancelOrderCollectionFactory->create($ordersIds, date("Y-m-d H:i:s"), 0);

        foreach ($ordersToCancel as $orderToCancel) {
            $order = $objectManager->get('Magento\Sales\Model\Order')->loadByAttribute('entity_id', $orderToCancel->getOrderId());
            if ($order->canCancel()) {
                $order->cancel()->save();
                $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED)
                    ->addStatusHistoryComment("This order has been automatically cancelled by the \"Althea Auto Cancel Order\" module.")
                    ->save();

                $orderToCancel->setData('autocancel_status', 1);
                $orderToCancel->save();
            }
        }

        return $this;
    }

    protected function _getOrderStatuses()
    {
	    $orderStatusesConfig = $this->_configHelper->getOrderStatuses();
	    $orderStatuses       = explode(',', $orderStatusesConfig);

	    return $orderStatuses;
    }

    protected function _getPaymentMethod(\Magento\Sales\Api\Data\OrderInterface $order)
    {
    	$paymentMethod = $order->getPayment()->getMethod();

	    switch ($paymentMethod) {

		    // althea:
		    // - get MOLPay payment channel from checkout session
		    case ConfigProvider::CODE:
			    $paymentMethod = $this->_checkoutSession->getAltheaCheckoutPaymentChannel();
			    break;
		    default:
				// do nothing
	    }

	    return $paymentMethod;
    }

}
