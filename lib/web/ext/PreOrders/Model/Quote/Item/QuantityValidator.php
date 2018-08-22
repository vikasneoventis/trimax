<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\Quote\Item;

class QuantityValidator extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator
{
    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    private $helper;

    /**
     * @var \Aitoc\PreOrders\Model\Product
     */
    private $product;


    /**
     * QuantityValidator constructor.
     *
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option    $optionInitializer
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem $stockItemInitializer
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface                               $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockStateInterface                                  $stockState
     * @param \Aitoc\PreOrders\Helper\Data                                                       $helper
     * @param \Aitoc\PreOrders\Model\Product                                                     $product
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $optionInitializer,
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem $stockItemInitializer,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Aitoc\PreOrders\Helper\Data $helper,
        \Aitoc\PreOrders\Model\Product $product
    ) {
        $this->helper = $helper;
        $this->product = $product;
        parent::__construct(
            $optionInitializer,
            $stockItemInitializer,
            $stockRegistry,
            $stockState
        );
    }

    /**
     * Check product inventory data when quote item quantity declaring
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Framework\Event\Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote()
            || $quoteItem->getQuote()
                ->getIsSuperMode()
        ) {
            return;
        }
        $qty = $quoteItem->getQty();
        $stockItem = $this->stockRegistry->getStockItem(
            $quoteItem->getProduct()->getId(),
            $quoteItem->getProduct()->getStore()->getWebsiteId()
        );
        if (!$stockItem instanceof \Magento\CatalogInventory\Api\Data\StockItemInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The stock item for Product is not valid.'));
        }
        $parentStockItem = false;
        if ($quoteItem->getParentItem()) {
            $product = $quoteItem->getParentItem()->getProduct();
            $parentStockItem = $this->stockRegistry->getStockItem(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );
        }
        if ($stockItem) {
            if ((!$stockItem->getIsInStock() || $parentStockItem && !$parentStockItem->getIsInStock())) {
                if (!$this->isPreOrder($quoteItem->getProduct()->getId())) {
                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                        __('This product is out of stock.')
                    );
                    $quoteItem->getQuote()->addErrorInfo(
                        'stock',
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                        __('Some of the products are out of stock.')
                    );

                    return;
                }
            } else {
                $this->_removeErrorsFromQuoteAndItem($quoteItem, \Magento\CatalogInventory\Helper\Data::ERROR_QTY);
            }
        }
        if (($options = $quoteItem->getQtyOptions()) && $qty > 0) {
            $qty = $quoteItem->getProduct()->getTypeInstance()->prepareQuoteItemQty($qty, $quoteItem->getProduct());
            $quoteItem->setData('qty', $qty);
            if ($stockItem) {
                $result = $this->stockState->checkQtyIncrements(
                    $quoteItem->getProduct()->getId(),
                    $qty,
                    $quoteItem->getProduct()->getStore()->getWebsiteId()
                );
                if ($result->getHasError()) {
                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY_INCREMENTS,
                        $result->getMessage()
                    );

                    $quoteItem->getQuote()->addErrorInfo(
                        $result->getQuoteMessageIndex(),
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY_INCREMENTS,
                        $result->getQuoteMessage()
                    );
                } else {
                    $this->_removeErrorsFromQuoteAndItem(
                        $quoteItem,
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY_INCREMENTS
                    );
                }
            }
            foreach ($options as $option) {
                $result = $this->optionInitializer->initialize($option, $quoteItem, $qty);
                if ($result->getHasError()) {
                    $option->setHasError(true);
                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                        $result->getMessage()
                    );

                    $quoteItem->getQuote()->addErrorInfo(
                        $result->getQuoteMessageIndex(),
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                        $result->getQuoteMessage()
                    );
                } else {
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, \Magento\CatalogInventory\Helper\Data::ERROR_QTY);
                }
            }
        } else {
            $result = $this->stockItemInitializer->initialize($stockItem, $quoteItem, $qty);
            if ($result->getHasError()) {
                $quoteItem->addErrorInfo(
                    'cataloginventory',
                    \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                    $result->getMessage()
                );
                $quoteItem->getQuote()->addErrorInfo(
                    $result->getQuoteMessageIndex(),
                    'cataloginventory',
                    \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                    $result->getQuoteMessage()
                );
            } else {
                $this->_removeErrorsFromQuoteAndItem($quoteItem, \Magento\CatalogInventory\Helper\Data::ERROR_QTY);
            }
        }
    }

    /**
     * Check product in pre-order
     *
     * @param $id
     *
     * @return int
     */
    protected function isPreOrder($id)
    {
        $product = $this->product->load($id);
        $preorder = 0;
        if ($product->getListPreorder()) {
            if (!$this->helper->getStockItem($product)) {
                if ($this->helper->isBackstockPreorderAllowed($product)) {
                    $preorder = 1;
                }
            } else {
                $preorder = 1;
            }
        }

        return $preorder;
    }
}
