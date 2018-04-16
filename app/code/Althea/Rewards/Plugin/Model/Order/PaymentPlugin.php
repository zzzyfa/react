<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/03/2018
 * Time: 5:13 PM
 */

namespace Althea\Rewards\Plugin\Model\Order;

use Magento\Payment\Model\Method\Free;

class PaymentPlugin {

	/**
	 * @param \Magento\Sales\Model\Order\Payment $subject
	 * @param \Closure                           $proceed
	 * @return bool|mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function aroundCanRefundPartialPerInvoice(\Magento\Sales\Model\Order\Payment $subject, \Closure $proceed)
	{
		if ($subject->getMethodInstance()->getCode() === Free::PAYMENT_METHOD_FREE_CODE
			&& $subject->getOrder()->hasForcedCanCreditmemo()
			&& $subject->getOrder()->getForcedCanCreditmemo()
		) {

			return true;
		}

		return $proceed();
	}

}