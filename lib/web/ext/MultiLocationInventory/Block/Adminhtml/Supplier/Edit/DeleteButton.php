<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Block\Adminhtml\Supplier\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 *
 * @package Aitoc\MultiLocationInventory\Block\Adminhtml\Supplier\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $supplierId = $this->getSupplierId();
        if ($supplierId) {
            $data = [
                'label' => __('Delete Supplier'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' .
                    __('Are you sure you want to do this?') .
                    '\', \'' .
                    $this->urlBuilder->getUrl('*/*/delete', ['id' => $supplierId]) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
