<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Model\ResourceModel;

class Role extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Role constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null                                              $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aitoc_advanced_permissions_role', 'role_id');
    }
}
