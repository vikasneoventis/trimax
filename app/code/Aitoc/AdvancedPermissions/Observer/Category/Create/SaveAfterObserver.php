<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer\Category\Create;

use Magento\Framework\Event\ObserverInterface;

class SaveAfterObserver implements ObserverInterface
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
     * @var \Aitoc\AdvancedPermissions\Model\Stores $stores
     */
    protected $stores;
    
    /**
     * Constructor
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     * @param \Magento\Backend\Model\Auth\Session $authStorage
     * @param \Aitoc\AdvancedPermissions\Model\Stores $stores
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Backend\Model\Auth\Session $authStorage,
        \Aitoc\AdvancedPermissions\Model\Stores $stores
    ) {
        $this->helper = $helper;
        $this->authStorage = $authStorage;
        $this->stores = $stores;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $category = $observer->getEvent()->getDataObject();

            foreach ($this->authStorage->getAitUpdateRoleAllowedCategories() as $categoryStoreId) {
                $roleStore = $this->stores->getCollection()
                    ->addFieldToFilter('store_id', $categoryStoreId)
                    ->addFieldToFilter('advanced_role_id', $this->helper->getRole()->getId())
                    ->getFirstItem();

                if ($roleStore->getId()) {
                    $categoryIds = $roleStore->getCategoryIds();
                    $roleStore->setCategoryIds(implode(',', [$categoryIds, $category->getId()]));
                    $roleStore->save();
                }
            }
            $this->authStorage->setAitUpdateRoleAllowedCategories(null);
        }
    }
}
