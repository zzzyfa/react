<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/04/2018
 * Time: 10:59 AM
 */

namespace Althea\Sales\Model\Order;

class Payment extends \Magento\Sales\Model\Order\Payment {

	/**
	 * @inheritDoc
	 */
	public function canVoid()
	{
		if (null === $this->_canVoidLookup) {
			$this->_canVoidLookup = (bool)$this->getMethodInstance()->canVoid();
			if ($this->_canVoidLookup) {
				$authTransaction = $this->getAuthorizationTransaction();
				// althea:
				// - allow cancelling failed adyen payment instead of voiding
				// - otherwise order state / status will become processing in _void() @ Magento/Sales/Model/Order/Payment.php
				$this->_canVoidLookup = (bool)$authTransaction && !(int)$authTransaction->getIsClosed() && !$this->getOrder()->canCancelPaymentReview();
			}
		}
		return $this->_canVoidLookup;
	}

}