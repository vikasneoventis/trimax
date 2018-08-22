<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Form\DataProvider;

use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 *
 * @package Aitoc\MultiLocationInventory\Model\Warehouse
 */
class WarehouseDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\Collection
     */
    public $collection;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    public $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $warehouseCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $warehouseCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $warehouseCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $_warehouse) {
            $data = $_warehouse->getData();
            $this->loadedData[$_warehouse->getId()] = $data;
        }

        $data = $this->dataPersistor->get('mli_warehouse');
        if (!empty($data)) {
            $warehouse = $this->collection->getNewEmptyItem();
            $warehouse->setData($data);
            $this->loadedData[$warehouse->getId()] = $warehouse->getData();
            $this->dataPersistor->clear('mli_warehouse');
        }

        return $this->loadedData;
    }
}
