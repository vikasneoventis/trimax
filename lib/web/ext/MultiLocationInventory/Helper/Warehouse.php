<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Helper;

use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory as WarehouseCollection;

/**
 * Warehouse helper.
 */
class Warehouse extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var WarehouseCollection
     */
    private $warehouseCollection;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItem;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param WarehouseCollection $warehouseCollection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        WarehouseCollection $warehouseCollection,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItem
    ) {
        $this->warehouseCollection = $warehouseCollection;
        $this->stockItem = $stockItem;
        parent::__construct($context);
    }

    /**
     * Search most suitable warehouse for order item
     *
     * @param $orderItem
     *
     * @return mixed
     */
    public function findWarehouseForOrderItem($orderItem)
    {
        $storeId = $orderItem->getOrder()->getStoreId();
        $customerGroupId = $orderItem->getOrder()->getCustomerGroupId();
        $qtyOrdered = $orderItem->getQtyOrdered();

        $warehouseCollection = $this->warehouseCollection->create();
        $warehouseCollection->prepareCollectionForOrderItems();
        $warehouseCollection->getSelect()->joinLeft(
            ['wh_stock_item' => 'aitoc_mli_stock_item_warehouse'],
            "main_table.warehouse_id = wh_stock_item.warehouse_id",
            ['stock_item_id', 'qty']
        );


        $productStockItem = $this->stockItem->get($orderItem->getProductId());
        $stockItemId = $productStockItem->getItemId();
        $warehouseCollection->addFieldToFilter('stock_item_id', $stockItemId);
        $warehouseCollection->addFieldToFilter('qty', ['gteq' => $qtyOrdered]);


        $warehouseCollection->addFieldToFilter('store_id', $storeId);
        $warehouseCollection->addFieldToFilter('customer_group_id', $customerGroupId);
        $warehouseCollection->setOrder('priority', 'asc');
        $suitableWarehouse = $warehouseCollection->load()->getFirstitem();
        if (!$suitableWarehouse || !$suitableWarehouse->getId()) {
            $warehouseCollection = $this->warehouseCollection->create();
            $suitableWarehouse = $warehouseCollection->addFieldToFilter('is_default', '1')->getFirstitem();
        }
        return $suitableWarehouse->getWarehouseId();
    }

    /**
     * Search most suitable warehouse for order item
     *
     * @param $orderItem
     *
     * @return mixed
     */
    public function getWarehouseStockDataForProduct($product)
    {
        $resultArray = [];
        foreach ($product->getData() as $attribute => $value) {
            if (strpos($attribute, 'warehouse_qty_') !== false) {
                $warehouseId = str_replace('warehouse_qty_', '', $attribute);
                $resultArray[$warehouseId]['qty'] = $value;
            } elseif (strpos($attribute, 'par_level_') !== false) {
                $warehouseId = str_replace('par_level_', '', $attribute);
                $resultArray[$warehouseId]['par_level'] = $value;
            } elseif (strpos($attribute, 'safety_stock_') !== false) {
                $warehouseId = str_replace('safety_stock_', '', $attribute);
                $resultArray[$warehouseId]['safety_stock'] = $value;
            }
        }
        return $resultArray;
    }
    
    public function disableDefaultWarehouse($ignoredIds = [])
    {
        $warehouseCollection = $this->warehouseCollection->create();
        $warehouseCollection->addFieldToFilter('is_default', '1');
        $warehouseCollection->addFieldToFilter('warehouse_id', ['nin' => $ignoredIds]);
        foreach ($warehouseCollection->getItems() as &$warehouseItem) {
            $warehouseItem->setData('is_default', '0');
        }
        $warehouseCollection->save();
    }
}
