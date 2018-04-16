<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/03/2018
 * Time: 6:05 PM
 */

namespace Althea\Rewards\Plugin\Model\Order;

class CreditMemoPlugin {

	public function aroundSetOrder(\Magento\Sales\Model\Order\Creditmemo $subject, \Closure $proceed, \Magento\Sales\Model\Order $order)
	{
		$result = $proceed($order);

		if ($order->hasForcedCanCreditmemo()
			&& $order->getForcedCanCreditmemo()
		) {

			$subject->setAllowZeroGrandTotal(true);
		}

		return $result;
	}

}