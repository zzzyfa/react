<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/03/2018
 * Time: 5:26 PM
 */

namespace Althea\Rewards\Plugin\Model\Order;

class InvoicePlugin {

	/**
	 * @param \Magento\Sales\Model\Order\Invoice $subject
	 * @param \Closure                           $proceed
	 * @return bool|mixed
	 */
	public function aroundCanRefund(\Magento\Sales\Model\Order\Invoice $subject, \Closure $proceed)
	{
		if ($subject->getState() == $subject::STATE_PAID
			&& $subject->getOrder()->hasForcedCanCreditmemo()
			&& $subject->getOrder()->getForcedCanCreditmemo()
		) {

			return true;
		}

		return $proceed();
	}

}