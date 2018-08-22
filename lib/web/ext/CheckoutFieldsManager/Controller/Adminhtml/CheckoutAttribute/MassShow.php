<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory;

class MassShow extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Context $context,
        Filter $filter
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if($this->getRequest()->getParam('ids')) {
            $ids = $this->getRequest()->getParam('ids');
            $attributeCollectionCount = $this->collectionFactory->create()->addFieldToFilter('additional_table.attribute_id',['in' => $ids])->count();
            $attributeCollection = $this->collectionFactory->create()->addFieldToFilter('additional_table.attribute_id',['in' => $ids])->getItems();
            foreach ($attributeCollection as $attribute)
            {
                
                $attribute->setIsVisible(1);
                $attribute->save();
            }

            $this->messageManager->addSuccess(__('A total of %1 element(s) have been show.', $attributeCollectionCount));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('aitoccheckoutfieldsmanager/*/index');
            return $resultRedirect;
        }
    }
}
