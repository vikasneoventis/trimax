<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Dashboard\Products;

class Viewed extends \Magento\Backend\Block\Dashboard\Tab\Products\Viewed
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    protected $_template = "Magento_Backend::dashboard/grid.phtml";

    /**
     * @var \Aitoc\AdvancedPermissions\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productsFactoryAdv;

    /**
     * Viewed constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                  $context
     * @param \Magento\Backend\Helper\Data                                             $backendHelper
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory           $productsFactory
     * @param \Aitoc\AdvancedPermissions\Model\ResourceModel\Product\CollectionFactory $productsFactoryAdv
     * @param \Aitoc\AdvancedPermissions\Helper\Data                                   $helper
     * @param array                                                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
        \Aitoc\AdvancedPermissions\Model\ResourceModel\Product\CollectionFactory $productsFactoryAdv,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $productsFactory, $data);
        $this->helper             = $helper;
        $this->productsFactoryAdv = $productsFactoryAdv;
    }

    /**
     * Change Collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        if ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId  = array_pop($storeIds);
            if ($storeIds = $this->helper->getAllowedStoreViewIds()) {
                $storeId = array_pop($storeIds);
            }
        } elseif ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $storeId  = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
            if ($storeIds = $this->helper->getAllowedStoreViewIds()) {
                $storeId = array_pop($storeIds);
            }
        }
        if (!$this->getParam('website') && !$this->getParam('group')) {
            $collection = $this->productsFactoryAdv->create()
                ->addAttributeToSelect('*')
                ->addViewsCount()
                ->setStoreId($storeId)
                ->addStoresFilter($storeIds);
        } else {
            $collection = $this->_productsFactory->create()
                ->addAttributeToSelect('*')
                ->addViewsCount()
                ->setStoreId($storeId)
                ->addStoreFilter($storeId);
        }

        $this->setCollection($collection);

        return \Magento\Backend\Block\Dashboard\Grid::_prepareCollection();
    }
}
