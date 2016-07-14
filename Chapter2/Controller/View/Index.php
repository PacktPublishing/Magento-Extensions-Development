<?php

namespace Blackbird\TicketBlaster\Controller\View;

class Index extends \Magento\Framework\App\Action\Action {

    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Event Index, shows a single event
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute() {
        $event_id = $this->getRequest()->getParam('event_id', $this->getRequest()->getParam('id', false));
        /** @var \Blackbird\TicketBlaster\Helper\Event $event_helper */
        $event_helper = $this->_objectManager->get('Blackbird\TicketBlaster\Helper\Event');
        $result_page = $event_helper->prepareResultEvent($this, $event_id);
        if (!$result_page) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        return $result_page;
    }

}
