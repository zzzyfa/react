<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 10:19 AM
 */

namespace Althea\Cms\Block\Adminhtml\Banner\Edit;

use Althea\Cms\Api\BannerRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton {

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var BannerRepositoryInterface
	 */
	protected $bannerRepository;

	/**
	 * @param Context                   $context
	 * @param BannerRepositoryInterface $bannerRepository
	 */
	public function __construct(
		Context $context,
		BannerRepositoryInterface $bannerRepository
	)
	{
		$this->context          = $context;
		$this->bannerRepository = $bannerRepository;
	}

	/**
	 * Return banner ID
	 *
	 * @return int|null
	 */
	public function getBannerId()
	{
		try {

			return $this->bannerRepository->getById($this->context->getRequest()->getParam('banner_id'))
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