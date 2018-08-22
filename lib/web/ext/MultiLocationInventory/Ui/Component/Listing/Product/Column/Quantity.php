<?php

namespace Aitoc\MultiLocationInventory\Ui\Component\Listing\Product\Column;

use \Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory as WarehouseStockItemCollectionFactory;
use Aitoc\MultiLocationInventory\Helper\Warehouse as WarehouseHelper;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;

class Quantity extends Column
{
    /**
     * @var WarehouseStockItemCollectionFactory
     */
    private $warehouseStockItemCollectionFactory;

    /**
     * @var StockItemRepository
     */
    private $stockItem;

    /**
     * @var WarehouseHelper
     */
    private $warehouseHelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        WarehouseStockItemCollectionFactory $warehouseStockItemCollectionFactory,
        StockItemRepository $stockItem,
        WarehouseHelper $warehouseHelper,
        array $components,
        array $data
    ) {
        $this->warehouseStockItemCollectionFactory = $warehouseStockItemCollectionFactory;
        $this->stockItem = $stockItem;
        $this->warehouseHelper = $warehouseHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = '';
                $productId = $item['entity_id'];
                $productStockItemId = $this->stockItem->get($productId)->getItemId();
                $warehouseStockItemCollection = $this->warehouseStockItemCollectionFactory->create()
                    ->addWarehouseDataToSelect(['name'])
                    ->addFieldToFilter('stock_item_id', $productStockItemId);
                foreach ($warehouseStockItemCollection->getItems() as $warehouseStockItem) {
                    if ($warehouseStockItem->getQty() != 0) {
                        $item[$this->getData('name')] .= '<b>' . $warehouseStockItem->getName() . ':</b>  ' . $warehouseStockItem->getQty() . '<br/>';
                    }
                }
                if ($item[$this->getData('name')] == "") {
                    $item[$this->getData('name')] = "0";
                }
            }
        }

        return $dataSource;
    }
}
