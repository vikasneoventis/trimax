<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\AdvancedPermissions\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aitoc\AdvancedPermissions\Model\Permissions;

/**
 * Data provider for categories field of product page
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Categories extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Categories
{
    /**
     * @var Permissions
     */
    protected $permissions;
    
    /**
     * Constructor
     *
     * @param LocatorInterface $locator
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param DbHelper $dbHelper
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     * @param Permissions $permissions
     */
    public function __construct(
        LocatorInterface $locator,
        CategoryCollectionFactory $categoryCollectionFactory,
        DbHelper $dbHelper,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        Permissions $permissions
    ) {
        parent::__construct($locator, $categoryCollectionFactory, $dbHelper, $urlBuilder, $arrayManager);
        $this->permissions = $permissions;
    }

    /**
     * Retrieve categories tree
     *
     * @param string|null $filter
     * @return array
     */
    protected function getCategoriesTree($filter = null)
    {
        return $this->permissions->getAllowedCategoriesTree(parent::getCategoriesTree($filter));
    }
}
