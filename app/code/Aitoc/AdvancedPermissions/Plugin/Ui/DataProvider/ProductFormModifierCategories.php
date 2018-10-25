<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
 
namespace Aitoc\AdvancedPermissions\Plugin\Ui\DataProvider;
    
class ProductFormModifierCategories
{
    /**
     * @var \Aitoc\AdvancedPermissions\Model\Permissions
     */
    protected $permissions;
    
    /**
     * Product constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Model\Permissions $$permissions
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Model\Permissions $permissions
    ) {
        $this->permissions = $permissions;
    }

    /**
     * Apply regular filters like collection filters
     *
     * @param AbstractDb $collection
     * @param Filter     $filter
     *
     * @return void
     */
    public function afterModifyMeta(
        \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Categories $object,
        $meta
    ) {
        $metaCategories = &$meta['product-details']['children']['container_category_ids']['children']['category_ids']['arguments']['data']['config']['options'];
        $metaCategories = $this->permissions->getAllowedCategoriesTree($metaCategories);
        return $meta;
    }
}
