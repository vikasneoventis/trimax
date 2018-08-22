<?php
namespace Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Alert;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model and resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Aitoc\AbandonedCartAlertsPro\Model\Alert',
            'Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Alert'
        );
        parent::_construct();
    }
}
