<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Sales\Order;

/**
 * Aitoc plug-in: Adding checkout fields on the invoice page
 */
class AitocCheckoutFields extends \Magento\Framework\View\Element\Template
{
    use \Aitoc\CheckoutFieldsManager\Traits\CustomFields;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * AitocCheckoutFields constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->orderId  = $this->getOrderId();
    }

    /**
     * Retrieve current order model instance
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->registry->registry('current_order')->getEntityId();
    }
}
