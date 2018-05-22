<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Product\Edit\Action\Attribute\Tab;

class Inventory extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\Inventory
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * Inventory constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                   $context
     * @param \Magento\CatalogInventory\Model\Source\Backorders         $backorders
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Aitoc\AdvancedPermissions\Helper\Data                    $helper
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Model\Source\Backorders $backorders,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        array $data
    ) {
        parent::__construct($context, $backorders, $stockConfiguration, $data);
        $this->helper = $helper;
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getManageGlobalAttribute()) {
            return false;
        }

        return true;
    }
}
