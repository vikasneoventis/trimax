<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Observer;

class CartAllowed implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * CartAllowed constructor.
     * @param \Aitoc\PreOrders\Helper\Data $helper
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function __construct(
        \Aitoc\PreOrders\Helper\Data $helper,
        \Magento\Quote\Model\Quote $quote
    ) {
        $this->_helper = $helper;
        $this->_quote = $quote;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        if (!$this->_helper->isMixedCartAllowed()) {
            $quote = $this->_quote->load($order->getQuoteId());
            if ($this->_helper->_validateQuoteItems($quote)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Order cannot be placed. It is not allowed to place order with pre-order and in-stock products.'));
            }
        }

    }
}
