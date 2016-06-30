<?php 

namespace Blackbird\TicketBlaster\Helper;

use Blackbird\TicketBlaster\Api\Data\EventInterface;
use Blackbird\TicketBlaster\Model\ResourceModel\Event\Collection as EventCollection;
use Magento\Framework\App\Action\Action;

class Event extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Blackbird\TicketBlaster\Model\Event
     */
    protected $_event;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Blackbird\TicketBlaster\Model\Event $event
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Blackbird\TicketBlaster\Model\Event $event,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_event = $event;
        $this->_storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Return an event from given event id.
     *
     * @param Action $action
     * @param null $eventId
     * @return \Magento\Framework\View\Result\Page|bool
     */
    public function prepareResultEvent(Action $action, $eventId = null)
    {
        if(!$this->isLoggedIn())
        {
            return false;
        }
        if ($eventId !== null && $eventId !== $this->_event->getId()) {
            $delimiterPosition = strrpos($eventId, '|');
            if ($delimiterPosition) {
                $eventId = substr($eventId, 0, $delimiterPosition);
            }
            
            $this->_event->setStoreId($this->_storeManager->getStore()->getId());
            if (!$this->_event->load($eventId)) {
                return false;
            }
        }

        if (!$this->_event->getId()) {
            return false;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        // We can add our own custom page handles for layout easily.
        $resultPage->addHandle('ticketblaster_event_view');

        // This will generate a layout handle like: ticketblaster_event_view_id_1
        // giving us a unique handle to target specific event if we wish to.
        $resultPage->addPageLayoutHandles(['id' => $this->_event->getId()]);

        // Magento is event driven after all, lets remember to dispatch our own, to help people
        // who might want to add additional functionality, or filter the events somehow!
        $this->_eventManager->dispatch(
            'blackbird_ticketblaster_event_render',
            ['event' => $this->_event, 'controller_action' => $action]
        );

        return $resultPage;
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }
}