<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Guest;

class Login extends \Magento\Sales\Block\Widget\Guest\Form
{
    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl(
            'amasty_rma/guest/loginPost',
            ['_secure' => true]
        );
    }
}
