<?php
namespace Althea\AutoCancelOrder\Test\Unit\Model;

class AutoCancelOrderTest extends \PHPUnit_Framework_TestCase
{
    const targetOrderId = 402963;
    protected $_autoCancelOrderModel;
    protected $_orderModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_orderCollectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_autoCancelOrderCollectionFactoryMock;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;


    public function setUp()
    {
        $_objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $context = $this->getMock('Magento\Framework\Model\Context', ['getEventDispatcher'], [], '', false);

        $this->_orderCollectionFactoryMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Order\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->_autoCancelOrderCollectionFactoryMock = $this->getMock(
            '\Althea\AutoCancelOrder\Model\ResourceModel\AutoCancelOrder\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->_autoCancelOrderModel = $_objectManager->getObject('Althea\AutoCancelOrder\Model\AutoCancelOrder',
            [
                'context' => $context,
                'orderCollectionFactory' => $this->_orderCollectionFactoryMock,
                'autoCancelOrderCollectionFactory' => $this->_autoCancelOrderCollectionFactoryMock
            ]
        );
//        $this->_orderModel = $objectManager->getObject('Magento\Sales\Model\Order');
    }

    public function testProcessCancel(){
//        $result = $this->_autoCancelOrderModel->processCancel();
//        $this->assertEquals(!is_null($result), true);
    }

    public function testRegisterCancel(){
//        $result = $this->_autoCancelOrderModel->processCancel($this->_orderModel);
//        $this->assertEquals(!is_null($result), true);
    }
}