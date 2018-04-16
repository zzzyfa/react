<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 10:19 AM
 */

namespace Althea\Cms\Block\Adminhtml\Alert\Edit;

use Althea\Cms\Api\AlertRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton {

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var AlertRepositoryInterface
	 */
	protected $alertRepository;

	/**
	 * @param Context                   $context
	 * @param AlertRepositoryInterface $alertRepository
	 */
	public function __construct(
		Context $context,
		AlertRepositoryInterface $alertRepository
	)
	{
		$this->context          = $context;
		$this->alertRepository = $alertRepository;
	}

	/**
	 * Return alert ID
	 *
	 * @return int|null
	 */
	public function getAlertId()
	{
		try {

			return $this->alertRepository->getById($this->context->getRequest()->getParam('alert_id'))
			                              ->getId();
		} catch (NoSuchEntityException $e) {
		}

		return null;
	}

	/**
	 * Generate url by route and parameters
	 *
	 * @param   string $route
	 * @param   array  $params
	 * @return  string
	 */
	public function getUrl($route = '', $params = [])
	{
		return $this->context->getUrlBuilder()->getUrl($route, $params);
	}

}