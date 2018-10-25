<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer;

use Magento\CatalogInventory\Api\StockIndexInterface;
use Magento\CatalogInventory\Observer\SaveInventoryDataObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event eav_collection_abstract_load_before
 */
class InventoryObserver implements ObserverInterface
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\CatalogInventory\Api\StockIndexInterface
     */
    protected $stockIndex;
    
    /**
     * @var \Magento\CatalogInventory\Observer\SaveInventoryDataObserver
     */
    protected $saveInventory;

    /**
     * InventoryObserver constructor.
     *
     * @param StockIndexInterface $stockIndex
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     * @param SaveInventoryDataObserver $saveInventory
     */
    public function __construct(
        StockIndexInterface $stockIndex,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        SaveInventoryDataObserver $saveInventory
    ) {
        $this->stockIndex = $stockIndex;
        $this->saveInventory = $saveInventory;
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product->getStockData() === null) {
            if ($product->getIsChangedWebsites() || $product->dataHasChangedFor('status')) {
                $this->stockIndex->rebuild(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );
            }
        }
        
        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getManageGlobalAttribute()) {
            return $this;
        }
        
        $this->saveInventory->execute($observer);
        return $this;
    }
}
