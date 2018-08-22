<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

class Save extends Supplier
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
                $supplierModel = $this->_objectManager->create('Aitoc\MultiLocationInventory\Model\Supplier');
                if ($postData['entity_id']) {
                    $supplierModel->load($postData['entity_id']);
                }
                $supplierModel->setData($postData);
                if ($postData['entity_id'] == '') {
                    $supplierModel->setId(null);
                }

                $supplierModel->save();
                $this->messageManager->addSuccess(__('You saved the supplier.'));

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
