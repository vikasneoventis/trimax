<?php
namespace Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Statistic;

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
            'Aitoc\AbandonedCartAlertsPro\Model\Statistic',
            'Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Statistic'
        );
        parent::_construct();
    }
}
