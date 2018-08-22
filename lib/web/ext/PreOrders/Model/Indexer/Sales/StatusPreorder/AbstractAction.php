<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\Indexer\Sales\StatusPreorder;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\ResourceConnection;

abstract class AbstractAction
{
    /**
     * Resource instance
     *
     * @var Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;


    /**
     * AbstractAction constructor.
     * @param ResourceConnection $resource
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        ResourceConnection $resource,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->_resource = $resource;
        $this->eventManager = $eventManager;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     *
     * @return void
     */
    abstract public function execute($ids);

    /**
     * Retrieve connection instance
     *
     * @return bool|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function _getConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection();
        }
        return $this->_connection;
    }


    /**
     * Returns table name for given entity
     *
     * @param string $entityName
     * @return string
     */
    protected function _getTable($entityName)
    {
        return $this->_resource->getTableName($entityName);
    }


    /**
     * Reindex all
     *
     * @return void
     */
    public function reindexAll()
    {
        $connection = $this->_getConnection();
        $select = $connection->select()
            ->from($this->_getTable('sales_order'), ['entity_id', 'status','status_preorder']);
        $assocc = $connection->fetchAssoc($select);
        $this->changeState($assocc);
    }

    /**
     * Refresh statuses index
     *
     * @param array $productIds
     * @return array Affected ids
     */
    protected function _reindexRows($ordersIds = [])
    {
        $connection = $this->_getConnection();
        if (!is_array($ordersIds)) {
            $ordersIds = [$ordersIds];
        }

        // retrieve product types by processIds
        $select = $connection->select()
            ->from($this->_getTable('sales_order'), ['entity_id', 'status','status_preorder'])
            ->where('entity_id IN(?)', $ordersIds);
        $assocc = $connection->fetchAssoc($select);
        $this->changeState($assocc);
    }


    /**
     * ADd status_preorder for orders
     * @param $assocc
     */
    public function changeState($assocc)
    {
        $connection = $this->_getConnection();
        foreach ($assocc as $order) {
            if (!$this->checkSynchronization($order['status'], $order['status_preorder'])) {
                $order = \Magento\Framework\App\ObjectManager::getInstance()->get('Aitoc\PreOrders\Model\Order')->load($order['entity_id']);
                $order->setStatusPreorder($order['status']);
                list($orderStatusNew, $orderStatusPreorderNew) = $order->changeStatuses();
                $order->setData("status", $orderStatusNew);
                $order->setData("status_preorder", $orderStatusPreorderNew);
                $condition = ['entity_id = ?' => (int)$order['entity_id']];
                $connection->update($this->_getTable('sales_order'), ["status" => $orderStatusNew, "status_preorder" => $orderStatusPreorderNew], $condition);
                $connection->update($this->_getTable('sales_order_grid'), ["status" => $orderStatusNew, "status_preorder" => $orderStatusPreorderNew], $condition);
            }
        }
    }

    /**
     * Check field status_preorder
     *
     * @param $status
     * @param $statusPreorder
     * @return bool
     */
    public function checkSynchronization($status, $statusPreorder)
    {
        if (!$statusPreorder) {
            return false;
        }
        if ($status != $statusPreorder) {
            if (!(($statusPreorder == \Aitoc\PreOrders\Model\Order::STATE_PENDING_PREORDER && $status == \Aitoc\PreOrders\Model\Order::STATE_PENDING)
                || ($statusPreorder == \Aitoc\PreOrders\Model\Order::STATE_PROCESSING_PREORDER && $status == \Aitoc\PreOrders\Model\Order::STATE_PROCESSING))
            ) {
                return false;
            }
        }
        return true;
    }
}
