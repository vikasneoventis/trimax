<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Helper\Bundle;

class Configuration extends \Magento\Bundle\Helper\Catalog\Product\Configuration
{
    /**
     *  bundled selections with pre-order (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBundleOptions(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item)
    {
        $options = [];
        $product = $item->getProduct();
        $typeInstance = $product->getTypeInstance();
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : [];
        if ($bundleOptionsIds) {
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');
            $bundleSelectionIds = unserialize($selectionsQuoteItemOption->getValue());
            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $product);
                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = ['label' => $bundleOption->getTitle(), 'value' => []];
                        $bundleSelections = $bundleOption->getSelections();
                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                $preorder = '';
                                $productPre = \Magento\Framework\App\ObjectManager::getInstance()->get('Aitoc\PreOrders\Model\Product')->load($bundleSelection->getId());
                                if ($productPre->getListPreorder()) {
                                    $preorder .= ' <span style="font-weight:bold">(' . __('Pre-Order') . ' ' . $productPre->getPreorderdescript() . ')<span>';
                                }
                                $option['value'][] = $qty . ' x '
                                    . $this->escaper->escapeHtml($bundleSelection->getName())
                                    . ' '
                                    . $this->pricingHelper->currency(
                                        $this->getSelectionFinalPrice($item, $bundleSelection)
                                    )
                                    . $preorder;
                            }
                        }
                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }
}
