<?php

namespace Aitoc\ProductUnitsAndQuantities\Plugin\Block;

use Magento\Catalog\Model\Product;

class RenderCartPlugin
{
    private $productId;
    private $itemId;
    private $productModel;

    public function __construct(
        Product $productModel
    ) {
        $this->productModel = $productModel;
    }

    public function beforeGetActions($subject, $item)
    {
        $this->itemId = $item->getData('item_id');
        $this->productId = $item->getData('product_id');

        $this->productModel->load($this->productId);
    }

    public function afterGetActions($subject, $result)
    {
        $productType = $this->productModel->getTypeId();

        if ($productType == 'bundle') {
            return $result;
        }

        $result .= $subject->getLayout()
            ->createBlock('Aitoc\ProductUnitsAndQuantities\Block\Render')
            ->setTemplate('Aitoc_ProductUnitsAndQuantities::renderer/cart.phtml')
            ->setData(['productId' => $this->productId, 'itemId' => $this->itemId])
            ->toHtml();

        return $result;
    }
}
