<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Althea\Store\Model\Service;

use Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Store\Model\ScopeInterface;

class StoreConfigManager implements \Althea\Store\Api\StoreConfigManagerInterface
{

    /*
     * Path to config value, which lists countries, for which state is required.
     */
    const XML_PATH_STATES_REQUIRED = 'general/region/state_required';

    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory
     */
    protected $storeCollectionFactory;

    /**
     * @var \Althea\Store\Model\Data\StoreConfigFactory
     */
    protected $storeConfigFactory;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Map the setters to config path
     *
     * @var array
     */
    protected $configPaths = [
        'setLocale' => 'general/locale/code',
        'setBaseCurrencyCode' => 'currency/options/base',
        'setDefaultDisplayCurrencyCode' => 'currency/options/default',
        'setTimezone' => 'general/locale/timezone',
        'setWeightUnit' => \Magento\Directory\Helper\Data::XML_PATH_WEIGHT_UNIT
    ];

    protected $statesRequiredCountyList;
    /**
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Althea\Store\Model\Data\StoreConfigFactory $storeConfigFactory
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Althea\Store\Model\Data\StoreConfigFactory $storeConfigFactory
    )
    {
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeConfigFactory = $storeConfigFactory;

       // $this->directoryHelper = $directoryHelper;
    }

    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Store\Model\ResourceModel\Store\Collection $collection)
    {
        $fields = [];
        $values = [];
        $categoryFilter = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

            if ($filter->getField() == 'root_category_id') {
                $categoryFilter[$conditionType][] = $filter->getValue();
                continue;
            }

            $fields[] = ['attribute' => $filter->getField()];
            $values[] = [$conditionType => $filter->getValue()];
        }

        if ($categoryFilter) {
            $collection->addCategoryFilter($categoryFilter);
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $values);
        }
    }

    public function getStoredStatesRequiredConfig()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('core_config_data');

        $sql = "Select * FROM " . $tableName . " WHERE path = '".self::XML_PATH_STATES_REQUIRED."'";

        $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.
        $this->statesRequiredCountyList = $result[0]['value'];
    }

    public function getStoreConfigsByFilter(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $this->getStoredStatesRequiredConfig();

        $storeConfigs = [];
        $storeCollection = $this->storeCollectionFactory->create();

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $storeCollection);
        }

        foreach ($storeCollection->load() as $item) {
            $storeConfigs[] = $this->getStoreConfig($item);
        }

        return $storeConfigs;
    }

    /**
     * @param string[] $storeCodes list of stores by store codes, will return all if storeCodes is not set
     * @return \Magento\Store\Api\Data\StoreConfigInterface[]
     */
    public function getStoreConfigs(array $storeCodes = null)
    {
        $storeConfigs = [];
        $storeCollection = $this->storeCollectionFactory->create();
        if ($storeCodes != null) {
            $storeCollection->addFieldToFilter('code', ['in' => $storeCodes]);
        }

        foreach ($storeCollection->load() as $item) {
            $storeConfigs[] = $this->getStoreConfig($item);
        }

        return $storeConfigs;
    }

    /**
     * Returns the list of countries, for which region is required
     *
     * @param boolean $asJson
     * @return array
     */
    public function getCountriesWithStatesRequired($store)
    {
        $value = trim(
            $this->scopeConfig->get(
                'general/region/state_required',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
                $store->getCode()
            )
        );

        $countryList = preg_split('/\,/', $value, 0, PREG_SPLIT_NO_EMPTY);

        return $countryList;
    }

    /**
     * Returns flag, which indicates whether region is required for specified country
     *
     * @param string $countryId
     * @return bool
     */
    public function isRegionRequired($countryId)
    {
        //$countyList = $this->getCountriesWithStatesRequired($store);
        $countyList = $this->statesRequiredCountyList;
        $countyList = explode(',', $countyList);

        if (!is_array($countyList)) {
            return false;
        }

        return in_array($countryId, $countyList);
        //return boolval(in_array($countryId, $countyList)) ? 'true' : 'false';
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return \Althea\Store\Api\Data\StoreConfigInterface
     */
    protected function getStoreConfig($store)
    {
        /** @var \Althea\Store\Model\Data\StoreConfig $storeConfig */
        $storeConfig = $this->storeConfigFactory->create();

        $storeConfig->setId($store->getId())
            ->setCode($store->getCode())
            ->setWebsiteId($store->getWebsiteId());

        foreach ($this->configPaths as $methodName => $configPath) {
            $configValue = $this->scopeConfig->getValue(
                $configPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
                $store->getCode()
            );

            $storeConfig->$methodName($configValue);
        }

        $storeConfig->setBaseUrl($store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, false));
        $storeConfig->setSecureBaseUrl($store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, true));
        $storeConfig->setBaseLinkUrl($store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, false));
        $storeConfig->setSecureBaseLinkUrl($store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true));
        $storeConfig->setBaseStaticUrl($store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC, false));
        $storeConfig->setSecureBaseStaticUrl(
            $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC, true)
        );
        $storeConfig->setBaseMediaUrl($store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, false));
        $storeConfig->setSecureBaseMediaUrl(
            $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, true)
        );

        $storeConfig->setStateRequired($this->isRegionRequired(strtoupper(substr($storeConfig->getLocale(),0,2))));

        return $storeConfig;
    }
}
