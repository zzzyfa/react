<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 25/07/2017
 * Time: 4:24 PM
 */

namespace Althea\Nexmo\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Custom extends Base {

	/**
	 * @var string
	 */
	protected $fileName = '/var/log/debug-nexmo.log';

	/**
	 * @var int
	 */
	protected $loggerType = Logger::DEBUG;

}