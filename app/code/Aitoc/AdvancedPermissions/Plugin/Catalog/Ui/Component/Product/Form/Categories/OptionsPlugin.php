<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Ui\Component\Product\Form\Categories;

use Magento\Catalog\Ui\Component\Product\Form\Categories\Options;

class OptionsPlugin
{
    /**
     * @var \Aitoc\AdvancedPermissions\Model\Permissions
     */
    protected $permissions;
    
    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Model\Permissions $permissions
    ) {
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function afterToOptionArray(
        \Magento\Catalog\Ui\Component\Product\Form\Categories\Options $subject,
        $options
    ) {
        return $this->permissions->getAllowedCategoriesTree($options);
    }
}
