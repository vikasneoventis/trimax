<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Extension\Index\Tab\Alert;

class Sent extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Sent Alerts');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Sent Alerts');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
