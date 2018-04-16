<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 5:02 PM
 */

namespace Althea\PaymentFilter\Block\Adminhtml\Rule\Edit;

use Althea\PaymentFilter\Api\RuleRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton {

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var RuleRepositoryInterface
	 */
	protected $ruleRepository;

	/**
	 * @param Context                 $context
	 * @param RuleRepositoryInterface $ruleRepository
	 */
	public function __construct(
		Context $context,
		RuleRepositoryInterface $ruleRepository
	)
	{
		$this->context        = $context;
		$this->ruleRepository = $ruleRepository;
	}

	/**
	 * Return rule ID
	 *
	 * @return int|null
	 */
	public function getRuleId()
	{
		try {

			return $this->ruleRepository->getById($this->context->getRequest()->getParam('rule_id'))
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