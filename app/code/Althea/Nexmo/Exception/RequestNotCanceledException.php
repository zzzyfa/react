<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 24/07/2017
 * Time: 3:28 PM
 */

namespace Althea\Nexmo\Exception;

use Althea\Nexmo\Helper\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class RequestNotCanceledException extends LocalizedException {

	protected $configHelper;

	public function __construct(Phrase $phrase, \Exception $cause = null, Config $configHelper)
	{
		$this->configHelper = $configHelper;

		parent::__construct($phrase, $cause);
	}

	public function getDefaultErrorMessage()
	{
		return $this->configHelper->getRequestNotCanceledExceptionMsg();
	}

}