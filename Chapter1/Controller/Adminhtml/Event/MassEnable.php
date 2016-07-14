<?php

namespace Blackbird\TicketBlaster\Controller\Adminhtml\Event;

use Blackbird\TicketBlaster\Controller\Adminhtml\Event\AbstractMassStatus;

/**
 * Class MassEnable
 */
class MassEnable extends AbstractMassStatus
{
    /**
     * Field id
     */
    const ID_FIELD = 'event_id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Blackbird\TicketBlaster\Model\ResourceModel\Event\Collection';

    /**
     * Event model
     *
     * @var string
     */
    protected $model = 'Blackbird\TicketBlaster\Model\Event';

    /**
     * Event enable status
     *
     * @var boolean
     */
    protected $status = true;
}