<?php
/**
 * This file is part of the Sulaeman Paypal Web Service package.
 *
 * @author Sulaeman <me@sulaeman.com>
 */
namespace Althea\PaypalWebService\Plugin\Paypal\Model;

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Paypal\Model\Express as PaypalExpress;
use Magento\Sales\Model\Order;
use Magento\Payment\Model\InfoInterface;

class Express
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var array
     */
    private $_areas = [
        \Magento\Framework\App\Area::AREA_WEBAPI_REST,
        \Magento\Framework\App\Area::AREA_WEBAPI_SOAP
    ];
    /**
     * @var array
     */
    private $_paymentPayload = null;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(Context $context, Registry $registry)
    {
        $this->_appState = $context->getAppState();
        $this->_logger = $context->getLogger();
        $this->_registry = $registry;
    }

    /**
     * Interceptor capture.
     *
     * @param \Magento\Paypal\Model\Express $subject
     *
     * {@inheritdoc}
     */
    public function aroundCapture(
        PaypalExpress $subject,
        \Closure $proceed,
        InfoInterface $payment,
        $amount
    )
    {
        if (in_array($this->_appState->getAreaCode(), $this->_areas)
            && $this->_paymentPayload != null
        ) {
            $payment->setLastTransId($this->_paymentPayload['id']);
            $payment->setTransactionId($this->_paymentPayload['id']);
            foreach ($this->_paymentPayload as $key => $value) {
                $payment->setTransactionAdditionalInfo($key, $value);
            }
            $payment->setIsTransactionPending(false);
        }
        return $proceed($payment, $amount);
    }

    /**
     * Interceptor assignData.
     *
     * @param \Magento\Paypal\Model\Express $subject
     *
     * {@inheritdoc}
     */
    public function beforeAssignData(
        PaypalExpress $subject,
        \Magento\Framework\DataObject $data
    )
    {
        if (in_array($this->_appState->getAreaCode(), $this->_areas)) {
            $additionalData = $data->getData('additional_data');
            if ($additionalData) {
                if (isset($additionalData['paypal_express_payment_payload'])) {
                    $this->_paymentPayload = json_decode($additionalData['paypal_express_payment_payload'], true);
                }
            }
        }
        return [$data];
    }
}