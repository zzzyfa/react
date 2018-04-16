<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Injection;

class Redirect extends \Magento\Framework\Controller\Result\Redirect
{
    protected $map = [
        'sales/order/history' => 'amasty_rma/guest/history',
        'sales/guest/form' => 'amasty_rma/guest/login',
    ];

    public function setPath($path, array $params = [])
    {
        if (isset($this->map[$path])) {
            $path = $this->map[$path];
        }

        return parent::setPath($path, $params);
    }
}
