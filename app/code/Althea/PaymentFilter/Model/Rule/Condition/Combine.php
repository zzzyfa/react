<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/09/2017
 * Time: 3:50 PM
 */

namespace Althea\PaymentFilter\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;

class Combine extends \Magento\Rule\Model\Condition\Combine {

	/**
	 * Core event manager proxy
	 *
	 * @var \Magento\Framework\Event\ManagerInterface
	 */
	protected $_eventManager = null;

	/**
	 * @var \Magento\SalesRule\Model\Rule\Condition\Address
	 */
	protected $_conditionAddress;

	/**
	 * @var \Althea\PaymentFilter\Model\Rule\Condition\Customer
	 */
	protected $_conditonCustomer;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Context $context,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress,
		Customer $conditionCustomer,
		array $data = []
	)
	{
		$this->_eventManager     = $eventManager;
		$this->_conditionAddress = $conditionAddress;
		$this->_conditonCustomer = $conditionCustomer;

		parent::__construct($context, $data);
	}

	/**
	 * @inheritDoc
	 */
	public function getNewChildSelectOptions()
	{
		$addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
		$attributes        = [];

		foreach ($addressAttributes as $code => $label) {
			$attributes[] = [
				'value' => 'Magento\SalesRule\Model\Rule\Condition\Address|' . $code,
				'label' => $label,
			];
		}

		$customerAttributes = $this->_conditonCustomer->loadAttributeOptions()->getAttributeOption();
		$customerAttrs      = [];

		foreach ($customerAttributes as $code => $label) {

			$customerAttrs[] = [
				'value' => sprintf('Althea\PaymentFilter\Model\Rule\Condition\Customer|%s', $code),
				'label' => $label,
			];
		}

		$conditions = parent::getNewChildSelectOptions();
		$conditions = array_merge_recursive($conditions, [
			[
				'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Found',
				'label' => __('Product attribute combination'),
			],
			[
				'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Subselect',
				'label' => __('Products subselection'),
			],
			[
				'value' => $customerAttrs,
				'label' => __('Customers'),
			],
			[
				'value' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
				'label' => __('Conditions combination'),
			],
			[
				'value' => $attributes,
				'label' => __('Cart Attribute'),
			],
		]);
		$additional = new \Magento\Framework\DataObject();

		$this->_eventManager->dispatch('salesrule_rule_condition_combine', ['additional' => $additional]);

		$additionalConditions = $additional->getConditions();

		if ($additionalConditions) {

			$conditions = array_merge_recursive($conditions, $additionalConditions);
		}

		return $conditions;
	}

}