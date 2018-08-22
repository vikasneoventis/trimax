<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\Order\Email\Container;

class OrderIdentity extends \Magento\Sales\Model\Order\Email\Container\OrderIdentity
{

    const XML_PATH_EMAIL_PREORDER_TEMPLATE = 'preorder/preorder_order/email_preorder_order_template';

    /**
     * Get template id for email-order with pre-order
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        if ($this->getConfigValue('preorder/preorder_order/active_order', $this->getStore()->getStoreId())) {
            return $this->getConfigValue(self::XML_PATH_EMAIL_PREORDER_TEMPLATE, $this->getStore()->getStoreId());
        } else {
            return $this->getConfigValue(\Magento\Sales\Model\Order\Email\Container\OrderIdentity::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
        }
    }
}
