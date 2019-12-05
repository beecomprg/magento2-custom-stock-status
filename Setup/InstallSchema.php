<?php

namespace Beecom\CustomStockStatus\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)

    {

        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('beecom_custom_stock_status')
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Rule Name'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Active'
        )->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Sort Order (Priority)'
        )->setComment(
            'Beecom Custom Stock Status'
        );

        $table2 = $setup->getConnection()->newTable(
            $setup->getTable('beecom_custom_stock_status_store')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Rule ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $setup->getIdxName('beecom_custom_stock_status_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName('beecom_custom_stock_status_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('beecom_custom_stock_status_store', 'rule_id', 'beecom_custom_stock_status', 'rule_id'),
            'rule_id',
            $setup->getTable('beecom_custom_stock_status'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Custom stock status rule To Store Linkage Table'
        );

        $table3 = $installer->getConnection()->newTable(
            $installer->getTable('beecom_custom_stock_status_rule')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'text',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Text'
        )->addColumn(
            'qty_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            null,
            ['nullable' => false],
            'Qty from'
        )->addColumn(
            'qty_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            null,
            ['nullable' => false],
            'Qty to'
        )->addColumn(
            'rule_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Rule Type'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Position'
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Rule ID'
        )->setComment(
            'Beecom Custom Stock Status Rules'
        );

        $table4 = $installer->getConnection()->newTable(
            $installer->getTable('beecom_custom_stock_status_applied')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Rule ID'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'default' => null],
            'Product ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'default' => null],
            'Store ID'
        );


        $installer->getConnection()->createTable($table);
        $installer->getConnection()->createTable($table3);
        $installer->getConnection()->createTable($table4);
        $installer->getConnection()->createTable($table2);

        $installer->endSetup();
    }
}
