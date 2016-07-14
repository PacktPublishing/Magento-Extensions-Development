<?php

namespace Blackbird\TicketBlaster\Controller\View;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use Blackbird\TicketBlaster\Api\Data\EventInterface;

class Share extends \Magento\Framework\App\Action\Action {
    
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'ticketblaster/email/recipient_email';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'ticketblaster/email/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'ticketblaster/email/email_template';

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Blackbird\TicketBlaster\Model\Event
     */
    protected $_event;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Blackbird\TicketBlaster\Model\Event $event
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_event = $event;
    }

    /**
     * Share event
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute() {
        
        $post = $this->getRequest()->getParams();
        
        if (!$post || !$post['event_id']) {
            $this->_redirect('events');
            return;
        }

        $this->inlineTranslation->suspend();
        try {
            $postObject = new \Magento\Framework\DataObject();
            
            $this->_event->setStoreId($this->storeManager->getStore()->getId());
            if (!$this->_event->load($post['event_id'])) {
                $this->_redirect('events');
                return;
            }
            
            $postObject->setData($post);

            $error = false;

            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
            }
            if ($error) {
                throw new \Exception();
            }

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['event' => $this->_event, 'post' => $postObject])
                ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                ->setReplyTo($post['email'])
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $this->messageManager->addSuccess(
                __('Thanks for sharing this event.')
            );
            $this->_redirect('contact/index');
            return;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->_redirect('events/view/index', array('event_id' => $post['event_id']));
            return;
        }        
        
        $this->_redirect('events/view/index', array('event_id' => $post['event_id']));
    }

}
