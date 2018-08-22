<?php
namespace Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Campaign;

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
            'Aitoc\AbandonedCartAlertsPro\Model\Campaign',
            'Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Campaign'
        );
        parent::_construct();
    }
}
