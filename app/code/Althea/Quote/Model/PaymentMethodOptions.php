<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/11/2017
 * Time: 11:33 AM
 */

namespace Althea\Quote\Model;

use Althea\Quote\Api\Data\PaymentMethodOptionsInterface;

class PaymentMethodOptions implements PaymentMethodOptionsInterface {

	/**
	 * @inheritDoc
	 */
	public function getCode()
	{
		return $this->{'code'};
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return $this->{'title'};
	}

	/**
	 * @inheritDoc
	 */
	public function getOptions()
	{
		return $this->{'options'};
	}

}