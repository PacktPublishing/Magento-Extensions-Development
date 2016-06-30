<?php
namespace Blackbird\TicketBlaster\Controller\Adminhtml\Event;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Blackbird_TicketBlaster::ticketblaster_event';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Blackbird_TicketBlaster::ticketblaster_event');
        $resultPage->addBreadcrumb(__('Events'), __('Events'));
        $resultPage->addBreadcrumb(__('Manage Events'), __('Manage Events'));
        $resultPage->getConfig()->getTitle()->prepend(__('TicketBlaster Events'));

        return $resultPage;
    }
}
