<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Campaign;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize campaign edit block
     *
     * @return void
     */
    public function _construct()
    {
        $this->_objectId = 'campaign_id';
        $this->_blockGroup = 'Aitoc_AbandonedCartAlertsPro';
        $this->_controller = 'adminhtml_campaign';

        parent::_construct();

        if ($this->_isAllowedAction('Aitoc_AbandonedCartAlertsPro::save')) {
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save Campaign'),
                    'class' => 'save primary',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        }
        $this->buttonList->remove('back');
        $this->buttonList->remove('save');
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Get URL for "Save and Continue" button
     *
     * @return string
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'aitocabandonedcart/*/save',
            [
                '_current' => true,
                'back' => 'edit',
                'active_tab' => '{{tab_id}}'
            ]
        );
    }
}
