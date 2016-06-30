<?php

namespace Blackbird\TicketBlaster\Model\Event\Attribute\Source;

class Event extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory
     */
    protected $_eventsFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Blackbird\TicketBlaster\Model\ResourceModel\Event\CollectionFactory $eventCollectionFactory,
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Blackbird\TicketBlaster\Model\ResourceModel\Event\CollectionFactory $eventsFactory
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->_eventsFactory = $eventsFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $events = $this->_createEventsCollection();

            $options[] = [
                    'label' => '',
                    'value' => '',
            ];
            
            foreach($events as $event){
                $options[] = [
                        'label' => $event->getTitle(),
                        'value' => $event->getId(),
                ];
            }
            
            $this->_options = $options;
        }
        return $this->_options;
    }

    /**
     * @return \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected function _createEventsCollection()
    {
        return $this->_eventsFactory->create();
    }
}
