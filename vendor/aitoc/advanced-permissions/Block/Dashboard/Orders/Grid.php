<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Block\Dashboard\Orders;

class Grid extends \Magento\Backend\Block\Dashboard\Orders\Grid
{

    protected $_template = 'Magento_Backend::dashboard/grid.phtml';

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                      $context
     * @param \Magento\Backend\Helper\Data                                 $backendHelper
     * @param \Magento\Framework\Module\Manager                            $moduleManager
     * @param \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Aitoc\AdvancedPermissions\Helper\Data                       $helper
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $moduleManager,
            $collectionFactory,
            $data
        );
        $this->helper = $helper;
    }

    /**
     * Change condition for collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $isFilter = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');
        if (!$isFilter && $storeIds = $this->helper->getAllowedStoreViewIds()) {
                $this->getCollection()->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        return $this;
    }
}
