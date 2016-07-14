<?php

namespace Blackbird\TicketBlaster\Model\Event\Attribute\Backend;

class Event extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * Before save
     *
     * @param \Magento\Framework\Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }

        if (!$object->hasStoreId()) {
            $object->setStoreId($this->_storeManager->getStore()->getId());
        }

        if (!$object->hasData('created_in')) {
            $object->setData('created_in', $this->_storeManager->getStore($object->getStoreId())->getName());
        }

        return $this;
    }
}
