<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;

class ChangeTemplateObserver implements ObserverInterface
{
    /**
     * @param mixed $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observer->getBlock()->setTemplate('Aitoc_AdvancedPermissions::helper/gallery.phtml');
    }
}
