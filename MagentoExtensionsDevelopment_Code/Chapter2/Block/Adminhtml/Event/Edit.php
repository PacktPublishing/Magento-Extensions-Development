<?php
namespace Blackbird\TicketBlaster\Block\Adminhtml\Event;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize event edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'event_id';
        $this->_blockGroup = 'Blackbird_TicketBlaster';
        $this->_controller = 'adminhtml_event';

        parent::_construct();

        if ($this->_isAllowedAction('Blackbird_TicketBlaster::ticketblaster_event_save')) {
            $this->buttonList->update('save', 'label', __('Save Event'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Blackbird_TicketBlaster::ticketblaster_event_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Event'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded event
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('ticketblaster_event')->getId()) {
            return __("Edit Event '%1'", $this->escapeHtml($this->_coreRegistry->registry('ticketblaster_event')->getTitle()));
        } else {
            return __('New Event');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('ticketblaster/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}