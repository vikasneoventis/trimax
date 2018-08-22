<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Form\DataProvider\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory as WarehouseCollectionFactory;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory as WarehouseStockItemCollectionFactory;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Aitoc\MultiLocationInventory\Model\ResourceModel\Supplier\CollectionFactory as SupplierCollectionFactory;
use Aitoc\MultiLocationInventory\Model\ResourceModel\SupplierProduct\CollectionFactory as SupplierProductCollectionFactory;

class CatalogQty extends AbstractModifier
{
    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory
     */
    private $warehouseCollectionFactory;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory
     */
    private $warehouseStockItemCollectionFactory;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\Supplier\CollectionFactory
     */
    private $supplierCollectionFactory;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\SupplierProduct\CollectionFactory
     */
    private $supplierProductCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItem;

    public function __construct(
        WarehouseCollectionFactory $warehouseCollectionFactory,
        WarehouseStockItemCollectionFactory $warehouseStockItemCollectionFactory,
        LocatorInterface $locator,
        StockItemRepository $stockItem,
        SupplierCollectionFactory $supplierCollectionFactory,
        SupplierProductCollectionFactory $supplierProductCollectionFactory
    ) {
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
        $this->warehouseStockItemCollectionFactory = $warehouseStockItemCollectionFactory;
        $this->locator = $locator;
        $this->stockItem = $stockItem;
        $this->supplierCollectionFactory = $supplierCollectionFactory;
        $this->supplierProductCollectionFactory = $supplierProductCollectionFactory;
    }

    public function modifyMeta(array $meta)
    {
        $meta['product-details']['children']['quantity_and_stock_status_qty']['children']['qty']['arguments']['data']['config']['visible'] = 0;

        $meta['warehouse_settings'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Warehouse Stock Settings'),
                        'sortOrder' => 10,
                        'collapsible' => true,
                        'componentType' => 'fieldset',
                        'dataScope' => static::DATA_SCOPE_PRODUCT, // save data in the product data
                        'provider' => static::DATA_SCOPE_PRODUCT . '_data_source',
                        'ns' => static::FORM_NAME,
                        'opened' => true
                    ]
                ]
            ],
            'children' => []
        ];

        $supplierOptionsArray = [
            [
                'label' => __('----------------------'),
                'value' => 0
            ]
        ];
        $supplierCollection = $this->supplierCollectionFactory->create();
        foreach ($supplierCollection->getItems() as $supplierItem) {
            $supplierOptionsArray[] = [
                'label' => $supplierItem->getTitle(),
                'value' => $supplierItem->getEntityId()
            ];
        }


        $productId = $this->locator->getProduct()->getId();
        $supplierId = 0;
        if ($productId) {
            $productStockItem = $this->stockItem->get($productId);
            $supplierProductCollection = $this->supplierProductCollectionFactory->create();
            $supplierProductCollection->addFieldToFilter('product_id', $productId);
            $supplierProductModel = $supplierProductCollection->getFirstItem();
            $supplierId = $supplierProductModel->getSupplierId() ? $supplierProductModel->getSupplierId() : 0;
        }

        $meta['warehouse_settings']['children']['supplier'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'select',
                        'componentType' => 'field',
                        'visible'       => 1,
                        'required'      => 1,
                        'code'          => 'supplier',
                        'dataScope'     => 'supplier',
                        'default'       => 0,
                        'value'         => $supplierId,
                        'options' => $supplierOptionsArray,
                        'label'         => __('Supplier')
                    ]
                ]
            ]
        ];

        $warehouseCollection = $this->warehouseCollectionFactory->create();
        foreach ($warehouseCollection->getItems() as $warehouseItem) {
            if ($productId) {
                $warehouseStockItem = $this->warehouseStockItemCollectionFactory->create()
                    ->addFieldToFilter('warehouse_id', $warehouseItem->getWarehouseId())
                    ->addFieldToFilter('stock_item_id', $productStockItem->getItemId())
                    ->getFirstItem();
                $currentQty = $warehouseStockItem->getQty();
                $parLevel = $warehouseStockItem->getParLevel();
                $safetyStock = $warehouseStockItem->getSafetyStock();
            } else {
                $currentQty = 0;
                $parLevel = 0;
                $safetyStock = 0;
            }
            $warehouseLabel = '';
            if ($warehouseItem->getIsDefault()) {
                $warehouseLabel .= '[Default] ';
            }
            $warehouseLabel .= __($warehouseItem->getName() . ':');

            $meta['warehouse_settings']['children']['warehouse_qty_' . $warehouseItem->getWarehouseId() . '_container'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'breakLine' => false,
                            'visible' => 1,
                            'required' => 1,
                            'label' => $warehouseLabel,
                            'component' => 'Magento_Ui/js/form/components/group'
                        ]
                    ]
                ],
                'children' => [
                    'warehouse_qty_' . $warehouseItem->getWarehouseId() => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => 'input',
                                    'componentType' => 'field',
                                    'visible' => 1,
                                    'required' => 1,
                                    'code' => 'warehouse_qty_' . $warehouseItem->getWarehouseId(),
                                    'dataScope' => 'warehouse_qty_' . $warehouseItem->getWarehouseId(),
                                    'default' => 0,
                                    'value' => $currentQty,
                                    'scopeLabel' => __('(Qty | Par Level | Safety Stock)'),
                                    'label' => $warehouseLabel
                                ]
                            ]
                        ]
                    ],
                    'par_level_' . $warehouseItem->getWarehouseId() => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => 'input',
                                    'componentType' => 'field',
                                    'visible' => 1,
                                    'required' => 1,
                                    'code' => 'par_level_' . $warehouseItem->getWarehouseId(),
                                    'dataScope' => 'par_level_' . $warehouseItem->getWarehouseId(),
                                    'default' => 0,
                                    'value' => $parLevel,
                                    'label' => __($warehouseItem->getName() . ' Par Level')
                                ]
                            ]
                        ]
                    ],
                    'safety_stock_' . $warehouseItem->getWarehouseId() => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => 'input',
                                    'componentType' => 'field',
                                    'visible' => 1,
                                    'required' => 1,
                                    'code' => 'safety_stock_' . $warehouseItem->getWarehouseId(),
                                    'dataScope' => 'safety_stock_' . $warehouseItem->getWarehouseId(),
                                    'default' => 0,
                                    'value' => $safetyStock,
                                    'label' => __($warehouseItem->getName() . ' Safety Stock')
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
