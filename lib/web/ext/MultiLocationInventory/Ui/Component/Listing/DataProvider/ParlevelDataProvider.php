<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Listing\DataProvider;

use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory;

/**
 * Class WarehouseDataProvider
 */
class ParlevelDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Warehouse collection
     *
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory
     */
    protected $collection;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->getCollection()
            ->prepareCollectionForParLevel()
            ->load();
        $items = $this->getCollection()->toArray();

        return $items;
    }
}
