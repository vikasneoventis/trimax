<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Block\Adminhtml\Warehouse\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 *
 * @package Aitoc\MultiLocationInventory\Block\Adminhtml\Warehouse\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $warehouseId = $this->getWarehouseId();
        if ($warehouseId && !$this->isDefault()) {
            $data = [
                'label' => __('Delete Warehouse'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __('Are you sure you want to do this?') .
                    '\', \'' .
                    $this->urlBuilder->getUrl('*/*/delete', ['id' => $warehouseId]) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
