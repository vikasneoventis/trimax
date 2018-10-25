<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\ResourceModel\Online\Grid;

class Collection extends \Magento\Customer\Model\ResourceModel\Online\Grid\Collection
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param string                                                       $mainTable
     * @param string                                                       $resourceModel
     * @param \Magento\Customer\Model\Visitor                              $visitorModel
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                  $date
     * @param \Aitoc\AdvancedPermissions\Helper\Data                       $helper
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $resourceModel,
        \Magento\Customer\Model\Visitor $visitorModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Aitoc\AdvancedPermissions\Helper\Data $helper
    ) {
        $this->helper = $helper;
        
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $visitorModel,
            $date
        );
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult::_initSelect();
        $connection = $this->getConnection();
        $lastDate   = $this->date->gmtTimestamp() - $this->visitorModel->getOnlineInterval() * self::SECONDS_IN_MINUTE;
        $this->getSelect()->joinLeft(
            ['customer' => $this->getTable('customer_entity')],
            'customer.entity_id = main_table.customer_id',
            ['email', 'firstname', 'lastname']
        )->where(
            'main_table.last_visit_at >= ?',
            $connection->formatDate($lastDate)
        );
        
        if ($this->helper->isAdvancedPermissionEnabled()) {
            if (!$this->helper->getRole()->getShowAllCustomers()) {
                $this->getSelect()->where('customer.website_id IN (?)', $this->helper->getAllowedWebsiteIds());
            }
        }

        $expression = $connection->getCheckSql(
            'main_table.customer_id IS NOT NULL AND main_table.customer_id != 0',
            $connection->quote(\Magento\Customer\Model\Visitor::VISITOR_TYPE_CUSTOMER),
            $connection->quote(\Magento\Customer\Model\Visitor::VISITOR_TYPE_VISITOR)
        );
        $this->getSelect()->columns(['visitor_type' => $expression]);

        return $this;
    }
}
