<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Dashboard;

class Sales extends \Magento\Backend\Block\Dashboard\Sales
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::dashboard/salebar.phtml';

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * Sales constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                      $context
     * @param \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Module\Manager                            $moduleManager
     * @param \Aitoc\AdvancedPermissions\Helper\Data                       $helper
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $collectionFactory, $moduleManager, $data);
        $this->helper = $helper;
    }

    /**
     * Change collection
     *
     * @return $this|void
     */
    protected function _prepareLayout()
    {
        if (!$this->_moduleManager->isEnabled('Magento_Reports')) {
            return $this;
        }
        $isFilter   = $this->getRequest()->getParam('store') || $this->getRequest()->getParam('website')
            || $this->getRequest()
                ->getParam('group');
        $collection = $this->_collectionFactory->create()->calculateSales($isFilter);

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('main_table.store_id', $this->getRequest()->getParam('store'));
        } elseif ($this->getRequest()->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('main_table.store_id', ['in' => $storeIds]);
        } elseif ($this->getRequest()->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('main_table.store_id', ['in' => $storeIds]);
        } else {
            if ($storeIds = $this->helper->getAllowedStoreViewIds()) {
                $collection->addFieldToFilter('main_table.store_id', ['in' => $storeIds]);
            }
        }

        $collection->load();
        $sales = $collection->getFirstItem();

        $this->addTotal(__('Lifetime Sales'), $sales->getLifetime());
        $this->addTotal(__('Average Order'), $sales->getAverage());

        return $this;
    }
}
