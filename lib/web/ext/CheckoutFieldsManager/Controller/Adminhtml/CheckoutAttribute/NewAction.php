<?php
namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

class NewAction extends \Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
