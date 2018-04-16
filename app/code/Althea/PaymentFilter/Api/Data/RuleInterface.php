<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 12:16 PM
 */

namespace Althea\PaymentFilter\Api\Data;

interface RuleInterface {

	/**#@+
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const RULE_ID               = 'rule_id';
	const NAME                  = 'name';
	const STATUS                = 'status';
	const CONDITIONS_SERIALIZED = 'conditions_serialized';
	const PAYMENT_METHOD        = 'payment_method';
	const SHIPPING_METHOD       = 'shipping_method';
	const PRIORITY              = 'priority';
	const STOP_RULES_PROCESSING = 'stop_rules_processing';
	const CREATED_AT            = 'created_at';
	const UPDATED_AT            = 'updated_at';
	/**#@-*/

	/**
	 * Get ID
	 *
	 * @return int|null
	 */
	public function getId();

	/**
	 * Get name
	 *
	 * @return string|null
	 */
	public function getName();

	/**
	 * Get status
	 *
	 * @return bool|null
	 */
	public function getStatus();

	/**
	 * Get serialized conditions
	 *
	 * @return string|null
	 */
	public function getConditionsSerialized();

	/**
	 * Get payment method
	 *
	 * @return string|null
	 */
	public function getPaymentMethod();

	/**
	 * Get shipping method
	 *
	 * @return string|null
	 */
	public function getShippingMethod();

	/**
	 * Get priority
	 *
	 * @return int|null
	 */
	public function getPriority();

	/**
	 * Get stop rules processing
	 *
	 * @return bool|null
	 */
	public function getStopRulesProcessing();

	/**
	 * Get created at
	 *
	 * @return string|null
	 */
	public function getCreatedAt();

	/**
	 * Get updated at
	 *
	 * @return string|null
	 */
	public function getUpdatedAt();

	/**
	 * Set ID
	 *
	 * @param int $id
	 * @return RuleInterface
	 */
	public function setId($id);

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return RuleInterface
	 */
	public function setName($name);

	/**
	 * Set name
	 *
	 * @param bool $status
	 * @return RuleInterface
	 */
	public function setStatus($status);

	/**
	 * Set serialized conditions
	 *
	 * @param string $conditions
	 * @return RuleInterface
	 */
	public function setConditionsSerialized($conditions);

	/**
	 * Set payment method
	 *
	 * @param string $method
	 * @return RuleInterface
	 */
	public function setPaymentMethod($method);

	/**
	 * Set shipping method
	 *
	 * @param string $method
	 * @return RuleInterface
	 */
	public function setShippingMethod($method);

	/**
	 * Set priority
	 *
	 * @param int $priority
	 * @return RuleInterface
	 */
	public function setPriority($priority);

	/**
	 * Set priority
	 *
	 * @param bool $stopRulesProcessing
	 * @return RuleInterface
	 */
	public function setStopRulesProcessing($stopRulesProcessing);

	/**
	 * Set creation time
	 *
	 * @param string $createdAt
	 * @return RuleInterface
	 */
	public function setCreatedAt($createdAt);

	/**
	 * Set update time
	 *
	 * @param string $updatedAt
	 * @return RuleInterface
	 */
	public function setUpdatedAt($updatedAt);

}