<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Supplier;

class Edit extends Supplier
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $supplierId = $this->getRequest()->getParam('id');
        /** @var $model \Aitoc\MultiLocationInventory\Model\Warehouse */
        $model = $this->supplierFactory->create();

        if ($supplierId) {
            /** TODO: change model to resource model */
            $model->load($supplierId);

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Supplier no longer exists.'));
                return $this->_redirect('multilocationinventory/*/');
            }
        }

        // set entered data if was error when we do save
        $data = $this->_session->getSupplierData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('supplier');
        if (!empty($attributeData) && $supplierId === null) {
            $model->addData($attributeData);
        }

        $this->coreRegistry->register('entity_supplier', $model);

        $item = __('New Supplier');
        if ($supplierId) {
            $item = __('Edit Supplier');
        }

        $resultPage = $this->createActionPage($item);
        $resultPage->getConfig()->getTitle()->prepend($supplierId ? $model->getTitle() : __('New Supplier'));

        return $resultPage;
    }
}
