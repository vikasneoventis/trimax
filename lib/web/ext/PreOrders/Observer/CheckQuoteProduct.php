<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Observer;

class CheckQuoteProduct implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Aitoc\PreOrders\Model\StockLoader
     */
    protected $_stockLoader;

    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $_context;

    /**
     * CheckQuoteProduct constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Aitoc\PreOrders\Model\StockLoader $stockLoader
     * @param \Aitoc\PreOrders\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Aitoc\PreOrders\Model\StockLoader $stockLoader,
        \Aitoc\PreOrders\Helper\Data $helper
    ) {
        $this->_context = $context;
        $this->_stockLoader = $stockLoader;
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if ($this->_helper->isMixedCartAllowed()) {
            return;
        }
        $event = $observer->getEvent();
        $quoteItem = $event->getQuoteItem();
        $product = $event->getProduct();
        if ($quoteItem->getId()) {
            return $this;
        }
        $name = $quoteItem->getName();
        if ($quoteItem->getParentItem()) {
            $parentItem = $quoteItem->getParentItem();
            $name = $parentItem->getName();
        }
        $quote = $quoteItem->getQuote();
        $addedItem = null;
        foreach ($quote->getItemsCollection() as $item_id => $item) {
            if ($item === $quoteItem) {
                $addedItem = $item_id;
                break;
            }
        }
        if ($this->_helper->_validateQuoteItems($quote)) {
            $quote->removeItem($addedItem);
            $options = $quoteItem->getOptions();
            foreach ($options as $option) {
                $quoteItem->removeOption($option->getCode());
            }

            $request = $this->_context->getRequest();
            if ($request->getActionName() == 'reorder') {
                throw new \Magento\Framework\Exception\LocalizedException(__('Sorry, you can’t mix pre-order and in-stock items in one order.'));
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Sorry, you can’t add pre-order and in-stock items to the same cart.'));
            }
        }
    }
}
