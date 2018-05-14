<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *
 * MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_CustomerApproval
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerApproval\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * InstallData constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return mixed
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(Customer::ENTITY, 'activasion_status', [
                'label' => 'Status',
                'input' => 'select',
                'required' => false,
                'sort_order' => 100,
                'visible' => false,
                'system' => false,
                'type' => 'static',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'option' => ['values' => ['Pending', 'Approved', 'Disapproved']],
            ]);

        // add attribute to form
        /** @var  $attribute */
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'activasion_status');
        $attribute->setData('used_in_forms', ['adminhtml_customer', 'customer_account_create']);
        $attribute->save();
        $setup->endSetup();
    }
}
