<?php

namespace Blackbird\TicketBlaster\Block;

use Blackbird\TicketBlaster\Api\Data\EventInterface;
use Blackbird\TicketBlaster\Model\ResourceModel\Event\Collection as EventCollection;
use Magento\Framework\ObjectManagerInterface;

class EventView extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface
{

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Blackbird\TicketBlaster\Model\Event $event
     * @param \Blackbird\TicketBlaster\Model\EventFactory $eventFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Blackbird\TicketBlaster\Model\Event $event,
        \Blackbird\TicketBlaster\Model\EventFactory $eventFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_event = $event;
        $this->_eventFactory = $eventFactory;
    }

    /**
     * @return \Blackbird\TicketBlaster\Model\Event
     */
    public function getEvent()
    {
        if (!$this->hasData('event')) {
            if ($this->getEventId()) {
                /** @var \Blackbird\TicketBlaster\Model\Event $page */
                $event = $this->_eventFactory->create();
            } else {
                $event = $this->_event;
            }
            $this->setData('event', $event);
        }
        return $this->getData('event');
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Blackbird\TicketBlaster\Model\Event::CACHE_TAG . '_' . $this->getEvent()->getId()];
    }

}