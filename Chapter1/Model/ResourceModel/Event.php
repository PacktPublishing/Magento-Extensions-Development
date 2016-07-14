<?php

namespace Blackbird\TicketBlaster\Model\ResourceModel;

class Event extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
            \Magento\Framework\Model\ResourceModel\Db\Context $context,
            \Magento\Framework\Stdlib\DateTime\DateTime $date,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_storeManager = $storeManager;
        $this->_date = $date;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('blackbird_ticketblaster_event', 'event_id');
    }

    /**
     * Process event data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Blackbird\TicketBlaster\Model\ResourceModel\Event
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['event_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('blackbird_ticketblaster_event_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process event data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) {

        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        $table = $this->getTable('blackbird_ticketblaster_event_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['event_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['event_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null) {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Blackbird\TicketBlaster\Model\Event $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), \Magento\Store\Model\Store::DEFAULT_STORE_ID];

            $select->join(
                ['cbs' => $this->getTable('blackbird_ticketblaster_event_store')],
                $this->getMainTable() . '.event_id = cbs.event_id',
                ['store_id']
            )->where(
                'is_active = ?',
                1
            )->where(
                'cbs.store_id in (?)',
                $stores
            )->order(
                'store_id DESC'
            )->limit(
                1
            );
        }

        return $select;
    }

    /**
     * Retrieve load select with filter by url_key and activity
     *
     * @param string $url_key
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByUrlKeySelect($url_key, $isActive = null) {
        $select = $this->_getReadAdapter()->select()->from(
                        ['bp' => $this->getMainTable()]
                )->where(
                'bp.url_key = ?', $url_key
        );

        if (!is_null($isActive)) {
            $select->where('bp.is_active = ?', $isActive);
        }

        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('blackbird_ticketblaster_event_store'),
            'store_id'
        )->where(
            'event_id = :event_id'
        );

        $binds = [':event_id' => (int)$id];

        return $connection->fetchCol($select, $binds);
    }

    /**
     * Check if event url key exists
     * return event id if event exists
     *
     * @param string $url_key
     * @return int
     */
    public function checkUrlKey($url_key) {
        $select = $this->_getLoadByUrlKeySelect($url_key, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('bp.event_id')->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

}