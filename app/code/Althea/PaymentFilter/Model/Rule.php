<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 12:31 PM
 */

namespace Althea\PaymentFilter\Model;

use Althea\PaymentFilter\Api\Data\RuleInterface;
use Althea\PaymentFilter\Model\Rule\Condition\CombineFactory;
use Magento\Rule\Model\AbstractModel;

class Rule extends AbstractModel implements RuleInterface {

	/**
	 * rule cache tag
	 */
	const CACHE_TAG = 'althea_paymentfilter_rule';

	/**#@+
	 * Rule's statuses
	 */
	const STATUS_ENABLED  = 1;
	const STATUS_DISABLED = 0;

	/**#@-*/
	/**
	 * @var string
	 */
	protected $_cacheTag = 'althea_paymentfilter_rule';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'althea_paymentfilter_rule';

	protected $_condCombineFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		CombineFactory $condCombineFactory,
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
		$resource = null,
		$resourceCollection = null,
		array $data = []
	)
	{
		$this->_condCombineFactory = $condCombineFactory;

		parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
	}

	/**
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Althea\PaymentFilter\Model\ResourceModel\Rule');
	}

	/**
	 * @inheritDoc
	 */
	public function getConditionsInstance()
	{
		return $this->_condCombineFactory->create();
	}

	/**
	 * @inheritDoc
	 */
	public function getActionsInstance()
	{
		return $this->_condCombineFactory->create();
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->getData(self::RULE_ID);
	}

	/**
	 * Get name
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return $this->getData(self::NAME);
	}

	/**
	 * Get status
	 *
	 * @return bool|null
	 */
	public function getStatus()
	{
		return $this->getData(self::STATUS);
	}

	/**
	 * Get serialized conditions
	 *
	 * @return string|null
	 */
	public function getConditionsSerialized()
	{
		return $this->getData(self::CONDITIONS_SERIALIZED);
	}

	/**
	 * Get payment method
	 *
	 * @return string|null
	 */
	public function getPaymentMethod()
	{
		return $this->getData(self::PAYMENT_METHOD);
	}

	/**
	 * Get shipping method
	 *
	 * @return string|null
	 */
	public function getShippingMethod()
	{
		return $this->getData(self::SHIPPING_METHOD);
	}

	/**
	 * Get priority
	 *
	 * @return int|null
	 */
	public function getPriority()
	{
		return $this->getData(self::PRIORITY);
	}

	/**
	 * Get stop rules processing
	 *
	 * @return bool|null
	 */
	public function getStopRulesProcessing()
	{
		return $this->getData(self::STOP_RULES_PROCESSING);
	}

	/**
	 * Get created at
	 *
	 * @return string|null
	 */
	public function getCreatedAt()
	{
		return $this->getData(self::CREATED_AT);
	}

	/**
	 * Get updated at
	 *
	 * @return string|null
	 */
	public function getUpdatedAt()
	{
		return $this->getData(self::UPDATED_AT);
	}

	/**
	 * Set rule id
	 *
	 * @param int $id
	 * @return RuleInterface
	 */
	public function setId($id)
	{
		return $this->setData(self::RULE_ID, $id);
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return RuleInterface
	 */
	public function setName($name)
	{
		return $this->setData(self::NAME, $name);
	}

	/**
	 * Set status
	 *
	 * @param bool $status
	 * @return RuleInterface
	 */
	public function setStatus($status)
	{
		return $this->setData(self::STATUS, $status);
	}

	/**
	 * Set serialized conditions
	 *
	 * @param string $conditions
	 * @return RuleInterface
	 */
	public function setConditionsSerialized($conditions)
	{
		return $this->setData(self::CONDITIONS_SERIALIZED, $conditions);
	}

	/**
	 * Set payment method
	 *
	 * @param string $method
	 * @return RuleInterface
	 */
	public function setPaymentMethod($method)
	{
		return $this->setData(self::PAYMENT_METHOD, $method);
	}

	/**
	 * Set shipping method
	 *
	 * @param string $method
	 * @return RuleInterface
	 */
	public function setShippingMethod($method)
	{
		return $this->setData(self::SHIPPING_METHOD, $method);
	}

	/**
	 * Set priority
	 *
	 * @param int $priority
	 * @return RuleInterface
	 */
	public function setPriority($priority)
	{
		return $this->setData(self::PRIORITY, $priority);
	}

	/**
	 * Set stop rules processing
	 *
	 * @param bool $stopRulesProcessing
	 * @return RuleInterface
	 */
	public function setStopRulesProcessing($stopRulesProcessing)
	{
		return $this->setData(self::STOP_RULES_PROCESSING, $stopRulesProcessing);
	}

	/**
	 * Set created at
	 *
	 * @param string $createdAt
	 * @return RuleInterface
	 */
	public function setCreatedAt($createdAt)
	{
		return $this->setData(self::CREATED_AT, $createdAt);
	}

	/**
	 * Set updated at
	 *
	 * @param string $updatedAt
	 * @return RuleInterface
	 */
	public function setUpdatedAt($updatedAt)
	{
		return $this->setData(self::UPDATED_AT, $updatedAt);
	}

	/**
	 * Receive rule store ids
	 *
	 * @return int[]
	 */
	public function getStores()
	{
		return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
	}

	/**
	 * Prepare banner's statuses.
	 *
	 * @return array
	 */
	public function getAvailableStatuses()
	{
		return [
			self::STATUS_ENABLED  => __('Enabled'),
			self::STATUS_DISABLED => __('Disabled'),
		];
	}

	/**
	 * @param string $formName
	 * @return string
	 */
	public function getConditionsFieldSetId($formName = '')
	{
		return $formName . 'rule_conditions_fieldset_' . $this->getId();
	}

	/**
	 * @return array
	 */
	public function getPaymentMethods()
	{
		return explode(",", $this->getPaymentMethod());
	}

	/**
	 * @return array
	 */
	public function getShippingMethods()
	{
		return explode(",", $this->getShippingMethod());
	}

}