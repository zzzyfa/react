<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 26/03/2018
 * Time: 6:05 PM
 */

namespace Althea\Checkout\Plugin\Controller\Index;

class IndexPlugin {

	protected $_checkoutSession;

	/**
	 * IndexPlugin constructor.
	 *
	 * @param \Magento\Checkout\Model\Session $session
	 */
	public function __construct(\Magento\Checkout\Model\Session $session)
	{
		$this->_checkoutSession = $session;
	}

	public function aroundExecute(\MOLPay\Seamless\Controller\Index\Index $subject, \Closure $proceed)
	{
		if (isset($_POST['payment_options']) && $_POST['payment_options'] != "") {

			$this->_checkoutSession->setAltheaCheckoutPaymentChannel($_POST['payment_options']);
		}

		return $proceed();
	}

}