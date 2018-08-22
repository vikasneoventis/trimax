<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel;

use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\AttributeInterface;

/**
 * Class ReadHandler
 */
class ReadHandler implements AttributeInterface
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
        $entityId = $entityData[$linkField];

        $entityData['customer_group_ids'] = $this->warehouseResource->getCustomerGroupIds($entityId);
//        $entityData['website_ids'] = $this->warehouseResource->getWebsiteIds($entityId);

        return $entityData;
    }
}
