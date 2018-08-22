<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Extension\Index\Tab\Alert;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Pending extends Generic implements TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Pending Alerts');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Pending Alerts');
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
