<?php

namespace Aitoc\MultiLocationInventory\Ui\Component\Listing\Order\Column;

use \Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseOrderItem\CollectionFactory as WarehouseOrderItemCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Aitoc\MultiLocationInventory\Helper\Warehouse as WarehouseHelper;

class Warehouse extends Column
{
    /**
     * @var WarehouseOrderItemCollectionFactory
     */
    private $warehouseOrderItemCollectionFactory;

    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemCollectionFactory;

    /**
     * @var WarehouseHelper
     */
    private $warehouseHelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        WarehouseOrderItemCollectionFactory $warehouseOrderItemCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        WarehouseHelper $warehouseHelper,
        array $components,
        array $data
    ) {
        $this->warehouseOrderItemCollectionFactory = $warehouseOrderItemCollectionFactory;
        $this->warehouseHelper = $warehouseHelper;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $warehouseArray = [];
                $orderId = $item['entity_id'];
                $orderItems = $this->orderItemCollectionFactory->create()
                    ->addFieldToFilter('order_id', $orderId)
                    ->getItems();
                $item[$this->getData('name')] = '';
                foreach ($orderItems as $orderItem) {
                    $warehouseOrderItem = $this->warehouseOrderItemCollectionFactory->create()
                        ->addFieldToFilter('order_item_id', $orderItem->getId())
                        ->addWarehouseDataToSelect(['name'])
                        ->getFirstItem();
                    $warehouseArray[$warehouseOrderItem->getWarehouseId()] = $warehouseOrderItem->getName();
                }

                foreach ($warehouseArray as $warehouseName) {
                    $item[$this->getData('name')] .= $warehouseName . '<br/>';
                }
            }
        }

        return $dataSource;
    }
}
