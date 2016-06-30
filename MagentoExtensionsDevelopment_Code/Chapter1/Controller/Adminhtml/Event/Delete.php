<?php

namespace Blackbird\TicketBlaster\Controller\Adminhtml\Event;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_TicketBlaster::ticketblaster_event_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('event_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Blackbird\TicketBlaster\Model\Event');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('You deleted the event.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['event_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a event to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
