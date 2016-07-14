<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Blackbird\TicketBlaster\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
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
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'event_link',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Event',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Blackbird\TicketBlaster\Model\Event\Attribute\Source\Event',
                    'global' => true,
                    'visible' => true,
                    'required' => true,
                    'user_defined' => true,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false,
                    'apply_to' => 'ticket',
                    'used_in_product_listing' => 0
                ]
            );
            
            // Add the new attribute group
            $eavSetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'Default', 'TicketBlaster', 2);
            
            // Update the attribute group by adding our custom attribute
            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
            $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'TicketBlaster');
            
            $eavSetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $attributeGroupId,
                    'event_link',
                    10);
        }
    }
}
