<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

/**
 * Abstract Warehouse entity resource collection model
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\Collection;

abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store associated with warehouse entities information map
     * @var array
     */
    protected $_associatedEntitiesMap = [];

    /**
     * Add store ids to warehouse data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_stores_to_result') && $this->_items) {
            foreach ($this->_items as $item) {
                $item->afterLoad();
            }
        }

        return $this;
    }

    /**
     * Init flag for adding warehouse store ids to collection result
     *
     * @param bool|null $flag
     * @return $this
     */
    public function addStoresToResult($flag = null)
    {
        $flag = $flag === null ? true : $flag;
        $this->setFlag('add_stores_to_result', $flag);
        return $this;
    }

    /**
     * Limit warehous collection by specific stores
     *
     * @param int $websiteId
     * @return $this
     */
    public function addStoreFilter($websiteId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('store');
        if (!$this->getFlag('is_store_table_joined')) {
            $this->setFlag('is_store_table_joined', true);
            if ($websiteId instanceof \Magento\Store\Model\Website) {
                $websiteId = $websiteId->getId();
            }
            $this->getSelect()->join(
                ['store' => $this->getTable($entityInfo['associations_table'])],
                $this->getConnection()->quoteInto('store.' . $entityInfo['entity_id_field'] . ' = ?', $websiteId)
                . ' AND main_table.' . $entityInfo['warehouse_id_field'] . ' = store.' . $entityInfo['warehouse_id_field'],
                []
            );
        }
        return $this;
    }

    /**
     * Provide support for store id filter
     *
     * @param string $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'stores') {
            return $this->addStoreFilter($condition);
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of warehouse's associated entity by specified entity type
     *
     * @param string $entityType
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('There is no information about associated entity type "%1".', $entityType)
        );
    }
}
