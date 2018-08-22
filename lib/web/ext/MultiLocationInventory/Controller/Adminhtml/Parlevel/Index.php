<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Parlevel;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Parlevel;

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
class Index extends Parlevel
{
    public function execute()
    {
        return $this->createActionPage();
    }
}
