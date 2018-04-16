<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 14/09/2017
 * Time: 2:56 PM
 */

namespace Althea\Cms\Model;


use Magento\Cms\Api\Data;
use Althea\Cms\Api\PageManagementInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\ResourceModel\Page as ResourcePage;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;


//* @method Page setStoreId(int $storeId)

class PageManagement extends \Magento\Cms\Model\PageRepository implements \Althea\Cms\Api\PageManagementInterface
{
    /**
     * @var ResourcePage
     */
    protected $resource;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var PageCollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @var Data\PageSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Cms\Api\Data\PageInterfaceFactory
     */
    protected $dataPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourcePage $resource
     * @param PageFactory $pageFactory
     * @param Data\PageInterfaceFactory $dataPageFactory
     * @param PageCollectionFactory $pageCollectionFactory
     * @param Data\PageSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ResourcePage $resource, PageFactory $pageFactory, Data\PageInterfaceFactory $dataPageFactory, PageCollectionFactory $pageCollectionFactory, Data\PageSearchResultsInterfaceFactory $searchResultsFactory, DataObjectHelper $dataObjectHelper, DataObjectProcessor $dataObjectProcessor, StoreManagerInterface $storeManager)
    {
        parent::__construct($resource, $pageFactory, $dataPageFactory, $pageCollectionFactory, $searchResultsFactory, $dataObjectHelper, $dataObjectProcessor, $storeManager);
    }


    /**
     * Load Page data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    public function getByIdentifier(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->pageCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter1) {
                if ($filter1->getField() === 'identifier') {
                    $collection->addStoreFilter($filter1->getValue(), false);
                    continue;
                }
                $condition = $filter1->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter1->getField(), [$condition => $filter1->getValue()]);
            }
            foreach ($filterGroup->getFilters() as $filter2) {
                if ($filter2->getField() === 'store_id') {
                    $collection->addStoreFilter($filter2->getValue(), false);
                    continue;
                }
                $condition = $filter2->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter2->getField(), [$condition => $filter2->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $pages = [];
        /** @var Page $pageModel */
        foreach ($collection as $pageModel) {
            $pageData = $this->dataPageFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $pageData,
                $pageModel->getData(),
                'Magento\Cms\Api\Data\PageInterface'
            );
            $pages[] = $this->dataObjectProcessor->buildOutputDataArray(
                $pageData,
                'Magento\Cms\Api\Data\PageInterface'
            );
        }
        $searchResults->setItems($pages);
        return $searchResults;
    }

}