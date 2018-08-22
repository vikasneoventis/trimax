<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Plugin\Block\Product\View\Type\Bundle;

class Option
{
    protected $_product;

    public function __construct(
        \Aitoc\PreOrders\Model\Product $product
    ) {
        $this->_product = $product;
    }

    public function aroundGetSelectionTitlePrice(\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $block, \Closure $proceed, $selection, $includeContainer = true)
    {
        $addInfo = "";
        $product = $this->_product->load($selection->getId());
        if ($product->getListPreorder()) {
            $addInfo = ($includeContainer ? '<span class="price-notice">' : '') . __('Pre-Order') . ($includeContainer ? '</span>' : '');
        }
        $priceTitle = '<span class="product-name">' . $block->escapeHtml($selection->getName()) . '</span>';
        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '') . '+'
            . $block->renderPriceString($selection, $includeContainer) . ($includeContainer ? '</span>' : '');
        $priceTitle .= $addInfo;
        return $priceTitle;
    }
}
