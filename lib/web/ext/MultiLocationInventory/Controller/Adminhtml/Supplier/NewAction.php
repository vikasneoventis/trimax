<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

use \Magento\Backend\App\Action;

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
class NewAction extends Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
