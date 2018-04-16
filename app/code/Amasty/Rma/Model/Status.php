<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Model;

use Magento\Framework\Model\AbstractModel;

class Status extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rma\Model\ResourceModel\Status');
    }

    public function activate()
    {
        $this
            ->setIsActive(true)
            ->save()
        ;

        return $this;
    }

    public function deactivate()
    {
        $this
            ->setIsActive(false)
            ->save()
        ;

        return $this;
    }

    public function getStoreLabels()
    {
        if (!$this->hasData('store_labels')) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setData('store_labels', $labels);
        }

        return $this->_getData('store_labels');
    }

    public function getStoreTemplates()
    {
        if (!$this->hasData('store_templates')) {
            $templates = $this->_getResource()->getStoreTemplates($this->getId());
            $this->setData('store_templates', $templates);
        }

        return $this->_getData('store_templates');
    }

    public function getStoreLabel($storeId = 0)
    {
        $labels = $this->getStoreLabels();
        return isset($labels[$storeId])
            ? $labels[$storeId]
            :
            (isset($labels[0]) ? $labels[0] : "");
    }

    public function getStoreTemplate($storeId = 0)
    {
        $templates = $this->getStoreTemplates();
        return isset($templates[$storeId])
            ? $templates[$storeId]
            :
            (isset($templates[0]) ? $templates[0] : "");
    }
}
