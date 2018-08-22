<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Controller\Adminhtml\Warehouse;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem\CollectionFactory as StockItemCollectionFactory;
use Aitoc\MultiLocationInventory\Model\Warehouse as WarehouseModel;
use Aitoc\MultiLocationInventory\Model\Supplier as SupplierModel;

/**
 * Class MassOrder
 *
 * @package Aitoc\MultiLcoationInventory\Controller\Adminhtml\Parlevel
 */
class Regionlist extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_countryFactory = $countryFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {

        $countryCode = $this->getRequest()->getParam('country');
        $regionId = (int)$this->getRequest()->getParam('region');
        $state = '';
        if ($countryCode != '') {
            $stateArray = $this->_countryFactory->create()->setId(
                $countryCode
            )->getLoadedRegionCollection()->toOptionArray();
            foreach ($stateArray as $_state) {
                if ($_state['value']) {
                    $selected = '';
                    if ($_state['value'] == $regionId) {
                        $selected = ' selected="selected"';
                    }
                    $state .= "<option value='" . $_state['value'] . "'" . $selected . ">" . $_state['label'] . "</option>";
                }
            }
        }
        $result['htmlconent'] = $state;
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}
