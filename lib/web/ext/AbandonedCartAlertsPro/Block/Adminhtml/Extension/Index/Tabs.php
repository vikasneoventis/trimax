<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Extension\Index;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var string
     */
    public $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('aitocabandonedcart_extension_index_tabs');
        $this->setDestElementId('aitocabandonedcart_extension_index_content');
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        $campaignsUrl = $this->getUrl('*/campaign/edit', ['id' => 1]);
        $this->addTab(
            'campaigns',
            [
                'label' => __('Campaigns'),
                'content' => '<a href="' . $campaignsUrl . '">' . __('Edit Default Campaign') . '</a>'
            ]
        );

        $this->addTab(
            'alert-pending',
            [
                'label' => __('Pending Alerts'),
                'url' => $this->getUrl('aitocabandonedcart/alert/pending', ['_current' => true]),
                'class' => 'ajax'
            ]
        );

        $this->addTab(
            'alert-sent',
            [
                'label' => __('Sent Alerts'),
                'url' => $this->getUrl('aitocabandonedcart/alert/sent', ['_current' => true]),
                'class' => 'ajax'
            ]
        );

        $this->addTab(
            'statistic',
            [
                'label' => __('Statistics'),
                'content' => $this->getLayout()->createBlock(
                    'Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Extension\Index\Tab\Statistic',
                    'aitocabandonedcart_extension_tab_statistic'
                )->toHtml()
            ]
        );

        $this->addTab(
            'about',
            [
                'label' => __('About'),
                'content' => $this->getLayout()->createBlock(
                    'Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Extension\Index\Tab\About',
                    'aitocabandonedcart_extension_tab_about'
                )->toHtml()
            ]
        );

        return parent::_prepareLayout();
    }
}
