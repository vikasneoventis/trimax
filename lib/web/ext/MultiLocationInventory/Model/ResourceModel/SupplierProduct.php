<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel;

class SupplierProduct extends \Aitoc\MultiLocationInventory\Model\ResourceModel\AbstractResource
{

    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('aitoc_mli_supplier_product', 'entity_id') ;
    }
}
