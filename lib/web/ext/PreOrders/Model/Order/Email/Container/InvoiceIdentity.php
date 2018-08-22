<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\Order\Email\Container;

class InvoiceIdentity extends \Magento\Sales\Model\Order\Email\Container\InvoiceIdentity
{

    const XML_PATH_EMAIL_INVOICE_TEMPLATE = 'preorder/preorder_invoice/email_preorder_invoice_template';

    /**
     * Get template id for email-invoice with pre-order
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        if ($this->getConfigValue('preorder/preorder_invoice/active_invoice', $this->getStore()->getStoreId())) {
            return $this->getConfigValue(self::XML_PATH_EMAIL_INVOICE_TEMPLATE, $this->getStore()->getStoreId());
        } else {
            return $this->getConfigValue(\Magento\Sales\Model\Order\Email\Container\InvoiceIdentity::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
        }
    }
}
