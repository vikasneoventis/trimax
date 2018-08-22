<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product attributes grid
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\CheckoutAttribute;

use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends AbstractGrid
{
    /**
     * @var \Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->collectionFactory = $collectionFactory;
        $this->_module = 'aitoccheckoutfieldsmanager';
        $this->_addButtonLabel = __('Add Attribute');
    }

    /**
     * Prepare product attributes grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        $this->setCollection($collection);
        $this->addColumn(
            'is_visible',
            [
                'header' => __('Visible on checkout page'),
                'sortable' => true,
                'index' => 'is_visible',
                'header_css_class' => 'col-system',
                'type' => 'options',
                'options' => [
                    '1' => __('Yes'), 
                    '0' => __('No'), 
                ],
                'column_css_class' => 'col-system'
            ]
        );
        $this->addColumn(
            'display_area',
            [
                'header' => __('Display Area'),
                'sortable' => true,
                'index' => 'display_area',
                'header_css_class' => 'col-system',
                'column_css_class' => 'col-system'
            ]
        );
        return parent::_prepareCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('coupon_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setHideFormElement(true);

        $this->getMassactionBlock()->addItem(
            'show',
            [
                'label'    => __('Show'),
                'url'      => $this->getUrl('aitoccheckoutfieldsmanager/*/massShow', ['_current' => true]),
                'confirm'  => __('Are you sure you want to show the selected attributes(s)?')
            ]
        );
        $this->getMassactionBlock()->addItem(
            'hide',
            [
                'label'    => __('Hide'),
                'url'      => $this->getUrl('aitoccheckoutfieldsmanager/*/massHide', ['_current' => true]),
                'confirm'  => __('Are you sure you want to hide the selected attributes(s)?')
            ]
        );

        return $this;
    }
}
