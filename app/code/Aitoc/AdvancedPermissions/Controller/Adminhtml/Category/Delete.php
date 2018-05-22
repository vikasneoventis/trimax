<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Controller\Adminhtml\Category;

class Delete extends \Magento\Catalog\Controller\Adminhtml\Category\Delete
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * Delete constructor.
     *
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Aitoc\AdvancedPermissions\Helper\Data           $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Aitoc\AdvancedPermissions\Helper\Data $helper
    ) {
        parent::__construct($context, $categoryRepository);
        $this->helper = $helper;
    }

    /**
     * Delete category action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getAllowDelete()) {
            $this->messageManager->addError(__('Deleting categories is not allowed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            
            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }

        return parent::execute();
    }
}
