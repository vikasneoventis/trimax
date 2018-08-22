<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Block\Adminhtml\Product\Edit\Tab;

class Inventory extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Inventory
{

    /**
     * @var array
     */
    protected $_restrictedTypes = [
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
        \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
        \Aitoc\PreOrders\Model\Product\Type::TYPE_CONFIGURABLE,
    ];

    /**
     * Inventory constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Model\Source\Backorders $backorders
     * @param \Magento\CatalogInventory\Model\Source\Stock $stock
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Model\Source\Backorders $backorders,
        \Magento\CatalogInventory\Model\Source\Stock $stock,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backorders,
            $stock,
            $moduleManager,
            $coreRegistry,
            $stockRegistry,
            $stockConfiguration,
            $data
        );
    }

    /**
     * Get value from attribute preorderdescript
     *
     * @return string
     */
    public function getPreorderDescription()
    {
        $description = $this->getProduct()->getPreorderdescript();

        return strlen($description) ? $description : '';
    }

    /**
     * Get value from attribute preorderdescript
     *
     * @return int
     */
    public function getIsPreorder()
    {
        return (int)$this->getProduct()->getPreorder();
    }

    /**
     * Get array types of product
     *
     * @return array
     */
    public function getRestrictedTypes()
    {
        return $this->_restrictedTypes;
    }

    /**
     * Get value from attribute backstock_preorders
     *
     * @return mixed
     */
    public function getBackstockPreorders()
    {
        return $this->getProduct()->getData("backstock_preorders");
    }

    /**
     * Check type of product in array
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return !in_array($this->getProduct()->getTypeId(), $this->getRestrictedTypes());
    }
}
