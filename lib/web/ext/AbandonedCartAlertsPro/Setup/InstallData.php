<?php
namespace Aitoc\AbandonedCartAlertsPro\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreLine
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $data = [
            'name' => 'Default',
            'description' => 'Default campaign',
            'sender' => 'general',
            'status' => 'enabled',
            'discount_amount' => 10,
            'expiry_days' => 3,
            'exclude_from_alert' => 0
        ];

        $setup->getConnection()->insertForce($setup->getTable('aitoc_abandoned_cart_alerts_pro_campaign'), $data);
    }
}
