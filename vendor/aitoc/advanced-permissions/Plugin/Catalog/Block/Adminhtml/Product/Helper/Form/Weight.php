<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form;

class Weight extends AbstractElement
{
    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @param \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight $element
     */
    public function beforeGetElementHtml(\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight $element)
    {
        if ($this->isNeedDisable()) {
            /** @noinspection PhpUndefinedMethodInspection */
            $element->setDisabled('disabled')->setReadonly(true);
        }
    }
}
