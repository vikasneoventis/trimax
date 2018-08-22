<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml;

/**
 * Adminhtml catalog product attributes block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class CheckoutAttribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_controller = 'adminhtml_checkoutAttribute';
        $this->_blockGroup = 'Aitoc_CheckoutFieldsManager';
        $this->_headerText = __('Checkout Attributes');
        $this->_addButtonLabel = __('Add Checkout Attribute');
    }
}
