<?php
/**
 *
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

class Delete extends \Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $model = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
            $model->load($id);
            try {
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the product attribute.'));
                return $resultRedirect->setPath('aitoccheckoutfieldsmanager/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath(
                    'aitoccheckoutfieldsmanager/*/edit',
                    ['attribute_id' => $this->getRequest()->getParam('attribute_id')]
                );
            }
        }
        $this->messageManager->addError(__('We can\'t find an attribute to delete.'));
        return $resultRedirect->setPath('aitoccheckoutfieldsmanager/*/');
    }
}
