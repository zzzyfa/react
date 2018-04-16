<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 12/02/2018
 * Time: 10:00 AM
 */

namespace Althea\Talkable\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class VoidOrderReferralObserver implements ObserverInterface {

	protected $_configHelper;
	protected $_talkableHelper;
	protected $_talkableLogger;

	/**
	 * VoidOrderReferralObserver constructor.
	 *
	 * @param \AltheaTech\Talkable\Helper\Config     $config
	 * @param \Althea\Talkable\Helper\Data           $data
	 * @param \Althea\Talkable\Logger\TalkableLogger $logger
	 */
	public function __construct(
		\AltheaTech\Talkable\Helper\Config $config,
		\Althea\Talkable\Helper\Data $data,
		\Althea\Talkable\Logger\TalkableLogger $logger
	)
	{
		$this->_configHelper   = $config;
		$this->_talkableHelper = $data;
		$this->_talkableLogger = $logger;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var \Magento\Sales\Model\Order $order */
		$order = $observer->getEvent()->getOrder();

		if (!$order) {

			return $this;
		}

		try {

			$this->_talkableHelper->voidOrderReferral($order);
		} catch (\GuzzleHttp\Exception\RequestException $e) {

			if ($this->_configHelper->getIsLogEnabled()) {

				$this->_talkableLogger->debug(sprintf("order #%s: %s", $order->getIncrementId(), $e->getMessage()));
			}
		} catch (\Exception $e) {

			if ($this->_configHelper->getIsLogEnabled()) {

				$this->_talkableLogger->debug(sprintf("order #%s: %s", $order->getIncrementId(), $e->getMessage()));
			}
		}
	}


}