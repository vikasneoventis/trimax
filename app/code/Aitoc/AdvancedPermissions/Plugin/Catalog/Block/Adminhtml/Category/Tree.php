<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Category;

use Magento\Framework\Data\Tree\Node;
use Magento\Store\Model\Store;

class Tree
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $auth;

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    protected $resourceHelper;
    
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\Permissions
     */
    protected $permissions;
    
    protected $block;

    protected $categoriesDissalow;
    
    /**
     * Constructor
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $auth,
        \Aitoc\AdvancedPermissions\Model\Permissions $permissions
    ) {
        $this->helper          = $helper;
        $this->categoryFactory = $categoryFactory;
        $this->resourceHelper  = $resourceHelper;
        $this->jsonEncoder     = $jsonEncoder;
        $this->auth            = $auth;
        $this->permissions     = $permissions;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product $object
     * @param \Closure                                     $work
     * @param                                              $product
     *
     * @return mixed
     */
    public function afterGetSuggestedCategoriesJson(\Magento\Catalog\Block\Adminhtml\Category\Tree $block, $json)
    {
        $categories = $this->getJson($json);
        $categoryIdsDissalow = [];
        $tree = $this->helper->getTree();
        foreach ($tree as $value) {
            if (count($CategoryIdsDissalow) == 0) {
                $categoryIdsDissalow = $value;
            } else {
                $categoryIdsDissalow = array_merge($categoryIdsDissalow, array_diff($value, $categoryIdsDissalow));
            }
        }
        if (count($categoryIdsDissalow) > 0) {
            $this->categoriesDissalow = $this->_getFullCategories($categoryIdsDissalow);
            $json = $this->jsonEncoder->encode($this->getRecursive($categories));
        }

        return $json;
    }

    /**
     * @param $categories
     *
     * @return array
     */
    public function getRecursive($categories)
    {
        $newCategories = [];
        $count = 0;
        foreach ($categories as $key => $value) {
            if (isset($value['id']) && in_array($value['id'], $this->categoriesDissalow)) {
                $newCategories[$count]['id'] = $value['id'];
                if (isset($value['is_active'])) {
                    $newCategories[$count]['is_active'] = $value['is_active'];
                }
                if (isset($value['label'])) {
                    $newCategories[$count]['label'] = $value['label'];
                }
                if (isset($value['children'])) {
                    $newCategories[$count]['children'] = $this->getRecursive($value['children']);
                } else {
                    $newCategories[$count]['children'] = [];
                }
                $count++;
            }
        }

        return $newCategories;
    }

    /**
     * @param \Magento\Catalog\Block\Adminhtml\Category\Tree $block
     * @param                                                $json
     *
     * @return string
     */
    public function afterGetTreeJson(\Magento\Catalog\Block\Adminhtml\Category\Tree $block, $json)
    {
        if (!$this->helper->getScope()) {
            return $json;
        }
        
        $nodes = $this->permissions->getAllowedCategoriesTree(
            $this->getJson($json),
            $block->getStore()->getGroup()->getId()
        );

        return $this->jsonEncoder->encode($nodes);
    }

    /**
     * Get decode json
     *
     * @param $json
     *
     * @return mixed
     * @throws \Zend_Json_Exception
     */
    public function getJson($json)
    {
        return \Zend_Json::decode($json);
    }

    /**
     * Check availability of adding root category
     *
     * @return boolean
     */
    public function afterCanAddRootCategory(\Magento\Catalog\Block\Adminhtml\Category\Tree $block, $return)
    {
        if ($block->getStore()->getId() == Store::DEFAULT_STORE_ID && $this->helper->getScope()) {
            return false;
        }
        
        return $return;
    }
}
