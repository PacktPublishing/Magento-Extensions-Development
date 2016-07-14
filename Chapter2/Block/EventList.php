<?php
namespace Blackbird\TicketBlaster\Block;
use Blackbird\TicketBlaster\Api\Data\EventInterface;
use Blackbird\TicketBlaster\Model\ResourceModel\Event\Collection as EventCollection;
use Magento\Customer\Model\Context;

class EventList extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{

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
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Blackbird\TicketBlaster\Model\ResourceModel\Event\CollectionFactory $eventCollectionFactory,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Blackbird\TicketBlaster\Model\ResourceModel\Event\CollectionFactory $eventCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_eventCollectionFactory = $eventCollectionFactory;
        $this->_customerSession = $customerSession;
    }

    /**
     * @return \Blackbird\TicketBlaster\Model\ResourceModel\Event\Collection
     */
    public function getEvents()
    {
        if (!$this->hasData('events')) {
            $events = $this->_eventCollectionFactory
                ->create()
                ->addOrder(
                    EventInterface::CREATION_TIME,
                    EventCollection::SORT_ORDER_DESC
                )
                ->addStoreFilter($this->_storeManager->getStore()->getId());
            $this->setData('events', $events);
        }
        return $this->getData('events');
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Blackbird\TicketBlaster\Model\Event::CACHE_TAG . '_' . 'list'];
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
