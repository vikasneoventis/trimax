<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Product\Form;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\AuthorizationInterface;

class Category extends \Magento\Framework\Data\Form\Element\Multiselect
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendData;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory                    $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory          $factoryCollection
     * @param \Magento\Framework\Escaper                                      $escaper
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Data                                    $backendData
     * @param \Magento\Framework\View\LayoutInterface                         $layout
     * @param \Magento\Framework\Json\EncoderInterface                        $jsonEncoder
     * @param AuthorizationInterface                                          $authorization
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        AuthorizationInterface $authorization,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->jsonEncoder       = $jsonEncoder;
        $this->collectionFactory = $collectionFactory;
        $this->backendData       = $backendData;
        $this->authorization      = $authorization;
        $this->layout = $layout;
        if (!$this->isAllowed()) {
            $this->setType('hidden');
            $this->addClass('hidden');
        }
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values     = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addAttributeToSelect('name');
        $collection->addIdFilter($values);

        $options = [];

        foreach ($collection as $category) {
            $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }

        return $options;
    }

    /**
     * Get categories collection
     *
     * @return Collection
     */
    protected function _getCategoriesCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * Attach category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        if (!$this->isAllowed()) {
            return '';
        }
        $htmlId             = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions    = $this->jsonEncoder->encode($this->_getSelectorOptions());


        $return = <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        require(["jquery", "mage/mage"], function($){
            $('#{$htmlId}-suggest').mage('aitapTreeSuggest', {$selectorOptions});
        });
    </script>
HTML;

        return $return;
    }

    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return [
            'source'      => $this->backendData->getUrl('catalog/category/suggestCategories'),
            'valueField'  => '#' . $this->getHtmlId(),
            'className'   => 'category-select',
            'multiselect' => true,
            'showAll'     => true,
            'root_ids'    => $this->getRootIds(),
            'show_id'     => $this->getShowId()
        ];
    }

    /**
     * Whether permission is granted
     *
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->authorization->isAllowed('Magento_Catalog::categories');
    }
}
