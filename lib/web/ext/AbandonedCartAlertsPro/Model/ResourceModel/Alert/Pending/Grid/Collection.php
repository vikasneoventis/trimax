<?php
namespace Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Alert\Pending\Grid;

class Collection extends \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Alert\Collection
{
    /**
     * Add condition
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('status', ['eq' => \Aitoc\AbandonedCartAlertsPro\Model\Alert::STATUS_PENDING]);
        return $this;
    }
}
