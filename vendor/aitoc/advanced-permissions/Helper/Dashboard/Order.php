<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Helper\Dashboard;

class Order extends \Magento\Backend\Helper\Dashboard\Order
{

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * Order constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                 $context
     * @param \Magento\Reports\Model\ResourceModel\Order\Collection $orderCollection
     * @param \Aitoc\AdvancedPermissions\Helper\Data                $helper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Reports\Model\ResourceModel\Order\Collection $orderCollection,
        \Aitoc\AdvancedPermissions\Helper\Data $helper
    ) {
        parent::__construct(
            $context,
            $orderCollection
        );
        $this->helper = $helper;
    }

    /**
     * Change collection
     *
     * @return void
     */
    protected function _initCollection()
    {
        $isFilter          = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');
        $this->_collection = $this->_orderCollection->prepareSummary($this->getParam('period'), 0, 0, $isFilter);
        if (!$isFilter && $this->_collection->isLive() && $storeIds = $this->helper->getAllowedStoreViewIds()) {
            $this->_collection->addFieldToFilter('main_table.store_id', ['in' => $storeIds])->load();
        } else {
            parent::_initCollection();
        }
    }
}
