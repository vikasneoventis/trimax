<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form;

class Category extends AbstractElement
{

    /**
     * @var \Magento\Category\Model\Category
     */
    protected $category;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Category constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     * @param \Magento\Catalog\Model\Category        $category
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Catalog\Model\Category $category,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->category        = $category;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($helper);
    }

    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @param $element
     */
    public function beforeGetElementHtml($element)
    {
        $this->globalAttributeCheck($element);
    }

    /**
     * @param \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category $object
     * @param                                                               $values
     *
     * @return mixed
     */
    public function afterGetValues(\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category $object, $values)
    {
        $tree = $this->helper->getTree();
        if ($this->helper->isAdvancedPermissionEnabled()) {
            if (count($tree) > 0) {
                $cats = [];
                foreach ($tree as $value) {
                    if (count($cats) == 0) {
                        $cats = $value;
                    } else {
                        $cats = array_merge($cats, array_diff($value, $cats));
                    }
                }
                if (count($cats) > 0) {
                    $cats = $this->getFullCategories($cats);

                    if (count($values)) {
                        foreach ($values as $key => $value) {
                            if (!in_array($value['value'], $cats)) {
                                unset($values[$key]);
                            }
                            $categoryParents = $this->category->load($value['value'])->getParentIds();
                            if (!count(array_intersect($cats, $categoryParents))) {
                                unset($values[$key]);
                            }
                        }
                    }
                }
            }
            if (!$this->helper->getRole()->getManageGlobalAttribute()) {
                $values = [];
            }
        }

        return $values;
    }

    /**
     * Get All Categories
     *
     * @param $elements
     *
     * @return array
     */
    protected function getFullCategories($elements)
    {
        $categories = [];
        foreach ($elements as $element) {
            $category = $this->categoryFactory->create()->load($element);
            $ids      = $category->getParentIds();
            if (!count($categories)) {
                $categories = $ids;
            } else {
                $categories = array_merge($categories, array_diff($ids, $categories));
            }
            $children   = $category->getChildren();
            $childs     = explode(",", $children);
            $categories = array_merge($categories, array_diff($childs, $categories));
        }
        if (count($categories)) {
            $elements = array_merge($elements, array_diff($categories, $elements));
        }
        foreach ($elements as $key => $value) {
            if (!$value) {
                unset($elements[$key]);
            }
        }

        return $elements;
    }
}
