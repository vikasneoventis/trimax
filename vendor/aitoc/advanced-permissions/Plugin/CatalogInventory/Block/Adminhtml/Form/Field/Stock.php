<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\CatalogInventory\Block\Adminhtml\Form\Field;

class Stock extends \Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form\AbstractElement
{
    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @param \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock $element
     */
    public function beforeGetElementHtml(\Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock $element)
    {
        if ($this->isNeedDisable()) {
            $element->setDisabled('disabled')->setReadonly(true)->lock();
        }
    }
}
