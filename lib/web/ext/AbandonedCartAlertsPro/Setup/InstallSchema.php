<?php
namespace Aitoc\AbandonedCartAlertsPro\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreLine
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_abandoned_cart_alerts_pro_campaign'))
            ->addColumn(
                'campaign_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Campaign id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Campaign name'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Campaign description'
            )
            ->addColumn(
                'sender',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false, 'default' => 'general'],
                'Sender'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['unsigned' => false, 'nullable' => false, 'default' => 'pending'],
                'Status'
            )
            ->addColumn(
                'discount_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Discount amount'
            )
            ->addColumn(
                'expiry_days',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '12,4',
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Expiry days'
            )
            ->addColumn(
                'sales_rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Sales rule id'
            )
            ->addColumn(
                'exclude_from_alert',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Exclude unavailable products in alert'
            )
            ->addIndex(
                $installer->getIdxName('aitoc_abandoned_cart_alerts_pro_campaign', ['campaign_id']),
                ['campaign_id']
            )
            ->setComment(
                'Abandoned cart alerts campaigns'
            );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_abandoned_cart_alerts_pro_alert'))
            ->addColumn(
                'alert_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Alert id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created at'
            )
            ->addColumn(
                'sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Sent at'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['unsigned' => false, 'nullable' => false, 'default' => 'pending'],
                'Status'
            )
            ->addColumn(
                'alert_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['unsigned' => false, 'nullable' => false],
                'Alert type'
            )
            ->addColumn(
                'alert_type_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Alert type id'
            )
            ->addColumn(
                'campaign_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Campaign id'
            )
            ->addColumn(
                'sales_rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Sales rule id'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Customer id'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Customer email'
            )
            ->addColumn(
                'customer_firstname',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Customer first name'
            )
            ->addColumn(
                'customer_middlename',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Customer middle name'
            )
            ->addColumn(
                'customer_lastname',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Customer last name'
            )
            ->addColumn(
                'products',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Products'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['unsigned' => false, 'nullable' => false],
                'Recovery code'
            )
            ->addIndex(
                $installer->getIdxName('aitoc_abandoned_cart_alerts_pro_alert', ['alert_id']),
                ['alert_id']
            )
            ->addIndex(
                $installer->getIdxName('aitoc_abandoned_cart_alerts_pro_alert', ['campaign_id']),
                ['campaign_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_abandoned_cart_alerts_pro_alert',
                    'campaign_id',
                    'aitoc_abandoned_cart_alerts_pro_campaign',
                    'campaign_id'
                ),
                'campaign_id',
                $installer->getTable('aitoc_abandoned_cart_alerts_pro_campaign'),
                'campaign_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment(
                'Abandoned cart alerts'
            );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_abandoned_cart_alerts_pro_statistic'))
            ->addColumn(
                'statistic_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Statistic id'
            )
            ->addColumn(
                'campaign_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Campaign id'
            )
            ->addColumn(
                'alert_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Alert id'
            )
            ->addColumn(
                'recovered_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Recovered at'
            )
            ->addColumn(
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Quote id'
            )
            ->addColumn(
                'quote_grand_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '12,4',
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Quote grand total'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Order id'
            )
            ->addColumn(
                'order_grand_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '12,4',
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Order grand total'
            )
            ->addColumn(
                'order_created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Order created at'
            )
            ->addColumn(
                'order_currency_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                3,
                [],
                'Order currency code'
            )
            ->addIndex(
                $installer->getIdxName('aitoc_abandoned_cart_alerts_pro_statistic', ['alert_id']),
                ['alert_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_abandoned_cart_alerts_pro_statistic',
                    'alert_id',
                    'aitoc_abandoned_cart_alerts_pro_alert',
                    'alert_id'
                ),
                'alert_id',
                $installer->getTable('aitoc_abandoned_cart_alerts_pro_alert'),
                'alert_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment(
                'Recovered alerts statistic'
            );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_abandoned_cart_alerts_pro_stoplist'))
            ->addColumn(
                'stoplist_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Stoplist id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Created at'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Customer email'
            )
            ->addIndex(
                $installer->getIdxName('aitoc_abandoned_cart_alerts_pro_stoplist', ['customer_email']),
                ['customer_email']
            )
            ->setComment(
                'Alerts stoplist'
            );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
