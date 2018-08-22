<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

class Index extends Warehouse
{
    /**
     * Warehouse Grid action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        return $this->createActionPage();
    }
}
