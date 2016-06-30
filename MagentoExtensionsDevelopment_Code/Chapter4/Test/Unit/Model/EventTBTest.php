<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blackbird\TicketBlaster\Test\Unit\Model;

class EventTBTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Blackbird\TicketBlaster\Model\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $thisMock;

    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \Blackbird\TicketBlaster\Model\ResourceModel\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceEventMock;

    protected function setUp()
    {
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
        $this->resourceEventMock = $this->getMockBuilder('Blackbird\TicketBlaster\Model\ResourceModel\Event')
        ->disableOriginalConstructor()
        ->setMethods(
                [
                        'checkUrlKey'
                ]
        )
        ->getMock();
        $this->thisMock = $this->getMockBuilder('Blackbird\TicketBlaster\Model\Event')
        ->setConstructorArgs(
                [
                        $this->context,
                        $this->getMockBuilder('Magento\Framework\Registry')
                        ->disableOriginalConstructor()
                        ->getMock(),
                        $this->getMockBuilder('\Magento\Framework\UrlInterface')
                        ->disableOriginalConstructor()
                        ->getMock(),
                        $this->getMockBuilder('Magento\Framework\Model\ResourceModel\AbstractResource')
                        ->disableOriginalConstructor()
                        ->setMethods(
                                [
                                        '_construct',
                                        'getConnection',
                                ]
                        )
                        ->getMockForAbstractClass(),
                        $this->getMockBuilder('Magento\Framework\Data\Collection\AbstractDb')
                        ->disableOriginalConstructor()
                        ->getMockForAbstractClass(),
                ]
        )
        ->setMethods(
                [
                        '_construct',
                        '_getResource',
                        'load',
                ]
        )
        ->getMock();

        $this->thisMock->expects($this->any())
        ->method('_getResource')
        ->willReturn($this->resourceEventMock);
        $this->thisMock->expects($this->any())
        ->method('load')
        ->willReturnSelf();
    }
    
    /**
     * @covers \Blackbird\TicketBlaster\Model\Event::checkUrlKey
     */
    public function testCheckUrlKey()
    {
        $url_key = 'some_key';
        $fetchOneResult = 'some result';

        $this->resourceEventMock->expects($this->atLeastOnce())
        ->method('checkUrlKey')
        ->with($url_key)
        ->willReturn($fetchOneResult);

        $this->assertInternalType('string', $this->thisMock->checkUrlKey($url_key));
    }
}
