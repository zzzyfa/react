<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Model\System\Config\Backend;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Properties extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * Fix Magento's bug with arrays in default config
     * @return string
     */
    public function getOldValue()
    {
        $value = $this->_config->getValue(
            $this->getPath(),
            $this->getScope() ?: ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->getScopeCode()
        );

        if (is_array($value)) {
            $value = serialize($value);
        }

        return (string)$value;
    }
}
