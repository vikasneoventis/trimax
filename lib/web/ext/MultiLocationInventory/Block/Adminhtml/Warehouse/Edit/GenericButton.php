<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Block\Adminhtml\Warehouse\Edit;

use Magento\Backend\Block\Widget\Context;
use Aitoc\OrdersExportImport\Api\ProfileRepositoryInterface;
use Magento\CatalogRule\Controller\RegistryConstants;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 *
 * @package Aitoc\MultiLocationInventory\Block\Adminhtml\Warehouse\Edit
 */
class GenericButton
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Context
     */
    private $context;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param Context $context
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->context = $context;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * Return the current Warehouse Id.
     *
     * @return int|null
     */
    public function getWarehouseId()
    {
        $warehouse = $this->registry->registry('entity_warehouse');
        return $warehouse ? $warehouse->getWarehouseId() : null;
    }

    /**
     * Return the current Warehouse Id.
     *
     * @return int|null
     */
    public function isDefault()
    {
        $warehouse = $this->registry->registry('entity_warehouse');
        return $warehouse ? (bool)$warehouse->getIsDefault() : false;
    }
}
