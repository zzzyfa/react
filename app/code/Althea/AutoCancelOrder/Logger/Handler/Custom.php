<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 16/04/2018
 * Time: 10:40 AM
 */

namespace Althea\AutoCancelOrder\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;

class Custom extends Base {

	/**
	 * @var string
	 */
	protected $fileName = '/var/log/debug-auto-cancel-order.log';

	/**
	 * @var int
	 */
	protected $loggerType = \Monolog\Logger::DEBUG;

}