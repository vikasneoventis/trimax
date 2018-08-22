<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\Order\View;

/**
 * Aitoc plug-in: Adding checkout fields on the order page in admin area
 */
class AitocInfo extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{

    /**
     * Show edit url for administrators
     */
    const SHOW_EDIT_URL = 1;

    /**
     * Adding our block's html data to order's information
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setTemplate('Magento_Sales::order/view/info.phtml');
        $block = $this->getLayout()->createBlock(
            'Aitoc\CheckoutFieldsManager\Block\Adminhtml\Sales\Order\Invoice\AitocCheckoutFields',
            'adminhtml_sales_order_view_aitocinfo'
        );
        $block->setEditUrl(self::SHOW_EDIT_URL);

        return parent::_toHtml() . $block->_toHtml();
    }
}
