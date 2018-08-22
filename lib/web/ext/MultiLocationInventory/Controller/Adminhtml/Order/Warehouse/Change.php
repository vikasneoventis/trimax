<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Order\Warehouse;

class Change extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\WarehouseOrderItem
     */
    protected $warehouseOrderItem;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory
     */
    protected $warehouseStockItemCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\Item
     */
    protected $orderItemModel;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItem;

    /**
     * Initialize Login controller
     *
     * @param \Magento\Framework\App\Action\Context                  $context
     * @param \Magento\Framework\Controller\Result\JsonFactory       $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory        $resultRawFactory
     * @param \Aitoc\MultiLocationInventory\Model\WarehouseOrderItem $warehouseOrderItem
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Aitoc\MultiLocationInventory\Model\WarehouseOrderItem $warehouseOrderItem,
        \Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory $warehouseStockItemCollectionFactory,
        \Magento\Sales\Model\Order\Item $orderItemModel,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItem
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->warehouseOrderItem = $warehouseOrderItem;
        $this->warehouseStockItemCollectionFactory = $warehouseStockItemCollectionFactory;
        $this->orderItemModel = $orderItemModel;
        $this->stockItem = $stockItem;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        $httpBadRequestCode = 400;
        if ($this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $orderItemId = $this->getRequest()->getParam('order_item_id');
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        if (!$orderItemId || !$warehouseId) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $this->orderItemModel->load($orderItemId);
        $orderedQty = $this->orderItemModel->getQtyOrdered();
        $productId = $this->orderItemModel->getProductId();
        $productStockItem = $this->stockItem->get($productId);
        $stockItemId = $productStockItem->getItemId();

        $orderItemWarehouse = $this->warehouseOrderItem->load($orderItemId, 'order_item_id');
        if ($orderItemWarehouse->getId()) {
            $oldWarehouseId = $this->warehouseOrderItem->getWarehouseId();
            $warehouseStockItemCollection = $this->warehouseStockItemCollectionFactory->create();
            $warehouseStockItemCollection->addFieldToFilter('warehouse_id', $oldWarehouseId)
                ->addFieldToFilter('stock_item_id', $stockItemId);
            $oldStockItem = $warehouseStockItemCollection->getFirstItem();
            $oldStockItem->setQty($oldStockItem->getQty() + $orderedQty)->save();
            $warehouseStockItemCollection = $this->warehouseStockItemCollectionFactory->create();
            $warehouseStockItemCollection->addFieldToFilter('warehouse_id', $warehouseId)
                ->addFieldToFilter('stock_item_id', $stockItemId);
            $newStockItem = $warehouseStockItemCollection->getFirstItem();
            $newStockItem->setQty($newStockItem->getQty() - $orderedQty)->save();
        }

        $orderItemWarehouse->setWarehouseId($warehouseId)->setOrderItemId($orderItemId)->save();

        $warehouseOptions = "";
        $currentWarehouseId = $warehouseId;
        $warehouseList = $this->warehouseStockItemCollectionFactory->create();
        $warehouseList->addWarehouseDataToSelect(['warehouse_name' => 'name']);
        $warehouseList->addFieldToFilter('stock_item_id', $stockItemId);
        $warehouseList->addFieldToFilter(['qty', 'warehouse_id'], [['gteq' => $orderedQty], $currentWarehouseId]);
        foreach ($warehouseList->getItems() as $warehouseItem) {
            $selected = "";
            if ($warehouseItem->getWarehouseId() == $currentWarehouseId) {
                $selected = " selected='selected'";
            }
            $warehouseOptions .= "<option value='" . $warehouseItem->getWarehouseId() . "'" . $selected . ">" . $warehouseItem->getWarehouseName() . " (Qty: " . $warehouseItem->getQty() . ")</option>";
        }

        $response = [
            'errors'  => false,
            'message' => __('Warehouse changed successful.'),
            'options' => $warehouseOptions
        ];

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
