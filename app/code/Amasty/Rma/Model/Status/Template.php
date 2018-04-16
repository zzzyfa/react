<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Model\Status;

use Magento\Framework\Model\AbstractModel;

class Template extends AbstractModel
{
    const TEMPLATE_CODE = 'amrma_email_templates_status_changed';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rma\Model\ResourceModel\Status\Template');
    }
}
