<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form;

class AbstractElement
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @param $element
     */
    protected function globalAttributeCheck($element)
    {
        if (is_object($element) && $this->isNeedDisable()) {
            $element->setDisabled(true)->setReadonly(true);
        }
    }

    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @return bool
     */
    public function isNeedDisable()
    {
        return ($this->helper->isAdvancedPermissionEnabled()
            && !$this->helper->getRole()->getManageGlobalAttribute());
    }
}
