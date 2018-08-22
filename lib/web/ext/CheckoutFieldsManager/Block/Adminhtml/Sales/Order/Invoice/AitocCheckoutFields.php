<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\Sales\Order\Invoice;

/**
 * Aitoc plug-in: Adding checkout fields on the invoice page in admin area
 */
class AitocCheckoutFields extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    use \Aitoc\CheckoutFieldsManager\Traits\CustomFields {
        getCheckoutFieldsData as getCheckoutFieldsDataHtml;
    }

    /**
     * @var string - path to template
     */
    protected $_template = 'Aitoc_CheckoutFieldsManager::order/aitoccheckoutfields.phtml';

    /**
     * Get customer checkout attribute edit link
     *
     * @param int $orderId
     * @param string $label
     * @return string
     */
    public function getCustomerCheckoutAttributeEditLink($orderId, $label = '')
    {
        $url = '';
        if ($this->getEditUrl()) {
            $url = $this->getUrl('#');
            if ($orderId) {
                $url = $this->getUrl(
                    'aitoccheckoutfieldsmanager/customerdatacheckoutattribute/edit',
                    ['orderid' => $orderId]
                );
            }
            if (empty($label)) {
                $label = __('Edit');
            }

            $url = '<a href="' . $url . '">' . $label . '</a>';
        }

        return $url;
    }

    /**
     * Get customer checkout fields for order
     * Row contains HTML with label and value of checkout attribute
     *
     * @return array
     */
    public function getCheckoutFieldsData()
    {
        $this->orderId  = $this->getOrder()->getEntityId();

        return $this->getCheckoutFieldsDataHtml();
    }
}
