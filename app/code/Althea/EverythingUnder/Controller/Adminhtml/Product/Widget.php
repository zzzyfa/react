<?php
namespace Althea\EverythingUnder\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;

abstract class Widget extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Althea_EverythingUnder::widget_instance';
}
