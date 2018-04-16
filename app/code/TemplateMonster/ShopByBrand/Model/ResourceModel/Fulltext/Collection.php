<?php

namespace TemplateMonster\ShopByBrand\Model\ResourceModel\Fulltext;

class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    protected function _renderFiltersBefore(){
        parent::_renderFiltersBefore();
        $this->_totalRecords = null;
    }
}