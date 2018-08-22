<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Observer;

class AfterSetQtyItemQuote implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Aitoc\PreOrders\Model\StockLoader
     */
    protected $_stockLoader;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * AfterSetQtyItemQuote constructor.
     * @param \Aitoc\PreOrders\Model\StockLoader $stockLoader
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Aitoc\PreOrders\Model\StockLoader $stockLoader,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_stockLoader = $stockLoader;
        $this->_messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $quoteItem = $event->getItem();

        $simpleProduct = $quoteItem->getProduct()->getCustomOption('simple_product');

        if (isset($simpleProduct)) {
            $_product = $simpleProduct->getProduct();
        } else {
            $_product = $quoteItem->getProduct();
        }
        $_product = \Magento\Framework\App\ObjectManager::getInstance()->get('Aitoc\PreOrders\Model\Product')->load($_product->getId());
        $this->_stockLoader->applyStockToProduct($_product);

        if ($_product->getListPreorder()) {
            $comma = ", ";
            if ($_product->getPreorderdescript() == "") {
                $comma = "";
            }
            $preordermsg = __('Pre-Order') . $comma . $_product->getPreorderdescript();
        }

        if (isset($preordermsg)) {
            if ($quoteItem->getMessage() == "") {
                $quoteItem->setMessage($preordermsg);
            }
        }
    }
}
