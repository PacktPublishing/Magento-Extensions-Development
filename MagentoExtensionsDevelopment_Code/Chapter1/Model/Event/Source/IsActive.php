<?php

namespace Blackbird\TicketBlaster\Model\Event\Source;

class IsActive implements \Magento\Framework\Data\OptionSourceInterface {

    /**
     * @var \Blackbird\TicketBlaster\Model\Event
     */
    protected $_event;

    /**
     * Constructor
     *
     * @param \Blackbird\TicketBlaster\Model\Event $event
     */
    public function __construct(\Blackbird\TicketBlaster\Model\Event $event) {
        $this->_event = $event;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->_event->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

}
