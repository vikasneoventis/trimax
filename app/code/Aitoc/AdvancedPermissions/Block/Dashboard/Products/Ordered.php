<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Dashboard\Products;

class Ordered extends \Magento\Backend\Block\Dashboard\Tab\Products\Ordered
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    protected $_template = "Magento_Backend::dashboard/grid.phtml";

    /**
     * Ordered constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                 $context
     * @param \Magento\Backend\Helper\Data                                            $backendHelper
     * @param \Magento\Framework\Module\Manager                                       $moduleManager
     * @param \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory
     * @param \Aitoc\AdvancedPermissions\Helper\Data                                  $helper
     * @param array                                                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
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
     * Change collection with condtition
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if (!$this->_moduleManager->isEnabled('Magento_Sales')) {
            return $this;
        }
        if ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId  = array_pop($storeIds);
        } elseif ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $storeId  = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
            if ($storeIds = $this->helper->getAllowedStoreViewIds()) {
                $storeId = $storeIds;
            }
        }
        $collection = $this->_collectionFactory
            ->create()
            ->setModel('Magento\Catalog\Model\Product')
            ->addStoreFilter($storeId);
        $this->setCollection($collection);

        return \Magento\Backend\Block\Dashboard\Grid::_prepareCollection();
    }

    /**
     * Change collection with condition
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        if (!$this->_moduleManager->isEnabled('Magento_Sales')) {
            return $this;
        }
        if ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId  = array_pop($storeIds);
        } elseif ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $storeId  = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
            if ($storeIds = $this->helper->getAllowedStoreViewIds()) {
                $storeId = $storeIds;
            }
        }

        $collection = $this->_collectionFactory
            ->create()
            ->setModel('Magento\Catalog\Model\Product')
            ->addStoreFilter($storeId);
        $this->setCollection($collection);

        return \Magento\Backend\Block\Dashboard\Grid::_prepareCollection();
    }
}
