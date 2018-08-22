<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Listing\Columns\Warehouse;

use Magento\Framework\Data\OptionSourceInterface;
use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory as WarehouseCollectionFactory;

/**
 * Supplier Options for Parlevel Grid
 */
class Options implements OptionSourceInterface
{
    /**
     * All Suppliers value
     */
    const ALL_WAREHOUSES = '0';

    /**
     *
     * @var WarehouseCollectionFactory $warehouseCollectionFactory
     */
    protected $warehouseCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param WarehouseCollectionFactory $warehouseCollectionFactory
     */
    public function __construct(WarehouseCollectionFactory $warehouseCollectionFactory)
    {
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options['All Warehouses']['label'] = __('All Warehouses');
        $this->options['All Warehouses']['value'] = self::ALL_WAREHOUSES;

        $warehouseCollection = $this->warehouseCollectionFactory->create();

        foreach ($warehouseCollection->getItems() as $warehouseItem) {
            $this->options[$warehouseItem->getId()]['label'] = __($warehouseItem->getName());
            $this->options[$warehouseItem->getId()]['value'] = $warehouseItem->getId();
        }


        return $this->options;
    }
}
