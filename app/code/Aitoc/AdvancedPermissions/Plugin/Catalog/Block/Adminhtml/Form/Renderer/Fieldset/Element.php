<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Form\Renderer\Fieldset;

class Element
{
    /**
     * Element constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(\Aitoc\AdvancedPermissions\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element $object
     */
    public function beforeGetElementHtml(\Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element $object)
    {
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return;
        }
        $element   = $object->getElement();
        $attribute = $element->getEntityAttribute();
        if ($attribute && $attribute->isScopeGlobal() && !$this->helper->getRole()->getManageGlobalAttribute()) {
            $element->setDisabled(true);
        }
    }
}
