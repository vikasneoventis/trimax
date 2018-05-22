<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer\Category\Create;

use Magento\Framework\Event\ObserverInterface;

class SaveBeforeObserver implements ObserverInterface
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authStorage;
    
    /**
     * Constructor
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     * @param \Magento\Backend\Model\Auth\Session $authStorage
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Backend\Model\Auth\Session $authStorage
    ) {
        $this->helper = $helper;
        $this->authStorage = $authStorage;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $category = $observer->getEvent()->getCategory();
            
            // existing category
            if ($category->getId()) {
                return;
            }
            
            if ($assignStoreId = $this->getConfigAllowedCategoryStore($category)) {
                $this->authStorage->setAitUpdateRoleAllowedCategories($assignStoreId);
            }
        }
    }
    
    /**
     * @param mixed $category
     */
    public function getConfigAllowedCategoryStore($category)
    {
        $categoriesByStore = $this->helper->getTree($this->helper::ADVANCED_CATEGORIES, true);
        $parentStoreIds    = $category->getParentCategory()->getStoreIds();

        $stores = [];
        // collect stores that linked with new category
        foreach ($this->helper->getAllowedStoreIds() as $id) {
            // get stores with shared categories and has allowed store
            if (in_array($id, $parentStoreIds) && !empty($categoriesByStore[$id])) {
                $stores[] = $id;
            }
        }

        return $stores;
    }
}
