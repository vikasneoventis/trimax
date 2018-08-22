<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Form\DataProvider;

use Aitoc\MultiLocationInventory\Model\ResourceModel\Supplier\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 *
 * @package Aitoc\MultiLocationInventory\Model\Supplier
 */
class SupplierDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Aitoc\MultiLocationInventory\Model\ResourceModel\Supplier\Collection
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
     * @param CollectionFactory $supplierCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $supplierCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $supplierCollectionFactory->create();
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
        foreach ($items as $supplierItem) {
            $data = $supplierItem->getData();
            $this->loadedData[$supplierItem->getId()] = $data;
        }

        $data = $this->dataPersistor->get('mli_supplier');
        if (!empty($data)) {
            $supplier = $this->collection->getNewEmptyItem();
            $supplier->setData($data);
            $this->loadedData[$supplier->getId()] = $supplier->getData();
            $this->dataPersistor->clear('mli_supplier');
        }

        return $this->loadedData;
    }
}
