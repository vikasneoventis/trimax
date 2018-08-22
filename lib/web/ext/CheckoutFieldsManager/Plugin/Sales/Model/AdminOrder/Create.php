<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Sales\Model\AdminOrder;

class Create
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Save constructor.
     *
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function afterCreateOrder(\Magento\Sales\Model\AdminOrder\Create $object, $order)
    {
        if ($order->getId()) {
            $this->registry->register('current_order', $order);
        }

        return $order;
    }
}
