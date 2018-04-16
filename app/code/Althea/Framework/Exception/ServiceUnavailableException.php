<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 15/08/2017
 * Time: 10:44 AM
 */

namespace Althea\Framework\Exception;

use Magento\Framework\Exception\LocalizedException;

class ServiceUnavailableException extends LocalizedException {

	const HTTP_SERVICE_UNAVAILABLE = 503;

}