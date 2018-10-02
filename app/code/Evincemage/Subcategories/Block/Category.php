<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Subcategories\Block;

use Magento\Framework\View\Element\Template;

class Category extends \Magento\Catalog\Block\Category\View
{
    /**
     * Category constructor.
     * @param Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Image $image
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Image $image,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryFactory  $categoryFactory,
        array $data = array())
    {
        parent::__construct($context, $layerResolver, $registry, $categoryHelper,$data);
        $this->_categoryFactory = $categoryFactory;
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getCategoryList()
    {
        $_category  = $this->getCurrentCategory();
        $collection = $this->_categoryFactory->create()
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', 1)
            ->setOrder('position', 'ASC')
            ->addIdFilter($_category->getChildren());
        return $collection;

    }

    /**
     * @param $imageName
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryThumbImage($imageName) {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );

        return  $mediaDirectory.'catalog/category/'.$imageName;

    }

    /**
     * @return string
     */
    public function getPlaceholderImage(){
        return $this->image->getPlaceholder('image');
    }
}