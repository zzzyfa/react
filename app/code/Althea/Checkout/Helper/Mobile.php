<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/02/2018
 * Time: 11:18 AM
 */

namespace Althea\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Mobile extends AbstractHelper {

	const MOBILE_CHECKOUT_CODE = 'mcheckout';

	protected $_session;

	/**
	 * @inheritDoc
	 */
	public function __construct(\Althea\Checkout\Model\Session $session, Context $context)
	{
		$this->_session = $session;

		parent::__construct($context);
	}

	public function initMobileCheckoutSession()
	{
		$this->_session->setCheckoutCode(self::MOBILE_CHECKOUT_CODE);
	}

	public function getMobileCheckoutSession()
	{
		return $this->_session->getCheckoutCode();
	}

	public function clearMobileCheckoutSession()
	{
		$this->_session->unsCheckoutCode();
	}

	public function isMobileCheckout()
	{
		return $this->getMobileCheckoutSession() == self::MOBILE_CHECKOUT_CODE;
	}

}