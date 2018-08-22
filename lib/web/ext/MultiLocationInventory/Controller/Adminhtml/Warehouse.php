<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Aitoc\MultiLocationInventory\Model\WarehouseFactory;

/**
 * Warehouse controller
 */

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
abstract class Warehouse extends Action
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

    /**
     * @var WarehouseFactory
     */
    protected $warehouseFactory;

    /**
     * @param Context          $context
     * @param Registry         $coreRegistry
     * @param PageFactory      $resultPageFactory
     * @param WarehouseFactory $checkoutEavFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        WarehouseFactory $checkoutEavFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry      = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->warehouseFactory  = $checkoutEavFactory;
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
        $resultPage->setActiveMenu('Aitoc_MultiLocationInventory::catalog_inventory_warehouse');
        $resultPage->getConfig()->getTitle()->prepend(__('Warehouses'));

        return $resultPage;
    }
}
