<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\Sales\Order\OrderCustomerData;

/**
 * Edit order address form container block
 */
class Create extends \Aitoc\CheckoutFieldsManager\Block\Adminhtml\Sales\Order\OrderCustomerData
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_mode = 'checkoutFields_create';
    }
}
