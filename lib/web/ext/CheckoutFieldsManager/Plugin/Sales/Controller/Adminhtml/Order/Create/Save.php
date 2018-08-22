<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\CheckoutFieldsManager\Plugin\Sales\Controller\Adminhtml\Order\Create;

/**
 * Plugin for @see \Magento\Sales\Controller\Adminhtml\Order\Create\Save
 */
class Save
{
    const URL_REDIRECT_CREATE = 'aitoccheckoutfieldsmanager/customerdatacheckoutattribute/create';
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection
     */
    private $collection;

    /**
     * @param \Magento\Framework\Registry                                                   $registry
     * @param \Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection $collection
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection $collection
    ) {
        $this->registry   = $registry;
        $this->collection = $collection;
    }

    /**
     * After create order from admin, redirect to "Additional Fields"
     * for add Custom Fields to Order
     *
     * @param \Magento\Sales\Controller\Adminhtml\Order\Create\Save $object
     * @param \Magento\Backend\Model\View\Result\Redirect           $resultRedirect
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function afterExecute(\Magento\Sales\Controller\Adminhtml\Order\Create\Save $object, $resultRedirect)
    {
        if ($this->registry->registry('current_order')) {
            $orderId         = $this->registry->registry('current_order')->getId();
            $attributesArray = $this->collection->getAitocCheckoutfieldsByOrderId((int)$orderId, true);
            if (count($attributesArray)) {
                $resultRedirect->setPath(self::URL_REDIRECT_CREATE, ['orderid' => $orderId]);
            }
        }

        return $resultRedirect;
    }
}
