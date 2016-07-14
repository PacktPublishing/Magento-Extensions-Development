<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Blackbird\TicketBlaster\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        
        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $installer = $setup;
            $installer->startSetup();
            
            /**
             * Create table 'blackbird_ticketblaster_event_store'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('blackbird_ticketblaster_event_store')
            )->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Event ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $installer->getIdxName('blackbird_ticketblaster_event_store', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('blackbird_ticketblaster_event_store', 'event_id', 'blackbird_ticketblaster_event', 'event_id'),
                'event_id',
                $installer->getTable('blackbird_ticketblaster_event'),
                'event_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('blackbird_ticketblaster_event_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'TicketBlaster Event To Store Linkage Table'
            );
            $installer->getConnection()->createTable($table);

            $installer->endSetup();
        }
    }
}
