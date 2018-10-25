<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Catalog\Controller\Adminhtml\Product\MassDelete
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * MassDelete constructor.
     *
     * @param Context                                $context
     * @param Builder                                $productBuilder
     * @param Filter                                 $filter
     * @param CollectionFactory                      $collectionFactory
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Aitoc\AdvancedPermissions\Helper\Data $helper
    ) {
        parent::__construct($context, $productBuilder, $filter, $collectionFactory);
        $this->filter            = $filter;
        $this->helper            = $helper;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $allow = 1;

        if ($this->helper->isAdvancedPermissionEnabled()) {
            if ($this->helper->getRole()->getAllowDelete()) {
                $allow = 1;
            } else {
                $allow = 0;
            }
        }
        if ($allow) {
            $collection     = $this->filter->getCollection($this->collectionFactory->create());
            $productDeleted = 0;
            foreach ($collection->getItems() as $product) {
                $product->delete();
                $productDeleted++;
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $productDeleted));
        } else {
            $this->messageManager->addError(__('Deleting products is not allowed'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/*/index');
    }
}
