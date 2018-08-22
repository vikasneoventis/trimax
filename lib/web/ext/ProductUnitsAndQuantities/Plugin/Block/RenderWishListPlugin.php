<?php

namespace Aitoc\ProductUnitsAndQuantities\Plugin\Block;

use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Catalog\Model\Product;

class RenderWishListPlugin
{
    public function afterToHtml($subject, $result)
    {
        $productId = $subject->getItem()->getProductId();
        $result .= $subject->getLayout()
            ->createBlock('Aitoc\ProductUnitsAndQuantities\Block\Render')
            ->setTemplate('Aitoc_ProductUnitsAndQuantities::renderer/wishlist.phtml')
            ->setData(['productId' => $productId])
            ->toHtml();

        return $result;
    }
}
