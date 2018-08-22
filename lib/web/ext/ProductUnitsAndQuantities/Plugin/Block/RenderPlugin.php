<?php

namespace Aitoc\ProductUnitsAndQuantities\Plugin\Block;

use Aitoc\ProductUnitsAndQuantities\Helper\Data as AitocUnitsHelper;

class RenderPlugin
{
    private $productId;
    private $aitocUnitsHelper;

    public function __construct(
        AitocUnitsHelper $aitocUnitsHelper
    ) {
        $this->aitocUnitsHelper = $aitocUnitsHelper;
    }

    public function beforeRenderAmount($subject, $amount, $arguments)
    {
        $priceId = $arguments['price_id'];

        $productId = explode('-', $priceId);
        if (isset($productId[2])) {
            $productId = $productId[2];

            $this->productId = $productId;
        }

    }

    public function afterRenderAmount($subject, $result)
    {
        if (!$this->productId) {
            return $result;
        }

        $unitsData = $this->aitocUnitsHelper->getProductParams($this->productId);
        $unit = $unitsData['price_per'];
        $divider = $unitsData['price_per_divider'];

        $result .= ' ' . $divider . ' ' . $unit;

        return $result;
    }
}
