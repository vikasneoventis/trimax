<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
class Index extends Supplier
{
    public function execute()
    {
        return $this->createActionPage();
    }
}
