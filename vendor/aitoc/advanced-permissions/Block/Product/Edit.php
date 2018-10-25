<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Product;

class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aitoc\AdvancedPermissions\Helper\Data           $helper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
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

    /**
     * Get level scope
     *
     * @return int|null
     */
    public function levelScope()
    {
        return $this->helper->getScope();
    }
}
