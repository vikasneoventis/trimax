<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Product\Edit\Action\Attribute\Tab;

class Attributes extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\Attributes
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $attribute;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $attr;

    /**
     * Attributes constructor.
     *
     * @param \Magento\Backend\Block\Template\Context               $context
     * @param \Magento\Framework\Registry                           $registry
     * @param \Magento\Framework\Data\FormFactory                   $formFactory
     * @param \Magento\Catalog\Model\ProductFactory                 $productFactory
     * @param \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeAction
     * @param \Aitoc\AdvancedPermissions\Helper\Data                $helper
     * @param \Magento\Eav\Model\Entity\Attribute                   $attribute
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute    $attr
     * @param array                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeAction,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Eav\Model\Entity\Attribute $attribute,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attr,
        array $data
    ) {
        parent::__construct($context, $registry, $formFactory, $productFactory, $attributeAction, $data);
        $this->helper    = $helper;
        $this->attribute = $attribute;
        $this->attr      = $attr;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        if ($this->helper->getRole()->getManageGlobalAttribute()) {
            $allowGlobal[] = 1;
        }
        if ($this->helper->getRole()->getScope() == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_STORE) {
            $allowGlobal[] = 0;
        }
        if ($this->helper->getRole()->getScope() == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_WEBSITE) {
            $allowGlobal[] = 0;
            $allowGlobal[] = 2;
        }

        $attr = $this->attribute->load($element->getId(), "attribute_code");
        $cav  = $this->attr->load($attr->getId());
        // Add name attribute to checkboxes that correspond to multiselect elements
        $nameAttributeHtml = $element->getExtType() === 'multiple' ? 'name="' . $element->getId() . '_checkbox"' : '';
        $elementId         = $element->getId();
        $dataAttribute     = "data-disable='{$elementId}'";
        $dataCheckboxName  = 'toggle_' . $elementId;
        $checkboxLabel     = __('Change');

        if ($this->helper->isAdvancedPermissionEnabled()
            && $attr->getEntityType()->getId() == 4
            && !in_array(
                $cav->getIsGlobal(),
                $allowGlobal
            )
        ) {
            return '';
        }

        $html = <<<HTML
<span class="attribute-change-checkbox">
    <input type="checkbox" id="$dataCheckboxName" name="$dataCheckboxName" class="checkbox" $nameAttributeHtml onclick="toogleFieldEditMode(this, '{$elementId}')" $dataAttribute />
    <label class="label" for="$dataCheckboxName">
        {$checkboxLabel}
    </label>
</span>
HTML;
        if ($elementId === 'weight') {
            $html .= <<<HTML
<script>require(['Magento_Catalog/js/product/weight-handler'], function (weightHandle) {
    weightHandle.hideWeightSwitcher();
});</script>
HTML;
        }

        return $html;
    }
}
