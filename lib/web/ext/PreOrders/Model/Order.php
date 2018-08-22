<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model;

class Order extends \Magento\Sales\Model\Order
{
    const STATE_PENDING_PREORDER = 'pendingpreorder';

    const STATE_PROCESSING_PREORDER = 'processingpreorder';

    const STATE_PENDING = 'pending';

    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $_helper;

    /**
     * Order constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param \Aitoc\PreOrders\Helper\Data $helper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory  $customAttributeFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
        \Aitoc\PreOrders\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->_helper = $helper;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $timezone,
            $storeManager,
            $orderConfig,
            $productRepository,
            $orderItemCollectionFactory,
            $productVisibility,
            $invoiceManagement,
            $currencyFactory,
            $eavConfig,
            $orderHistoryFactory,
            $addressCollectionFactory,
            $paymentCollectionFactory,
            $historyCollectionFactory,
            $invoiceCollectionFactory,
            $shipmentCollectionFactory,
            $memoCollectionFactory,
            $trackCollectionFactory,
            $salesOrderCollectionFactory,
            $priceCurrency,
            $productListFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * add History comment for order
     *
     * @param string $comment
     * @param bool $status
     * @return $this
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if (false === $status) {
            $status = $this->getStatus();
        } elseif (true === $status) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        } else {
            $this->setStatus($status);
        }

        list($orderStatusNew,$orderStatusPreorderNew) = $this->changeStatuses();

        $this->setData("status", $orderStatusNew);
        $this->setData("status_preorder", $orderStatusPreorderNew);

        $history = $this->_orderHistoryFactory->create()->setStatus(
            $orderStatusNew
        )->setComment(
            ''
        )->setEntityName(
            $this->entityType
        );
        $this->addStatusHistory($history);


        return $history;
    }

    /**
     * Get status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $status = $this->getStatus();
        if ($this->getStatus() != $this->getStatusPreorder()) {
            $status = $this->getStatusPreorder();
        }

        return $this->getConfig()->getStatusLabel($status);
    }

    /**
     * Get items haved pre-order
     *
     * @return int
     */
    public function isHavePreOrder()
    {
        return ($this->_helper->isHavePreorder($this));
    }

    /**
     * Set statuses
     *
     * @return array
     */
    public function changeStatuses()
    {
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')
            ->debug('chacngeStatus');
        $orderStatus = $this->getStatus();
        $orderStatusPreorder = $this->getStatusPreorder();
        $orderStatusNew = '';
        $orderStatusPreorderNew = '';
        $globalChange = 0;
        if (!$orderStatusPreorder) {
            $orderStatusPreorder = $orderStatus;
        }
        if ($orderStatus == self::STATE_PENDING_PREORDER) {
            $orderStatus = self::STATE_PENDING;
        } elseif ($orderStatus == self::STATE_PROCESSING_PREORDER) {
            $orderStatus = self::STATE_PROCESSING;
        }

        if (in_array($orderStatus, [self::STATE_PROCESSING_PREORDER, self::STATE_PROCESSING, self::STATE_COMPLETE])) {
            $haveregular = 0;
            $_items = $this->getItems();
            $globalChange = 1;
            $haveregular = $this->_helper->isHaveReg($_items, 0);
            if($orderStatus == self::STATE_COMPLETE) {
                $orderStatusPreorderNew = self::STATE_COMPLETE;
                $orderStatusNew = self::STATE_COMPLETE;
            } elseif (($haveregular == 1) && (in_array($orderStatus, [self::STATE_PROCESSING_PREORDER, self::STATE_PROCESSING]))) {
                $orderStatusPreorderNew = self::STATE_PROCESSING;
                $orderStatusNew = self::STATE_PROCESSING;
            } elseif (($haveregular == 0) && ($orderStatus == self::STATE_PROCESSING)) {
                $orderStatusPreorderNew = self::STATE_PROCESSING_PREORDER;
                $orderStatusNew = self::STATE_PROCESSING;
            } elseif (($haveregular == -2) && ($orderStatus == self::STATE_PROCESSING)) {
                $orderStatusPreorderNew = self::STATE_PROCESSING;
                $orderStatusNew = self::STATE_PROCESSING;
            } elseif (($haveregular == -2) && ($orderStatus != self::STATE_PROCESSING)) {
                $orderStatusPreorderNew = self::STATE_COMPLETE;
                $orderStatusNew = self::STATE_COMPLETE;
            } elseif ($haveregular == -1) {
                $orderStatusPreorderNew = self::STATE_PROCESSING_PREORDER;
                $orderStatusNew = self::STATE_PROCESSING_PREORDER;
            }
            if (($haveregular == -1) && ($orderStatus != self::STATE_PROCESSING_PREORDER)) {
                $orderStatusPreorderNew = self::STATE_PROCESSING_PREORDER;
                $orderStatusNew = self::STATE_PROCESSING;
                $this->setIsCustomerNotified(true);
            }
        } elseif (in_array($orderStatus, [self::STATE_PENDING, self::STATE_PENDING_PREORDER])) {
            $haveregular = 0;
            $_items = $this->getItems();
            $haveregular = $this->_helper->isHaveReg($_items, 1);
            $globalChange = 1;
            if (($haveregular == 0) && ($orderStatusPreorder == self::STATE_PENDING)) {
                $orderStatusPreorderNew = self::STATE_PENDING_PREORDER;
                $orderStatusNew = self::STATE_PENDING;
            } elseif (($haveregular != 0) && ($orderStatusPreorder == self::STATE_PENDING_PREORDER)) {
                $orderStatusPreorderNew = self::STATE_PENDING;
                $orderStatusNew = self::STATE_PENDING;
                $this->setIsCustomerNotified(true);
            }
        }

        if (!$globalChange) {
            $orderStatusPreorder = $orderStatus;
        }
        if ($this->getStatusPreorder() == self::STATE_HOLDED || !$orderStatusNew) {
            $orderStatusNew = $orderStatus;
        }

        if (!$orderStatusPreorderNew) {
            $orderStatusPreorderNew = $orderStatusPreorder;
        }

        return [$orderStatusNew, $orderStatusPreorderNew];
    }
}
