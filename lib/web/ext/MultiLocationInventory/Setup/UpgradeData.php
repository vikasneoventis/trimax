<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aitoc\MultiLocationInventory\Model\WarehouseStockItem;
use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory as WarehouseCollectionFactory;
use Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory as BookmarkCollectionFactory;

/**
 * Upgrade Data script
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    private $warehouseStockItemModel;

    private $warehouseCollectionFactory;

    private $bookmarkCollectionFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * Init
     *
     * @param WarehouseStockItem $warehouseStockItemModel
     * @param WarehouseCollectionFactory $warehouseCollectionFactory
     * @param BookmarkCollectionFactory $bookmarkCollectionFactory
     */
    public function __construct(
        WarehouseStockItem $warehouseStockItemModel,
        WarehouseCollectionFactory $warehouseCollectionFactory,
        BookmarkCollectionFactory $bookmarkCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->warehouseStockItemModel = $warehouseStockItemModel;
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
        $this->bookmarkCollectionFactory = $bookmarkCollectionFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.1.6', '<')) {
            $this->moveStockToDefaultWarehouse();
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->clearGridBookmarks();
        }

        $setup->endSetup();
    }

    private function moveStockToDefaultWarehouse()
    {
        $warehouseCollection = $this->warehouseCollectionFactory->create();
        $warehouseCollection->addFieldToFilter('is_default', '1');
        $defaultWarehouse = $warehouseCollection->getFirstItem();
        $defaultWarehouseId = $defaultWarehouse->getId();
        if ($defaultWarehouseId) {
            $resource = $this->objectManager->create('Magento\CatalogInventory\Model\ResourceModel\Stock\Item');
            $select = $resource->getConnection()->select()->from($resource->getMainTable());
            $stockItems = $resource->getConnection()->fetchAll($select);
            foreach ($stockItems as $stockItem) {
                $stockItemId = $stockItem['item_id'];
                $qty = (int)$stockItem['qty'];

                $this->warehouseStockItemModel->clearInstance();
                $this->warehouseStockItemModel->unsetData()
                    ->setWarehouseId($defaultWarehouseId)
                    ->setStockItemId($stockItemId)
                    ->setQty($qty);
                $this->warehouseStockItemModel->save();
            }
        }
    }

    private function clearGridBookmarks()
    {
        $bookmarkCollection = $this->bookmarkCollectionFactory->create();
        $bookmarkCollection->addFieldToFilter('namespace', 'sales_order_grid');
        foreach ($bookmarkCollection->getItems() as $bookmarkItem) {
            $bookmarkItem->delete();
        }
        $bookmarkCollection->save();
    }
}
