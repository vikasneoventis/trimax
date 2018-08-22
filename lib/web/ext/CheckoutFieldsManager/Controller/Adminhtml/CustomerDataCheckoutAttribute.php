<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Adminhtml Customer Data Checkout Attribute controller
 *
 */
abstract class CustomerDataCheckoutAttribute extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection
     */
    protected $collection;
    /**
     * CustomerDataCheckoutAttribute constructor.
     *
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection $collection,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->collection = $collection;
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Sales::sales_order');
        $resultPage->addBreadcrumb(__('Sales'), __('Sales'));
        $resultPage->addBreadcrumb(__('Orders'), __('Orders'));

        return $resultPage;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function getResult()
    {
        $orderId = $this->getRequest()->getParam('orderid');
        $checkoutFieldsData = $this->collection->getAitocCheckoutfieldsByOrderId((int)$orderId, true);
        $this->coreRegistry->register('checkout_fields_data', $checkoutFieldsData);
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aitoc_CheckoutFieldsManager::attributes');
    }
}
