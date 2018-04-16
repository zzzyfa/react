<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Plugin\Controller;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\ObjectManagerInterface;

class Request
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Amasty\Rma\Model\Session
     */
    protected $rmaSession;

    /**
     * Request constructor.
     *
     * @param RedirectFactory           $resultRedirectFactory
     * @param ObjectManagerInterface    $objectManager
     * @param \Amasty\Rma\Model\Session $rmaSession
     */
    public function __construct(
        RedirectFactory $resultRedirectFactory,
        ObjectManagerInterface $objectManager,
        \Amasty\Rma\Model\Session $rmaSession
    )
    {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->objectManager = $objectManager;
        $this->rmaSession = $rmaSession;
    }

    public function aroundExecute(
        \Amasty\Rma\Controller\Request $subject,
        \Closure $proceed
    ) {
        $code = $subject->getRequest()->getParam('code');

        if ($code) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            /** @var \Amasty\Rma\Model\Request $request */
            $request = $this->objectManager->create('\Amasty\Rma\Model\Request');

            $request->load($code, 'code');

            if (!$request->getId()) {
                return $resultRedirect->setPath('/');
            }

            $order = $request->getOrder();
            $this->rmaSession->setOrder($order);

            $params = $subject->getRequest()->getParams();
            unset($params['code']);
            return $resultRedirect->setPath('*/*/*', $params);
        }

        return $proceed();
    }
}
