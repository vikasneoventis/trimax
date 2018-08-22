<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aitoc\MultiLocationInventory\Helper\Warehouse as WarehouseHelper;
use Aitoc\MultiLocationInventory\Model\WarehouseOrderItem as WarehouseOrderItemModel;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory as WarehouseStockItemCollectionFactory;

/**
 * Class SetWarehouseAfterPlaceOrder
 */
class SetWarehouseAfterPlaceOrder implements ObserverInterface
{
    /**
     * @var WarehouseHelper
     */
    private $warehouseHelper;

    /**
     * @var WarehouseOrderItemModel
     */
    private $warehouseOrderItemModel;

    /**
     * @var WarehouseStockItemCollectionFactory
     */
    private $warehouseStockItemCollectionFactory;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItem;

    /**
     * @param WarehouseHelper $warehouseHelper
     * @param WarehouseOrderItemModel $warehouseOrderItemModel
     * @param WarehouseStockItemCollectionFactory $warehouseStockItemCollectionFactory
     */
    public function __construct(
        WarehouseHelper $warehouseHelper,
        WarehouseOrderItemModel $warehouseOrderItemModel,
        WarehouseStockItemCollectionFactory $warehouseStockItemCollectionFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItem
    ) {
        $this->warehouseHelper  = $warehouseHelper;
        $this->warehouseOrderItemModel = $warehouseOrderItemModel;
        $this->warehouseStockItemCollectionFactory = $warehouseStockItemCollectionFactory;
        $this->stockItem = $stockItem;
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
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $orderItems = $order->getItems();
        foreach ($orderItems as $orderItem) {
            $warehouseId = $this->warehouseHelper->findWarehouseForOrderItem($orderItem);
            if ($warehouseId) {
                $this->warehouseOrderItemModel->clearInstance();
                $this->warehouseOrderItemModel->unsetData()
                    ->setWarehouseId($warehouseId)
                    ->setOrderItemId($orderItem->getId())
                    ->setQty($orderItem->getQtyOrdered());
                $this->warehouseOrderItemModel->save();

                $productStockItem = $this->stockItem->get($orderItem->getProductId());
                $stockItemId = $productStockItem->getItemId();
                $warehouseStockItemCollection = $this->warehouseStockItemCollectionFactory->create();
                $warehouseStockItemCollection->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('stock_item_id', $stockItemId);
                $warehouseStockItem = $warehouseStockItemCollection->getFirstItem();
                $newQty = $warehouseStockItem->getQty() - $orderItem->getQtyOrdered();
                $warehouseStockItem->setQty($newQty)->save();
            }
        }
    }
}
