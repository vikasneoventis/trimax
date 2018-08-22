<?php
namespace Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Stoplist;

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
            'Aitoc\AbandonedCartAlertsPro\Model\Stoplist',
            'Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Stoplist'
        );
        parent::_construct();
    }
}
