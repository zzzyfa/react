<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 14/09/2017
 * Time: 2:47 PM
 */

namespace Althea\Cms\Api;


/**
 * @api
 */
interface PageManagementInterface
{
    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Cms\Api\Data\PageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByIdentifier(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
