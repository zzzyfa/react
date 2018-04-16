<?php

namespace Althea\AutoCancelOrder\Cron;

class Cancel
{
    /**
     * @var \Xtento\XtCore\Model\ResourceModel\Config
     */
    protected $_logger;
    protected $_autoCancelOrderModel;

    /**
     * RegisterCronExecution constructor.
     * @param \Xtento\XtCore\Model\ResourceModel\Config $xtCoreConfig
     */
    public function __construct(
        \Althea\AutoCancelOrder\Model\Cancel $autoCancelOrderModel,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_autoCancelOrderModel = $autoCancelOrderModel;
        $this->_logger = $logger;
    }

    /**
     * Register last cronjob execution
     * @return void
     */
    public function processCancel()
    {
        $this->_autoCancelOrderModel->processCancel();
    }
}
