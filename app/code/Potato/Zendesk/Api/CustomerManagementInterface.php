<?php

namespace Potato\Zendesk\Api;

/**
 * @api
 */
interface CustomerManagementInterface
{
    /**
     * @param string $email
     * @param integer|\Magento\Store\Model\Website|\Magento\Store\Model\Store $scope
     * @return array
     */
    public function getInfo($email, $scope);

    /**
     * @param string $email
     * @param string $incrementId
     * @param integer|\Magento\Store\Model\Website|\Magento\Store\Model\Store $scope
     * @return array
     */
    public function getInfoFromOrder($email, $incrementId, $scope);
}
