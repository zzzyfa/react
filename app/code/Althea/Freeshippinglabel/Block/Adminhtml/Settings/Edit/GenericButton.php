<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 29/12/2017
 * Time: 12:14 PM
 */

namespace Althea\Freeshippinglabel\Block\Adminhtml\Settings\Edit;

use Aheadworks\FreeshippingLabel\Api\LabelRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton {

	/**
	 * @var Context
	 */
	protected $_context;

	/**
	 * @var LabelRepositoryInterface
	 */
	protected $_labelRepository;

	/**
	 * @param Context                  $context
	 * @param LabelRepositoryInterface $labelRepository
	 */
	public function __construct(
		Context $context,
		LabelRepositoryInterface $labelRepository
	)
	{
		$this->_context         = $context;
		$this->_labelRepository = $labelRepository;
	}

	/**
	 * Return label ID
	 *
	 * @return int|null
	 */
	public function getLabelId()
	{
		try {

			return $this->_labelRepository->get($this->_context->getRequest()->getParam('id'))->getId();
		} catch (NoSuchEntityException $e) {

		} catch (LocalizedException $e) {

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
		return $this->_context->getUrlBuilder()->getUrl($route, $params);
	}

}