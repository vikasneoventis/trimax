<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Profile
 *
 * @package Aitoc\OrdersExportImport\Model\ResourceModel
 */
class Profile extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aitoc_export_profile', 'profile_id');
    }
}
