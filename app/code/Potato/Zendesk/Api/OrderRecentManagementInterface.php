<?php

namespace Potato\Zendesk\Api;

/**
 * @api
 */
interface OrderRecentManagementInterface
{
    /**
     * @param string $email
     * @param integer|\Magento\Store\Model\Website|\Magento\Store\Model\Store $scope
     * @return array
     */
    public function getInfo($email, $scope);
}
