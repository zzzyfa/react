<?php

namespace Potato\Zendesk\Api;

/**
 * @api
 */
interface OrderManagementInterface
{
    /**
     * @param int $orderIncrementId
     * @param integer|\Magento\Store\Model\Website|\Magento\Store\Model\Store $scope
     * @return array
     */
    public function getInfo($orderIncrementId, $scope);
}
