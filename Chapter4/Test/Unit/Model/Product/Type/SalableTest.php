<?php
namespace Blackbird\TicketBlaster\Test\Unit\Model\Product\Type;

class SalableTest extends \PHPUnit_Framework_TestCase
{
    CONST PRODUCT_ID = 1;
    protected $_model;
    protected $_productObject;
    protected $_context;
    protected $_eventFactory;
    protected $_event;

    protected function setUp()
    {
        $objectHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $coreRegistryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $fileStorageDbMock = $this->getMock('Magento\MediaStorage\Helper\File\Storage\Database', [], [], '', false);
        $filesystem = $this->getMockBuilder('Magento\Framework\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $productFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', [], [], '', false);
        $this->_model = $objectHelper->getObject(
            'Magento\Catalog\Model\Product\Type\Virtual',
            [
                'eventManager' => $eventManager,
                'fileStorageDb' => $fileStorageDbMock,
                'filesystem' => $filesystem,
                'coreRegistry' => $coreRegistryMock,
                'logger' => $logger,
                'productFactory' => $productFactoryMock
            ]
        );
        
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->eventManagerMock = $this->getMockBuilder('Magento\Framework\Event\ManagerInterface')
        ->disableOriginalConstructor()
        ->getMock();
        $this->context = $objectManager->getObject(
                'Magento\Framework\Model\Context',
                [
                        'eventDispatcher' => $this->eventManagerMock
                ]
        );
        
        $this->_eventFactory = $objectHelper->getObject(
            '\Blackbird\TicketBlaster\Model\EventFactory',
            []
        );
        
        $this->productObject = $this->_model->load($this->PRODUCT_ID);
        
        $this->_event = $this->_eventFactory->create()->getCollection()
                ->addFieldToFilter('event_id', $this->productObject->getEventLink())
                ->getFirstItem();
        
    }
    
    /**
     * @covers \Blackbird\TicketBlaster\Model\Product\Type::isSalable()
     */
    public function testProductIsalable()
    {
        $entity_id = 1;

        $yesterdayDateTime = $context->getLocaleDate()->date()->sub(1, Zend_Date::DAY);
        
        $this->_event->setEventTime($yesterdayDateTime);
        $this->_event->save();

        $this->assertTrue($this->productObject->isSalable());
    }
}
