<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

/**
 * Aitoc Checkout Attribute controller
 */
namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml;

use Magento\Framework\Controller\Result;
use Magento\Framework\View\Result\PageFactory;

abstract class CheckoutAttribute extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    protected $entityTypeId;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Url
     */
    protected $ulrGenerator;

    /**
     * @var \Aitoc\CheckoutFieldsManager\Model\Entity\AttributeFactory
     */
    protected $checkoutEavFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                 $context
     * @param \Magento\Framework\Registry                         $coreRegistry
     * @param PageFactory                                         $resultPageFactory
     * @param \Magento\Catalog\Model\Product\Url                  $ulrGenerator
     * @param \Aitoc\CheckoutFieldsManager\Model\Entity\AttributeFactory $checkoutEavFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product\Url $ulrGenerator,
        \Aitoc\CheckoutFieldsManager\Model\Entity\AttributeFactory $checkoutEavFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry       = $coreRegistry;
        $this->resultPageFactory  = $resultPageFactory;
        $this->ulrGenerator       = $ulrGenerator;
        $this->checkoutEavFactory = $checkoutEavFactory;
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->entityTypeId = $this->_objectManager
            ->create('Magento\Eav\Model\Entity')
            ->setType('aitoc_checkout')
            ->getTypeId();

        return parent::dispatch($request);
    }

    /**
     * Generate code from label
     *
     * @param string $label
     *
     * @return string
     */
    protected function generateCode($label)
    {
        $code              = substr(
            preg_replace('/[^a-z_0-9]/', '_', $this->ulrGenerator->formatUrlKey($label)),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }

        return $code;
    }

    /**
     * @param \Magento\Framework\Phrase|null $title
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Checkout Attributes'));

        return $resultPage;
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aitoc_CheckoutFieldsManager::attributes');
    }
}
