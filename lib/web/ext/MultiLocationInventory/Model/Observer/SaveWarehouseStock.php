<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory as WarehouseStockItemCollectionFactory;
use Aitoc\MultiLocationInventory\Helper\Warehouse as WarehouseHelper;
use Aitoc\MultiLocationInventory\Model\SupplierProduct;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;

/**
 * Class SaveWarehouseStock
 */
class SaveWarehouseStock implements ObserverInterface
{
    /**
     * @var WarehouseStockItemCollectionFactory
     */
    private $warehouseStockItemCollectionFactory;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItem;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\SupplierProduct
     */
    private $supplierProduct;

    /**
     * @var WarehouseHelper
     */
    private $warehouseHelper;

    /**
     * @param WarehouseStockItemCollectionFactory $warehouseStockItemCollectionFactory
     * @param WarehouseHelper $warehouseHelper
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItem
     */
    public function __construct(
        WarehouseStockItemCollectionFactory $warehouseStockItemCollectionFactory,
        WarehouseHelper $warehouseHelper,
        SupplierProduct $supplierProduct,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItem
    ) {
        $this->warehouseStockItemCollectionFactory  = $warehouseStockItemCollectionFactory;
        $this->warehouseHelper = $warehouseHelper;
        $this->stockItem = $stockItem;
        $this->supplierProduct = $supplierProduct;
    }

    /**
     * Set most suitable warehouse to order items
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if (!$product) {
            return;
        }
        $productStockItem = $this->stockItem->get($product->getId());
        $warehouseStockData = $this->warehouseHelper->getWarehouseStockDataForProduct($product);
        $stockItemId = $productStockItem->getItemId();
        $totalProductQty = 0;
        foreach ($warehouseStockData as $warehouseId => $warehouseData) {
            $qty = $warehouseData['qty'] ? $warehouseData['qty'] : 0;
            $totalProductQty += $qty;
            $safetyStock = $warehouseData['safety_stock'] ? $warehouseData['safety_stock'] : 0;
            $parLevel = $warehouseData['par_level'] ? $warehouseData['par_level'] : 0;
            $currentStockModel = $this->warehouseStockItemCollectionFactory->create()
                ->addFieldToFilter('stock_item_id', $stockItemId)
                ->addFieldToFilter('warehouse_id', $warehouseId)
                ->getFirstItem();
            $currentStockModel->setWarehouseId($warehouseId)
                ->setStockItemId($stockItemId)
                ->setQty($qty)
                ->setSafetyStock($safetyStock)
                ->setParLevel($parLevel)
                ->save();
        }

        $productStockItem->setQty($totalProductQty)->save();

        $supplierId = $product->getData('supplier');
        $supplierProductModel = $this->supplierProduct->load($product->getId(), 'product_id');
        if ($supplierId) {
            $supplierProductModel->setSupplierId($supplierId)
                ->setProductId($product->getId())
                ->save();
        } else {
            $supplierProductModel->delete();
        }
    }
}
