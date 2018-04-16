<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Guest;

use Magento\Framework\App\Action\Context;

class Logout extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Amasty\Rma\Model\Session
     */
    protected $rmaSession;

    /**
     * @param Context                   $context
     * @param \Amasty\Rma\Model\Session $rmaSession
     */
    public function __construct(
        Context $context,
        \Amasty\Rma\Model\Session $rmaSession
    ) {
        parent::__construct($context);
        $this->rmaSession = $rmaSession;
    }

    public function execute()
    {
        $this->rmaSession->logout();
        return $this->_redirect('*/*/login');
    }
}
