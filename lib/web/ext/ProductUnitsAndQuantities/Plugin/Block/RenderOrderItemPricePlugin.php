<?php

namespace Aitoc\ProductUnitsAndQuantities\Plugin\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product\Type;
use Aitoc\ProductUnitsAndQuantities\Helper\Data as AitocUnitsHelper;

class RenderOrderItemPricePlugin
{
    private $orderItemId;
    private $productModel;
    private $registry;
    private $aitocUnitsHelper;

    public function __construct(
        Product $productModel,
        Registry $registry,
        AitocUnitsHelper $aitocUnitsHelper
    ) {
        $this->productModel = $productModel;
        $this->registry = $registry;
        $this->aitocUnitsHelper = $aitocUnitsHelper;
    }

    public function afterGetUnitDisplayPriceInclTax($subject, $result)
    {
        $this->registry->register('aitoc_get_order_item_price_on_frontend', true);
        return $result;
    }

    public function afterGetUnitDisplayPriceExclTax($subject, $result)
    {
        $this->registry->register('aitoc_get_order_item_price_on_frontend', true);
        return $result;
    }

    public function afterFormatPrice($subject, $result)
    {
        if ($this->registry->registry('aitoc_get_order_item_price_on_frontend')) {
            $this->processParams($subject->getItem());

            if ($this->productModel->getTypeId() == Type::TYPE_BUNDLE) {
                return $result;
            }

            $unitsData = $this->aitocUnitsHelper->getProductParams($this->productModel->getId());
            $unit = $unitsData['price_per'];
            $divider = $unitsData['price_per_divider'];

            $result .= ' ' . $divider . ' ' . $unit;
            $this->registry->unregister('aitoc_get_order_item_price_on_frontend');
        }
        return $result;
    }

    private function processParams($orderItem)
    {
        $this->orderItemId = $orderItem->getData('item_id') ?: $orderItem->getData('order_item_id');
        $productId = $orderItem->getData('product_id');
        $this->productModel->load($productId);
    }
}
