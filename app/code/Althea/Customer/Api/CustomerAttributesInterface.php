<?php

namespace Althea\Customer\Api;


/**
 * Interface CustomerAttributesInterface
 *
 * @package Althea\Customer\Api
 */
interface CustomerAttributesInterface
{
     /**
     * Add follow with brand
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */

    public function getAttributes();

    /**
     * Remove follow with brand
     *
     * @param int $customerId
     * @param string $attribute_code
     * @param string $value
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function saveAttributeValue($customerId, $attribute_code, $value);

}