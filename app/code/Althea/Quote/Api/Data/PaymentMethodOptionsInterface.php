<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/11/2017
 * Time: 11:03 AM
 */

namespace Althea\Quote\Api\Data;

use Magento\Quote\Api\Data\PaymentMethodInterface;

interface PaymentMethodOptionsInterface extends PaymentMethodInterface {

	/**
	 * Get payment method options
	 *
	 * @return array
	 */
	public function getOptions();

}