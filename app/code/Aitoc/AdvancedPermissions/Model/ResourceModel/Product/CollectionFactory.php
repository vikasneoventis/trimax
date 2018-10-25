<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Model\ResourceModel\Product;

class CollectionFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * CollectionFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string                                    $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = '\\Aitoc\\AdvancedPermissions\\Model\ResourceModel\\Product\\Collection'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName  = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     *
     * @return \Magento\Reports\Model\ResourceModel\Product\Collection
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
