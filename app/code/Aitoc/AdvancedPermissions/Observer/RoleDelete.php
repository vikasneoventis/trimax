<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;

class RoleDelete implements ObserverInterface
{
    /**
     * @var \Aitoc\AdvancedPermissions\Model\Role
     */
    private $role;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\Stores
     */
    protected $stores;

    /**
     * RoleSave constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     * @param \Aitoc\AdvancedPermissions\Model\Role  $roleAdv
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Model\Role $roleAdv,
        \Aitoc\AdvancedPermissions\Model\Stores $storesAdv
    ) {
        $this->role   = $roleAdv;
        $this->stores = $storesAdv;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event        = $observer->getEvent();
        $role         = $event->getDataObject();
        $roleAdvanced = $this->role->loadOriginal($role->getId());

        $this->stores->getCollection()
            ->setRoleFilter($roleAdvanced->getId())
            ->walk('delete');

        $roleAdvanced->delete();

        return;
    }
}
