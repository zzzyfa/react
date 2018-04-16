<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/08/2017
 * Time: 6:16 PM
 */

namespace Althea\Webapi\Controller;

use Althea\Framework\Exception\ServiceUnavailableException;
use Althea\Webapi\Helper\Config;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Authorization;
use Magento\Framework\Webapi\ErrorProcessor;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\Framework\Webapi\Rest\Response as RestResponse;
use Magento\Framework\Webapi\Rest\Response\FieldsFilter;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Webapi\Controller\PathProcessor;
use Magento\Webapi\Controller\Rest\ParamsOverrider;
use Magento\Webapi\Controller\Rest\Router;
use Magento\Webapi\Model\Rest\Swagger\Generator;

class Rest extends \Magento\Webapi\Controller\Rest {

	protected $configHelper;

	public function __construct(
		RestRequest $request,
		RestResponse $response,
		Router $router,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\App\State $appState,
		Authorization $authorization,
		ServiceInputProcessor $serviceInputProcessor,
		ErrorProcessor $errorProcessor,
		PathProcessor $pathProcessor,
		\Magento\Framework\App\AreaList $areaList,
		FieldsFilter $fieldsFilter,
		ParamsOverrider $paramsOverrider,
		ServiceOutputProcessor $serviceOutputProcessor,
		Generator $swaggerGenerator,
		StoreManagerInterface $storeManager,
		Config $configHelper
	)
	{
		$this->configHelper = $configHelper;

		parent::__construct($request, $response, $router, $objectManager, $appState, $authorization, $serviceInputProcessor, $errorProcessor, $pathProcessor, $areaList, $fieldsFilter, $paramsOverrider, $serviceOutputProcessor, $swaggerGenerator, $storeManager);
	}

	/**
	 * Execute API request
	 *
	 * @return void
	 * @throws AuthorizationException
	 * @throws \Magento\Framework\Exception\InputException
	 * @throws \Magento\Framework\Webapi\Exception
	 */
	protected function processApiRequest()
	{
		// todo: enable API maintenance mode by store

		if (!$this->configHelper->getGeneralEnable()) {

			parent::processApiRequest();
		} else {

			$phrase = __('Althea mobile app is temporarily unavailable. Please try again later.');

			if ($msg = $this->configHelper->getMsgContent()) {

				$phrase = __($msg);
			}

			throw new ServiceUnavailableException($phrase);
		}
	}

}