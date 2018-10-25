<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Data\Collection;

use Magento\Framework\DB\Select;

class AbstractDb
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Sales
     */
    protected $salesHelper;

    /**
     * AbstractDb constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Sales $helper
    ) {
        $this->salesHelper = $helper;
    }

    /**
     * Add filters for sub-Admin
     * Only for sales collections
     *
     * @param $subject
     */
    public function beforeGetSize($subject)
    {
        if ($this->salesHelper->isSalesResource($subject->getResource())
            && $this->salesHelper->isAdvancedPermissionEnabled()
        ) {
            $this->salesHelper->addAllowedProductFilter($subject);
        }
    }

    /**
     * Make DISTINCT for Count.
     * Only for sales collections
     *
     * @param        $subject
     * @param Select $countSelect
     *
     * @return Select
     */
    public function afterGetSelectCountSql($subject, Select $countSelect)
    {
        if ($this->salesHelper->isSalesResource($subject->getResource())
            && $this->salesHelper->isAdvancedPermissionEnabled()
            && $countSelect->getPart('distinct')
        ) {
            $countSelect->reset(Select::COLUMNS);
            $countSelect->columns('COUNT(DISTINCT main_table.entity_id)');
        }

        return $countSelect;
    }
}
