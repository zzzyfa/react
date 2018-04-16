<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 18/07/2017
 * Time: 12:48 PM
 */

namespace Althea\SalesRule\Model\Rewrite\ResourceModel\Coupon;

class Usage extends \Magento\SalesRule\Model\ResourceModel\Coupon\Usage {

	/**
	 * Increment / decrement times_used counter
	 *
	 * @param int $customerId
	 * @param mixed $couponId
	 * @return void
	 */
	public function updateCustomerCouponTimesUsed($customerId, $couponId, $isDecrement = false)
	{
		$connection = $this->getConnection();
		$select = $connection->select();
		$select->from(
			$this->getMainTable(),
			['times_used']
		)->where(
			'coupon_id = :coupon_id'
		)->where(
			'customer_id = :customer_id'
		);

		$timesUsed = $connection->fetchOne($select, [':coupon_id' => $couponId, ':customer_id' => $customerId]);

		if ($timesUsed > 0) {

			// althea: check increment or decrement
			if ($isDecrement) {

				$timesUsed--;
			} else {

				$timesUsed++;
			}

			$this->getConnection()->update(
				$this->getMainTable(),
				['times_used' => $timesUsed],
				['coupon_id = ?' => $couponId, 'customer_id = ?' => $customerId]
			);
		} else {
			$this->getConnection()->insert(
				$this->getMainTable(),
				['coupon_id' => $couponId, 'customer_id' => $customerId, 'times_used' => 1]
			);
		}
	}

}