<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\Indexer\Sales;

class StatusPreorder implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var StatusPreorder\Action\Row
     */
    protected $_productStockIndexerRow;

    /**
     * @var StatusPreorder\Action\Rows
     */
    protected $_productStockIndexerRows;

    /**
     * @var StatusPreorder\Action\Full
     */
    protected $_productStockIndexerFull;


    public function __construct(
        StatusPreorder\Action\Row $productStockIndexerRow,
        StatusPreorder\Action\Rows $productStockIndexerRows,
        StatusPreorder\Action\Full $productStockIndexerFull
    ) {
        $this->_productStockIndexerRow = $productStockIndexerRow;
        $this->_productStockIndexerRows = $productStockIndexerRows;
        $this->_productStockIndexerFull = $productStockIndexerFull;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     *
     * @return void
     */
    public function execute($ids)
    {
        $this->_productStockIndexerRows->execute($ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->_productStockIndexerFull->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     *
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->_productStockIndexerRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     *
     * @return void
     */
    public function executeRow($id)
    {
        $this->_productStockIndexerRow->execute($id);
    }
}
