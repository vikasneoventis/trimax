<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

class Delete extends Supplier
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        if ($supplierId = $this->getRequest()->getParam('id')) {
            try {
                $supplierModel = $this->_objectManager->create('Aitoc\MultiLocationInventory\Model\Supplier');
                $supplierModel->load($supplierId);

                $supplierModel->delete();
                $this->messageManager->addSuccess(__('Supplier has been deleted.'));

                $redirectResult->setPath('multilocationinventory/*/');
                return $redirectResult;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while deleting. Please review the error log.')
                );
            }
            $redirectResult->setUrl($this->_redirect->getRedirectUrl($this->getUrl('*')));
            return $redirectResult;
        }
        $redirectResult->setPath('multilocationinventory/*/');
        return $redirectResult;
    }
}
