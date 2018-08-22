<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Campaign\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('campaign_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Campaign Settings'));
    }
}
