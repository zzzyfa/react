<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 5:22 PM
 */

namespace Althea\PaymentFilter\Api;

interface RuleRepositoryInterface {

	/**
	 * Get rule by id
	 *
	 * @param string $ruleId
	 * @return \Althea\PaymentFilter\Api\Data\RuleInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($ruleId);

}