<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

class Save extends Warehouse
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        if ($this->getRequest()->isPost() && ($postData = $this->getRequest()->getPostValue())) {
            try {
                $warehouseModel = $this->_objectManager->create('Aitoc\MultiLocationInventory\Model\Warehouse');
                if ($postData['warehouse_id']) {
                    $warehouseModel->load($postData['warehouse_id']);
                }
                $warehouseModel->setData($postData);
                if ($postData['warehouse_id'] == '') {
                    $warehouseModel->setId(null);
                }

                $warehouseModel->save();
                $this->messageManager->addSuccess(__('You saved the warehouse.'));

                $redirectResult->setPath('multilocationinventory/*/');
                return $redirectResult;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setPostData($postData);
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving. Please review the error log.')
                );
                $this->_getSession()->setPostData($postData);
            }
            $redirectResult->setUrl($this->_redirect->getRedirectUrl($this->getUrl('*')));
            return $redirectResult;
        }
        $redirectResult->setPath('multilocationinventory/*/');
        return $redirectResult;
    }
}
