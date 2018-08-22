<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Api\Data;

interface SupplierProductInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID     = 'entity_id';
    const SUPPLIER_ID   = 'supplier_id';
    const PRODUCT_ID    = 'product_id';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getEntityId();

    /**
     * @param int $id
     *
     * @return bool
     */
    public function setEntityId($id);

    /**
     * @return int|null
     */
    public function getSupplierId();

    /**
     * @param int $id
     *
     * @return bool
     */
    public function setSupplierId($id);

    /**
     * @return int|null
     */
    public function getProductId();

    /**
     * @param int $id
     *
     * @return bool
     */
    public function setProductId($id);
}
