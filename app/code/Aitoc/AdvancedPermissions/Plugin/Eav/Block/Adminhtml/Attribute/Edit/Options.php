<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Eav\Block\Adminhtml\Attribute\Edit;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options as AttrOptions;

class Options
{
    /**
     * @var \Aitoc\AdvancedPermissions\Model\Store\StoreManager
     */
    protected $storeManager;

    /**
     * @param \Aitoc\AdvancedPermissions\Model\Store\StoreManager $storeManager
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Model\Store\StoreManager $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return array
     */
    public function aroundGetStores(AttrOptions $object, \Closure $closure)
    {
        return $this->storeManager->getStoresAll(true);
    }
}
