<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Block\Adminhtml\Order\Items\Column;

use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory as WarehouseCollectionFactory;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseOrderItem\CollectionFactory as WarehouseOrderItemCollectionFactory;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory as WarehouseStockItemCollectionFactory;

class Warehouse extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{

    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory
     */
    protected $warehouseCollectionFactory;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseOrderItem\CollectionFactory
     */
    protected $warehouseOrderItemCollectionFactory;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory
     */
    protected $warehouseStockItemCollectionFactory;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItem;

    /**)
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param WarehouseCollectionFactory $warehouseCollectionFactory
     * @param WarehouseOrderItemCollectionFactory $warehouseOrderItemCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        WarehouseOrderItemCollectionFactory $warehouseOrderItemCollectionFactory,
        WarehouseStockItemCollectionFactory $warehouseStockItemCollectionFactory,
        WarehouseCollectionFactory $warehouseCollectionFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItem,
        array $data = []
    ) {
        $this->warehouseOrderItemCollectionFactory = $warehouseOrderItemCollectionFactory;
        $this->warehouseStockItemCollectionFactory = $warehouseStockItemCollectionFactory;
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
        $this->stockItem = $stockItem;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    /**
     * @return string
     */
    public function getWarehouse()
    {
        $orderItemId = $this->getItem()->getItemId();
        $warehouseOrderItem = $this->warehouseOrderItemCollectionFactory->create()
            ->addWarehouseDataToSelect(['name'])
            ->addFieldToFilter('main_table.order_item_id', $orderItemId)
            ->getFirstItem();
        return $warehouseOrderItem;
    }

    public function getWarehouseList()
    {
        $productId = $this->getItem()->getProductId();
        $qtyOrdered = $this->getItem()->getQtyOrdered();
        $productStockItem = $this->stockItem->get($productId);
        $stockItemId = $productStockItem->getItemId();
        $currentWarehouseId = $this->getWarehouse()->getWarehouseId();
        $warehouseList = $this->warehouseStockItemCollectionFactory->create();
        $warehouseList->addWarehouseDataToSelect(['warehouse_name' => 'name']);
        $warehouseList->addFieldToFilter('stock_item_id', $stockItemId);
        $warehouseList->addFieldToFilter(['qty', 'warehouse_id'], [['gteq' => $qtyOrdered], $currentWarehouseId]);
        $warehouseList->getItems();
        return $warehouseList;
    }

    public function getWarehouseChangeUrl()
    {
        return $this->getUrl('multilocationinventory/order_warehouse/change');
    }
}
