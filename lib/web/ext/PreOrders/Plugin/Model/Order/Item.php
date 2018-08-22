<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Plugin\Model\Order;

class Item
{
    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $_helper;

    /**
     * Item constructor.
     * @param \Aitoc\PreOrders\Helper\Data $helper
     */
    public function __construct(
        \Aitoc\PreOrders\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @param $status
     * @return \Magento\Framework\Phrase
     */
    public function afterGetStatus(\Magento\Sales\Model\Order\Item $item, $status)
    {
        if ($item->getProductId()) {
            if (!is_array($item->getData('product_options'))) {
                $itemData = unserialize($item->getData('product_options'));
            } else {
                $itemData = $item->getData('product_options');
            }
            if (($item->getData('product_type') == \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE)
                && ($itemData['product_calculations'] && $itemData['shipment_type'])
            ) {
                $status = __('Pre-Ordered');
            }

            if (isset($itemData['simple_sku'])) {
                $product = $this->_helper->initProduct($item, $itemData['simple_sku']);
                if ($product->getListPreorder() || $this->_helper->isBackstockPreorderAllowed($product)) {
                    $status = __('Pre-Ordered');
                }
            } else {
                $product = $this->_helper->initProduct($item);
                if ($product->getListPreorder() || $this->_helper->isBackstockPreorderAllowed($product)) {
                    $status = __('Pre-Ordered');
                }
            }
        }

        return $status;
    }
}
