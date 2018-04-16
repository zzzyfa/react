<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/03/2018
 * Time: 1:55 PM
 */

namespace Althea\Payment\Plugin\Helper;

use MOLPay\Seamless\Model\Ui\ConfigProvider;

class DataPlugin {

	protected $_molpaySeamlessChannel;
	protected $_molpaySeamlessHelper;

	/**
	 * DataPlugin constructor.
	 *
	 * @param \MOLPay\Seamless\Model\Source\Channel $channel
	 * @param \MOLPay\Seamless\Helper\Data          $data
	 */
	public function __construct(\MOLPay\Seamless\Model\Source\Channel $channel, \MOLPay\Seamless\Helper\Data $data)
	{
		$this->_molpaySeamlessChannel = $channel;
		$this->_molpaySeamlessHelper  = $data;
	}

	public function aroundGetPaymentMethodList(\Magento\Payment\Helper\Data $subject, \Closure $proceed, $sorted = true, $asLabelValue = false, $withGroups = false, $store = null)
	{
		$result = $proceed($sorted, $asLabelValue, $withGroups, $store);

		foreach ($result as $key => $label) {

			if ($key !== ConfigProvider::CODE) {

				continue;
			}

			unset($result[$key]);

			$result = array_merge($result, $this->_getMolpayActiveChannels());
		}

		return $result;
	}

	protected function _getMolpayActiveChannels()
	{
		$channels       = explode(",", $this->_molpaySeamlessHelper->getActiveChannels());
		$allChannel     = $this->_molpaySeamlessChannel->toArray();
		$activeChannels = [];

		foreach ($allChannel as $k => $v) {

			if (in_array($k, $channels)) {

				$activeChannels[$k] = $v;
			}
		}

		return $activeChannels;
	}

}