<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel;

use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\AttributeInterface;

/**
 * Class SaveHandler
 */
class SaveHandler implements AttributeInterface
{
    /**
     * @var Warehouse
     */
    protected $warehouseResource;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Warehouse $warehouseResource
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        Warehouse $warehouseResource,
        MetadataPool $metadataPool
    ) {
        $this->warehouseResource = $warehouseResource;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param string $entityType
     * @param array $entityData
     * @param array $arguments
     * @return array
     * @throws \Exception
     */
    public function execute($entityType, $entityData, $arguments = [])
    {
        $linkField = $this->metadataPool->getMetadata($entityType)->getLinkField();
        if (isset($entityData['store_ids'])) {
            $storeIds = $entityData['store_ids'];
            if (!is_array($storeIds)) {
                $storeIds = explode(',', (string)$storeIds);
            }
            $this->warehouseResource->bindWarehouseToEntity($entityData[$linkField], $storeIds, 'store');
        }

        if (isset($entityData['customer_group_ids'])) {
            $customerGroupIds = $entityData['customer_group_ids'];
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->warehouseResource->bindWarehouseToEntity($entityData[$linkField], $customerGroupIds, 'customer_group');
        }
        return $entityData;
    }
}
