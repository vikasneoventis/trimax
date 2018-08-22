<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Plugin\Model\Order;

class Shipment
{
    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $helper;

    /**
     * Shipment constructor.
     * @param \Aitoc\PreOrders\Helper\Data $helper
     */
    public function __construct(\Aitoc\PreOrders\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $ship
     * @param \Closure $work
     * @param $item
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function aroundAddItem(\Magento\Sales\Model\Order\Shipment $ship, \Closure $work, $item)
    {
        $item->setShipment($ship)->setParentId($ship->getId())->setStoreId($ship->getStoreId());
        if (!$item->getId()) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->getData('product_type') == \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE) {
                $isRegular = 0;
                $isRegular = $this->helper->bundleHaveReg($orderItem);
                if (!$isRegular) {
                    $tmp_total_qty = $ship->getTotalQty();
                    $ship->setTotalQty($tmp_total_qty - $item->getQty());

                    $item->setQty(0);
                }
            }
            if (isset($itemData['simple_sku'])) {
                $product = $this->helper->initProduct($item, $itemData['simple_sku']);
                if ($product->getListPreorder()) {
                    $item->setQty(0);
                }
            } else {
                $product = $this->helper->initProduct($item);
                if ($product->getListPreorder()) {
                    $item->setQty(0);
                }
            }
            $ship->getItemsCollection()->addItem($item);
        }

        return $ship;
    }
}
