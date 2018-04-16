<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 18/01/2018
 * Time: 9:33 AM
 */

namespace Althea\Checkout\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;

class Custom extends Base {

	/**
	 * @var string
	 */
	protected $fileName = '/var/log/debug-mobile-checkout.log';

	/**
	 * @var int
	 */
	protected $loggerType = \Monolog\Logger::DEBUG;

}