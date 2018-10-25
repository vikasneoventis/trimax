<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Edit\Tab;

use Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form\AbstractElement;

class Inventory extends AbstractElement
{
    /**
     * @param \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Inventory $object
     * @param                                                             $result
     *
     * @return bool
     */
    public function afterIsReadonly(\Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Inventory $object, $result)
    {
        return $this->isNeedDisable() ? true : $result;
    }
}
