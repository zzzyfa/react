<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 12/02/2018
 * Time: 10:17 AM
 */

namespace Althea\Talkable\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;

class Custom extends Base {

	/**
	 * @var string
	 */
	protected $fileName = '/var/log/debug-talkable.log';

	/**
	 * @var int
	 */
	protected $loggerType = \Monolog\Logger::DEBUG;

}