<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 05/12/2017
 * Time: 6:05 PM
 */

namespace Althea\Aftership\Controller\Index;

use Althea\Aftership\Helper\Config;
use Althea\Aftership\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

class WebHook extends Action {

	const TYPE_UPDATE = 'tracking_update';

	const TAG_PENDING   = 'pending';
	const TAG_DELIVERED = 'Delivered';
	const TAG_EXCEPTION = 'Exception';

	CONST STATE_COMPLETE_SHIPMENT     = 'complete_shipping';
	CONST STATE_PROCESSING_COD        = 'processing_cod';
	CONST STATE_SHIPPED_COD           = 'shipped_cod';
	CONST STATE_COMPLETE_SHIPMENT_COD = 'complete_cod';
	CONST STATE_REFUSE_SHIPMENT_COD   = 'canceled_cod';

	protected $_altheaAftershipConfig;
	protected $_altheaAftershipHelper;
	protected $_orderFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(Config $config, Data $data, OrderFactory $orderFactory, Context $context)
	{
		$this->_altheaAftershipConfig = $config;
		$this->_altheaAftershipHelper = $data;
		$this->_orderFactory          = $orderFactory;

		parent::__construct($context);
	}

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		if (!$this->_validateRequest()) {

			// validate request from Aftership
			return;
		}

		$body    = file_get_contents('php://input');
		$content = json_decode($body, true);

		if (json_last_error() !== JSON_ERROR_NONE) {

			// discontinue if invalid json
			return;
		} else if (empty($content['event']) || $content['event'] != self::TYPE_UPDATE) {

			// discontinue if not tracking_update event
			return;
		} else if (empty($content['msg']['order_id'])) {

			// discontinue if no order id
			return;
		}

		$order = $this->_orderFactory->create()->loadByIncrementId($content['msg']['order_id']);

		if (!$order->getId()) {

			// discontinue if order not found
			return;
		}

		switch ($content['msg']['tag']) {

			case self::TAG_PENDING:
				// do nothing as shipment status is still pending
				break;
			case self::TAG_DELIVERED:
				if (self::STATE_PROCESSING_COD === $order->getStatus()
					|| self::STATE_SHIPPED_COD === $order->getStatus()
				) {

					// for CoD orders only

					$this->_altheaAftershipHelper->createInvoice($order); // create invoice once delivered
					$order->setState(Order::STATE_COMPLETE);
					$order->setStatus(self::STATE_COMPLETE_SHIPMENT_COD);
				} else {

					// for normal orders only

					$order->setState(Order::STATE_COMPLETE);
					$order->setStatus(self::STATE_COMPLETE_SHIPMENT);
				}

				$order->save();
				$this->_altheaAftershipHelper->updateRegistryOrder($order);
				break;
			case self::TAG_EXCEPTION:
				// set order as refused / canceled
				if (self::STATE_PROCESSING_COD === $order->getStatus()
					|| self::STATE_SHIPPED_COD === $order->getStatus()
				) {

					// for CoD - refused only

					$order->setState(Order::STATE_CANCELED);
					$order->setStatus(self::STATE_REFUSE_SHIPMENT_COD);
					$order->save();
				}
				break;
			default:
				if (self::STATE_PROCESSING_COD === $order->getStatus()) {

					$order->setState(Order::STATE_PROCESSING);
					$order->setStatus(self::STATE_SHIPPED_COD);
					$order->save();
				} else if (Order::STATE_PROCESSING === $order->getStatus()) {

					$order->setData(Order::STATE_COMPLETE);
					$order->setStatus(Order::STATE_COMPLETE);
					$order->save();
				}
		}
	}

	protected function _validateRequest()
	{
		if (!function_exists('hash_equals')) { // for PHP < 5.6

			function hash_equals($str1, $str2)
			{
				if (strlen($str1) != strlen($str2)) {

					return false;
				} else {

					$res = $str1 ^ $str2;
					$ret = 0;

					for ($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);

					return !$ret;
				}
			}
		}

		$request = $this->getRequest();

		if ($request->isPost()
			&& ($paramKey = $request->getParam('key'))
			&& ($key = $this->_altheaAftershipConfig->getExtensionWebhookKey())
			&& ($salt = $this->_altheaAftershipConfig->getExtensionWebhookSalt())
		) {

			return hash_equals($paramKey, md5($key . $salt));
		}

		return false;
	}

}