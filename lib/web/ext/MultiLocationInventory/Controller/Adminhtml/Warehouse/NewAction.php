<?php
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

use \Magento\Backend\App\Action;

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
