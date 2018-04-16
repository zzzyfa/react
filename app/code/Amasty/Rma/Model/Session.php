<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Model;

use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Session\ValidatorInterface;
use Magento\Sales\Model\Order;


class Session extends \Magento\Framework\Session\SessionManager
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\App\Request\Http                    $request
     * @param SidResolverInterface                                   $sidResolver
     * @param ConfigInterface                                        $sessionConfig
     * @param SaveHandlerInterface                                   $saveHandler
     * @param ValidatorInterface                                     $validator
     * @param StorageInterface                                       $storage
     * @param \Magento\Framework\Stdlib\CookieManagerInterface       $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\State                           $appState
     *
     * @param \Magento\Framework\ObjectManagerInterface              $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        SidResolverInterface $sidResolver,
        ConfigInterface $sessionConfig,
        SaveHandlerInterface $saveHandler,
        ValidatorInterface $validator,
        StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,

        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct(
            $request, $sidResolver, $sessionConfig, $saveHandler, $validator, $storage, $cookieManager,
            $cookieMetadataFactory, $appState
        );
        $this->objectManager = $objectManager;
    }

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @param Comment $comment
     *
     * @return bool
     */
    public function loginByComment(Comment $comment)
    {
        if ($order = $comment->authenticate()) {
            $this->setOrder($order);
            
            return true;
        }
        return false;
    }

    public function logout()
    {
        $this->storage->unsetData('id');

        return $this;
    }

    /**
     * @param Order $order
     *
     * @return $this
     */
    public function setOrder(Order $order)
    {
        $this->storage->setData('id', $order->getId());

        $this->_order = $order;

        return $this;
    }

    public function getOrder()
    {
        $id = (int)$this->getId();
        if (!$this->_order && $id > 0) {

            $this->_order = $this->objectManager->create(
                '\Magento\Sales\Model\Order'
            );

            $this->_order->load($id);
        }

        return $this->_order;
    }

    public function getId()
    {
        return $this->storage->getData('id');
    }

    public function isLoggedIn()
    {
        return (bool)$this->getId();
    }
}
