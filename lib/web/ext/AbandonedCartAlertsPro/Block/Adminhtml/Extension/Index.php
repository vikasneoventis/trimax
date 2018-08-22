<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Extension;

class Index extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    public $_template = 'extension/index.phtml';

    /**
     * Prepare layout
     *
     * @return void
     */
    public function _prepareLayout()
    {
        $this->addChild(
            'aitocabandonedcart_extension_index_tabs',
            'Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Extension\Index\Tabs'
        );

        parent::_prepareLayout();
    }
}
