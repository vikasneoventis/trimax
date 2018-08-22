<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

use Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

class Edit extends Warehouse
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        /** @var $model \Aitoc\MultiLocationInventory\Model\Warehouse */
        $model = $this->warehouseFactory->create();

        if ($warehouseId) {
            /** TODO: change model to resource model */
            $model->load($warehouseId);

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Warehouse no longer exists.'));
                return $this->_redirect('multilocationinventory/*/');
            }
        }

        // set entered data if was error when we do save
        $data = $this->_session->getWarehouseData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('warehouse');
        if (!empty($attributeData) && $warehouseId === null) {
            $model->addData($attributeData);
        }

        $this->coreRegistry->register('entity_warehouse', $model);

        $item = __('New Warehouse');
        if ($warehouseId) {
            $item = __('Edit Warehouse');
        }

        $resultPage = $this->createActionPage($item);
        $resultPage->getConfig()->getTitle()->prepend($warehouseId ? $model->getName() : __('New Warehouse'));

        return $resultPage;
    }
}
