<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Parlevel;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory as StockItemCollectionFactory;
use Aitoc\MultiLocationInventory\Model\Warehouse as WarehouseModel;
use Aitoc\MultiLocationInventory\Model\Supplier as SupplierModel;

/**
 * Class MassOrder
 *
 * @package Aitoc\MultiLcoationInventory\Controller\Adminhtml\Parlevel
 */
class MassOrder extends \Magento\Backend\App\Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_MultiLocationInventory::par_level';
    const EMAIL_TEMPLATE_ID = 'multilocationinventory_reorder_stock';
    const EMAIL_SENDER = 'general';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var StockItemCollectionFactory
     */
    private $stockItemCollectionFactory;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\Warehouse
     */
    private $warehouseModel;

    /**
     * @var \Aitoc\MultiLocationInventory\Model\Supplier
     */
    private $supplierModel;

    /**
     * @param Context $context
     * @param Filter  $filter
     */
    public function __construct(
        Context $context,
        Filter $filter,
        StockItemCollectionFactory $stockItemCollectionFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        WarehouseModel $warehouseModel,
        SupplierModel $supplierModel
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->warehouseModel = $warehouseModel;
        $this->supplierModel = $supplierModel;
        $this->filter = $filter;
        $this->stockItemCollectionFactory = $stockItemCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Order action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $warehouseArray = [];
        $supplierArray = [];
        $collection = $this->filter->getCollection($this->stockItemCollectionFactory->create());
        $collection->prepareCollectionForParLevel();
        $collectionSize = $collection->getSize();
        foreach ($collection->getItems() as $stockItem) {
            $warehouseArray[$stockItem->getWarehouseId()][] = $stockItem;
            if ($stockItem->getSupplierId()) {
                $supplierArray[$stockItem->getSupplierId()][] = $stockItem;
            }
        }

        $this->sendDataForReorder($warehouseArray, $supplierArray);
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been ordered.', $collectionSize));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    private function sendDataForReorder($warehouseData, $supplierData)
    {
        foreach ($warehouseData as $warehouseId => $warehouseItem) {
            $warehouseReorderItemsHtml = "";
            $this->warehouseModel->load($warehouseId);
            $warehouseTitle = $this->warehouseModel->getName();
            $warehouseEmail = $this->warehouseModel->getEmail();
            foreach ($warehouseItem as $warehouseStockItem) {
                $warehouseReorderItemsHtml .= '<b>' . $warehouseStockItem->getProductName() . ': </b>' .
                    $warehouseStockItem->getData('qty_to_order') .
                    ' ( from ' .  $warehouseStockItem->getData('supplier_title') . ')' .
                    '<br/>';
            }
            $this->sendEmail($warehouseTitle, $warehouseEmail, $warehouseReorderItemsHtml);
        }

        foreach ($supplierData as $supplierId => $supplierItem) {
            $supplierReorderItemsHtml = "";
            $this->supplierModel->load($supplierId);
            if (!$this->supplierModel->getCanReceiveEmail()) {
                continue;
            }
            $supplierTitle = $this->supplierModel->getTitle();
            $supplierEmail = $this->supplierModel->getEmail();
            foreach ($supplierItem as $warehouseStockItem) {
                $supplierReorderItemsHtml .= '<b>' . $warehouseStockItem->getProductName() . ': </b>' .
                    $warehouseStockItem->getData('qty_to_order') .
                    ' ( to ' .  $warehouseStockItem->getData('warehouse_name') . ')' .
                    '<br/>';
            }
            $this->sendEmail($supplierTitle, $supplierEmail, $supplierReorderItemsHtml);
        }
    }

    public function sendEmail($name, $email, $orderItems)
    {
        $alertSenderContact = self::EMAIL_SENDER;
        $alertEmailTemplateId = self::EMAIL_TEMPLATE_ID;

        $transport = $this->transportBuilder
            ->setTemplateIdentifier(
                $alertEmailTemplateId
            )
            ->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getStoreId()
                ]
            )
            ->setFrom(
                $alertSenderContact
            )
            ->setTemplateVars(
                [
                    'name'  => $name,
                    'order_items' => $orderItems
                ]
            )
            ->addTo(
                $email,
                $name
            )
            ->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $e) {
            $this->_logger->critical($e);
            return $this;
        }

        return $this;
    }
}
