<?php

namespace Blackbird\TicketBlaster\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Ticket extends \Magento\Catalog\Model\Product\Type\Virtual
{
    protected $_logger;
    protected $_event;
    protected $_eventFactory;
    protected $_localeDate;
    
    const TYPE_CODE = 'ticket';
    
    public function __construct(
            \Magento\Catalog\Model\Product\Option $catalogProductOption,
            \Magento\Eav\Model\Config $eavConfig,
            \Magento\Catalog\Model\Product\Type $catalogProductType,
            \Magento\Framework\Event\ManagerInterface $eventManager,
            \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
            \Magento\Framework\Filesystem $filesystem,
            \Magento\Framework\Registry $coreRegistry,
            \Psr\Log\LoggerInterface $logger,
            ProductRepositoryInterface $productRepository,
            \Blackbird\TicketBlaster\Model\EventFactory $eventFactory,
            \Magento\Framework\View\Element\Context $context
    ) {

        $this->_logger = $logger;
        $this->_eventFactory = $eventFactory;
        $this->_localeDate = $context->getLocaleDate();
        parent::__construct($catalogProductOption, $eavConfig, $catalogProductType, $eventManager, $fileStorageDb, $filesystem, $coreRegistry, $logger, $productRepository);
    }
    
    public function doSomething(){
        $this->_logger->warning('This is a warning level log');
    }

    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product){
        
    }
    
    public function isSalable($product)
    {
        $isSalable = parent::isSalable($product);
        return ($isSalable && $this->isEventSalable($product));
    }
    
    public function isEventSalable($product)
    {
        $event = $this->getEvent($product);
    
        $todayDateTime = $this->_localeDate->date();
        $eventDate = $this->_localeDate->date(strtotime($event->getEventTime()));
    
        return $todayDateTime < $eventDate;
    }
    
    public function getEvent($product)
    {
        if(is_null($this->_event)){
            $eventObject = $this->_eventFactory->create();
            
            $this->_event = $eventObject->getCollection()
                ->addFieldToFilter('event_id', $product->getEventLink())
                ->getFirstItem();
        }
            
        return $this->_event;
    }

}