<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 5:24 PM
 */

namespace Althea\PaymentFilter\Model;

use Althea\PaymentFilter\Api\RuleRepositoryInterface;
use Althea\PaymentFilter\Model\ResourceModel\Rule as ResourceRule;
use Magento\Framework\Exception\NoSuchEntityException;

class RuleRepository implements RuleRepositoryInterface {

	/**
	 * @var ResourceRule
	 */
	protected $resource;

	/**
	 * @var RuleFactory
	 */
	protected $bannerFactory;

	/**
	 * @param RuleFactory $ruleFactory
	 */
	public function __construct(ResourceRule $resource, RuleFactory $ruleFactory)
	{
		$this->resource    = $resource;
		$this->ruleFactory = $ruleFactory;
	}

	/**
	 * Load rule data by given rule identity
	 *
	 * @param string $ruleId
	 * @return Rule
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getById($ruleId)
	{
		$rule = $this->ruleFactory->create();

		$this->resource->load($rule, $ruleId);

		if (!$rule->getId()) {

			throw new NoSuchEntityException(__('Rule with id / code "%1" does not exist.', $ruleId));
		}

		return $rule;
	}

}