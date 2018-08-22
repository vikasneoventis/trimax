<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Aitoc\MultiLocationInventory\Model\SupplierFactory;

/**
 * Warehouse controller
 */

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
abstract class Supplier extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /*
     * @var SupplierFactory
     */
    protected $supplierFactory;

    /**
     * @param Context          $context
     * @param Registry         $coreRegistry
     * @param PageFactory      $resultPageFactory
     * @param SupplierFactory  $supplierFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        SupplierFactory $supplierFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry      = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->supplierFactory = $supplierFactory;
    }

    /**
     * @param \Magento\Framework\Phrase|null $title
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }
        $resultPage->setActiveMenu('Aitoc_MultiLocationInventory::supplier');
        $resultPage->getConfig()->getTitle()->prepend(__('Suppliers'));

        return $resultPage;
    }
}
