<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\Store\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreIsInactiveException;

/**
 * Store config manager interface
 *
 * @api
 */
interface StoreConfigManagerInterface
{
    /**
     * @param string[] $storeCodes
     * @return \Magento\Store\Api\Data\StoreConfigInterface[]
     */
    public function getStoreConfigs(array $storeCodes = null);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Althea\Store\Api\Data\StoreConfigInterface[]
     */
    public function getStoreConfigsByFilter(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
