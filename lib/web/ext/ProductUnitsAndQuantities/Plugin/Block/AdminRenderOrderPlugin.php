<?php

namespace Aitoc\ProductUnitsAndQuantities\Plugin\Block;

use Magento\Catalog\Model\Product;

class AdminRenderOrderPlugin
{
    private $orderItemId;

    private $productModel;

    public function __construct(
        Product $productModel
    ) {
        $this->productModel = $productModel;
    }

    public function beforeGetItemHtml($subject, \Magento\Framework\DataObject $item)
    {
        $this->orderItemId = $item->getData('item_id');

        $productId = $item->getData('product_id');

        $this->productModel->load($productId);

        if (!$this->orderItemId) {
            $this->orderItemId = $item->getData('order_item_id');
        }
    }

    public function afterGetItemHtml($subject, $result)
    {
        $productType = $this->productModel->getTypeId();

        if ($productType == 'bundle') {
            return $result;
        }

        $result .= $subject->getLayout()
            ->createBlock('Aitoc\ProductUnitsAndQuantities\Block\Render')
            ->setTemplate('Aitoc_ProductUnitsAndQuantities::renderproductorder.phtml')
            ->setData(['orderItemId' => $this->orderItemId])
            ->toHtml();

        return $result;
    }
}
