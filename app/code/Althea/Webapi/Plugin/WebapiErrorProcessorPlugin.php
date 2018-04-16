<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 21/11/2017
 * Time: 4:31 PM
 */

namespace Althea\Webapi\Plugin;

use Althea\Framework\Exception\ServiceUnavailableException;
use Magento\Framework\App\State;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\ErrorProcessor;
use Magento\Framework\Webapi\Exception as WebapiException;

class WebapiErrorProcessorPlugin {

	protected $_appState;

	/**
	 * WebapiErrorProcessorPlugin constructor.
	 *
	 * @param \Magento\Framework\App\State $state
	 */
	public function __construct(State $state)
	{
		$this->_appState = $state;
	}

	public function aroundMaskException(ErrorProcessor $subject, \Closure $proceed, \Exception $exception)
	{
		$result = $proceed($exception);

		if ($exception instanceof ServiceUnavailableException) {

			$isDevMode  = $this->_appState->getMode() === State::MODE_DEVELOPER;
			$stackTrace = $isDevMode ? $exception->getTraceAsString() : null;
			$result     = new WebapiException(
				new Phrase($exception->getRawMessage()),
				$exception->getCode(),
				ServiceUnavailableException::HTTP_SERVICE_UNAVAILABLE,
				$exception->getParameters(),
				get_class($exception),
				null,
				$stackTrace
			);
		}

		return $result;
	}

}