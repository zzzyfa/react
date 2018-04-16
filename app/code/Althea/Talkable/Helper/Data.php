<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 12/02/2018
 * Time: 10:11 AM
 */

namespace Althea\Talkable\Helper;

use GuzzleHttp\Client;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper {

	const STATUS_APPROVED = 'approved';
	const STATUS_VOIDED   = 'voided';

	protected $_client;
	protected $_configHelper;

	/**
	 * @inheritDoc
	 */
	public function __construct(\AltheaTech\Talkable\Helper\Config $config, Context $context)
	{
		$this->_configHelper = $config;

		parent::__construct($context);
	}

	protected function _getClient()
	{
		if (!$this->_client) {

			$this->_client = new Client();
		}

		return $this->_client;
	}

	protected function _updateOrderReferralStatus($orderIncrementId, $storeId, $status = self::STATUS_APPROVED)
	{
		$apiKey = $this->_configHelper->getApiKey($storeId);
		$siteId = $this->_configHelper->getSiteId($storeId);

		if (!$apiKey || !$siteId) {

			return $this;
		}

		$url      = sprintf("https://www.talkable.com/api/v2/origins/%s/referral", $orderIncrementId);
		$response = $this->_getClient()->request('PUT', $url, array(
			'json' => array(
				'api_key'   => $apiKey,
				'site_slug' => $siteId,
				'data'      => ['status' => $status],
			),
		));

		return json_decode((string)$response->getBody(), true);
	}

	public function approveOrderReferral(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		return $this->_updateOrderReferralStatus($order->getIncrementId(), $order->getStoreId());
	}

	public function voidOrderReferral(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		return $this->_updateOrderReferralStatus($order->getIncrementId(), $order->getStoreId(), self::STATUS_VOIDED);
	}

}