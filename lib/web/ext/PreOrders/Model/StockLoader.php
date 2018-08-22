<?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 */

namespace Aitoc\PreOrders\Model;

class StockLoader extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var array
     */
    protected $_productItems = [];

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Item
     */
    protected $_stockItem;

    /**
     * StockLoader constructor.
     * @param \Magento\CatalogInventory\Model\Stock\Item $itemStock
     */
    public function __construct(\Magento\CatalogInventory\Model\Stock\Item $itemStock)
    {
        $this->_stockItem = $itemStock;
    }

    /**
     * @param $product
     * @return $this
     */
    public function applyStockToProduct($product)
    {
        $this->applyStockToProductCollection([$product->getId() => $product]);
        return $this;
    }

    /**
     * Validate if products in collection or array have stock item that is used to validate Pre-Order status and load it if it's not found
     *
     * @param $collection
     * @return bool
     */
    public function applyStockToProductCollection($collection)
    {
        $ids = [];
        foreach ($collection as $_product) {
            $item = $_product->getStockItem();
            if (!$item) {
                // new product
                continue;
            }
            if (!is_null($item->getBackorders())) {
                continue;
            }
            if (isset($this->_productItems[$_product->getId()])) {
                $_product->setStockItem($this->_productItems[$_product->getId()]);
                continue;
            }
            $ids[] = $_product->getId();
        }
        if (sizeof($ids) == 0) {
            return false;
        }
        $inventoryCollection = $this->_stockItem->getCollection();
        $inventoryCollection->addStockFilter(\Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID)->addProductsFilter($ids);
        foreach ($inventoryCollection as $item) {
            $_product = $this->_getProductFromCollection($collection, $item);
            if ($_product) {
                $_product->setStockItem($item);
                $this->_productItems[$_product->getId()] = $item;
            }
        }
    }

    /**
     * Get product from collection
     *
     * @param $collection
     * @param $item
     * @return bool
     */
    protected function _getProductFromCollection($collection, $item)
    {
        if (is_object($collection)) {
            $_product = $collection->getItemById($item->getProductId());
        } else {
            if (isset($collection[$item->getProductId()])) {
                $_product = $collection[$item->getProductId()];
            } else {
                $found = false;
                foreach ($collection as $_product) {
                    if ($_product->getId() == $item->getProductId()) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $_product = false;
                }
            }
        }

        return $_product;
    }
}
