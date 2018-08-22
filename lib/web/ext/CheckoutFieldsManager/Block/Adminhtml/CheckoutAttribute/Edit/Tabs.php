<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\CheckoutAttribute\Edit;

/**
 * Aitoc checkout fields left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('checkoutattribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Checkout attribute Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label'   => __('Properties'),
                'title'   => __('Properties'),
                'content' => $this->getChildHtml('main'),
                'active'  => true
            ]
        );

        $this->addTab(
            'labels_section',
            [
                'label' => __('Manage Labels'),
                'title' => __('Manage Labels'),
                'content' => $this->getChildHtml('labels'),
            ]
        );

        $this->addTab(
            'websitesstoreviews_section',
            [
                'label' => __('Websites / Store Views'),
                'title' => __('Websites / Store Views'),
                'content' => $this->getChildHtml('checkoutfieldsmanager_checkoutattribute_edit_tab_websitesstoreviews')
            ]
        );
        $this->addTab(
            'front',
            [
                'label' => __('Storefront Properties'),
                'title' => __('Storefront Properties'),
                'content' => $this->getChildHtml('front')
            ]
        );

        return parent::_beforeToHtml();
    }
}
