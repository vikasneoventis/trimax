<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\PreOrders\Model;

class StockStateProvider extends \Magento\CatalogInventory\Model\StockStateProvider
{

    /**
     *
     * Check qty for product with pre-order
     *
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param float|int $qty
     * @return bool
     */
    public function checkQty(\Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem, $qty)
    {
        if (!$this->qtyCheckApplicable) {
            return true;
        }
        if (!$stockItem->getManageStock()) {
            return true;
        }
        if ($stockItem->getQty() - $stockItem->getMinQty() - $qty < 0) {
            switch ($stockItem->getBackorders()) {
                case \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NONOTIFY:
                case \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NOTIFY:
                case \Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS:
                case \Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO:
                    break;
                default:
                    return false;
            }
        }

        return true;
    }
}
