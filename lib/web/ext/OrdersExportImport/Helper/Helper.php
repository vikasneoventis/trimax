<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Helper;

use \Magento\Framework\App\ResourceConnection;
/**
 * Class Helper
 *
 * @package Aitoc\OrdersExportImport\Helper
 */
class Helper extends \Magento\Framework\DB\Helper
{
    /**
     * @param $table
     * @return array
     */
    public function getFields($table)
    {
        $tableName = $this->_resource->getTableName($table);
        return array_keys($this->getConnection()->describeTable($tableName));
    }
}
