<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

class Delete extends Warehouse
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        if ($warehouseId = $this->getRequest()->getParam('id')) {
            try {
                $warehouseModel = $this->_objectManager->create('Aitoc\MultiLocationInventory\Model\Warehouse');
                $warehouseModel->load($warehouseId);

                $warehouseModel->delete();
                $this->messageManager->addSuccess(__('Warehouse has been deleted.'));

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
