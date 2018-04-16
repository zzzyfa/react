<?php

namespace Potato\Zendesk\Controller\Info;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Potato\Zendesk\Api\CustomerManagementInterface;
use Potato\Zendesk\Api\OrderRecentManagementInterface;
use Potato\Zendesk\Model\Authorization;
use Potato\Zendesk\Api\OrderManagementInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Oauth\Helper\Request;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;

/**
 * Class Data
 */
class Data extends Action
{
    /** @var Authorization  */
    protected $authorization;

    /** @var OrderManagementInterface  */
    protected $orderManagement;

    /** @var CustomerManagementInterface  */
    protected $customerManagement;

    /** @var OrderRecentManagementInterface  */
    protected $orderRecentManagement;

    /** @var null  */
    private $postData = null;

    /** @var LoggerInterface  */
    protected $logger;

    /**
     * Data constructor.
     * @param Context $context
     * @param Authorization $authorization
     * @param OrderManagementInterface $orderManagement
     * @param CustomerManagementInterface $customerManagement
     * @param OrderRecentManagementInterface $orderRecentManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Authorization $authorization,
        OrderManagementInterface $orderManagement,
        CustomerManagementInterface $customerManagement,
        OrderRecentManagementInterface $orderRecentManagement,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->authorization = $authorization;
        $this->orderManagement = $orderManagement;
        $this->customerManagement = $customerManagement;
        $this->orderRecentManagement = $orderRecentManagement;
        $this->logger = $logger;
    }

    /**
     * @return mixed|null
     */
    private function getPostData()
    {
        if (null !== $this->postData) {
            return $this->postData;
        }
        $this->postData = file_get_contents('php://input');
        if (false === $this->postData) {
            $this->logger->error(__('Invalid POST data'));
            return $this->postData = null;
        }
        $this->postData = json_decode($this->postData, true);
        if (null === $this->postData) {
            $this->logger->error(__('Invalid JSON'));
        }
        return $this->postData;
    }
    
    /**
     * Check authorization with Zendesk account
     * @return bool
     */
    private function authorise()
    {
        return $this->authorization->isAuth($this->getPostData());
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $scope = $this->authorise();
        if (null === $scope) {
            $resultJson->setHttpResponseCode(Request::HTTP_UNAUTHORIZED);
            return $resultJson->setData($scope);
        }
        $customerInfo = $this->getCustomerInfo($scope);
        $orderInfo = $this->getOrderInfo($scope);
        $recentOrderInfo = $this->getRecentOrderInfo($scope);

        if (!$customerInfo && !$orderInfo && !$recentOrderInfo) {
            $resultJson->setHttpResponseCode(Request::HTTP_BAD_REQUEST);
            return $resultJson->setData([]);
        }
        return $resultJson->setData(array_merge($customerInfo, $orderInfo, $recentOrderInfo));
    }

    /**
     * @param integer|Website|Store $scope
     * @return array
     */
    private function getCustomerInfo($scope)
    {
        $result = [];
        $postData = $this->getPostData();
        if (null === $postData || !isset($postData['email'])) {
            return $result;
        }
        
        $customerInfo = $this->customerManagement->getInfo($postData['email'], $scope);
        if (!$customerInfo && isset($postData['order_id'])) {
            $customerInfo = $this->customerManagement->getInfoFromOrder($postData['email'], $postData['order_id'], $scope);
        }
        if ($customerInfo) {
            $result = ['customer_list' => $customerInfo];
        }
        return $result;
    }

    /**
     * @param integer|Website|Store $scope
     * @return array
     */
    private function getOrderInfo($scope)
    {
        $result = [];
        $postData = $this->getPostData();
        if (null === $postData || !isset($postData['order_id'])) {
            return $result;
        }

        $orderInfo = $this->orderManagement->getInfo($postData['order_id'], $scope);
        if ($orderInfo) {
            $result = ['order' => $orderInfo];
        }
        return $result;
    }

    /**
     * @param integer|Website|Store $scope
     * @return array
     */
    private function getRecentOrderInfo($scope)
    {
        $result = [];
        $postData = $this->getPostData();
        if (null === $postData || !isset($postData['email'])) {
            return $result;
        }

        $orderItemInfo = $this->orderRecentManagement->getInfo($postData['email'], $scope);
        if ($orderItemInfo) {
            $result = ['order_list' => $orderItemInfo];
        }
        return $result;
    }
}