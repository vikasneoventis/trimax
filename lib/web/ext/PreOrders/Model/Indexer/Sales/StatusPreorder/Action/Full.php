<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\Indexer\Sales\StatusPreorder\Action;

class Full extends \Aitoc\PreOrders\Model\Indexer\Sales\StatusPreorder\AbstractAction
{
    /**
     * Execute Full reindex
     *
     * @param null $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($ids = null)
    {
        try {
            $this->reindexAll();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }
}
