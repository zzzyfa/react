<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 18/01/2018
 * Time: 3:56 PM
 */

namespace Althea\Checkout\Block;

use Jenssegers\Agent\Agent;
use Magento\Framework\View\Element\Template;

class DeepLink extends Template {

	const MOBILE_CHECKOUT_SUC_DEEPLINK = 'althea://order/success/%s';
	const MOBILE_CHECKOUT_ERR_DEEPLINK = 'althea://order/error/%s';
	const MOBILE_CHECKOUT_SUC_INTENT   = 'intent://order/success/%s/#Intent;scheme=althea;package=kr.althea.release;end';
	const MOBILE_CHECKOUT_ERR_INTENT   = 'intent://order/error/%s/#Intent;scheme=althea;package=kr.althea.release;end';

	protected $_userAgent;
	protected $_configHelper;
	protected $_customLogger;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Althea\Checkout\Helper\Config $config,
		\Althea\Checkout\Logger\CheckoutLogger $logger,
		Template\Context $context,
		array $data = []
	)
	{
		$this->_configHelper = $config;
		$this->_customLogger = $logger;

		parent::__construct($context, $data);
	}

	public function getUserAgent()
	{
		if (!$this->_userAgent) {

			$this->_userAgent = new Agent();
		}

		return $this->_userAgent;
	}

	public function getSuccessDeepLink()
	{
		$deepLink = self::MOBILE_CHECKOUT_SUC_DEEPLINK;

		if ($this->_configHelper->getIsUseAndroidIntentDeepLink()
			&& $this->getUserAgent()->isAndroidOS()
		) {

			$deepLink = self::MOBILE_CHECKOUT_SUC_INTENT;
		}

		if ($this->_configHelper->getIsLogEnabled()) {

			$this->_customLogger->debug(sprintf("[success deep link][%s][%s]", $this->getUserAgent()->platform(), $deepLink));
		}

		return sprintf($deepLink, $this->getRequest()->getParam('order_id'));
	}

	public function getErrorDeepLink()
	{
		$deepLink = self::MOBILE_CHECKOUT_ERR_DEEPLINK;

		if ($this->_configHelper->getIsUseAndroidIntentDeepLink()
			&& $this->getUserAgent()->isAndroidOS()
		) {

			$deepLink = self::MOBILE_CHECKOUT_ERR_INTENT;
		}

		$deepLink = sprintf($deepLink, urlencode($this->getRequest()->getParam('error_message')));

		if ($this->_configHelper->getIsLogEnabled()) {

			$this->_customLogger->debug(sprintf("[error deep link][%s][%s]", $this->getUserAgent()->platform(), $deepLink));
		}

		return $deepLink;
	}

}