<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute
{
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @var \Aitoc\CheckoutFieldsManager\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                                          $context
     * @param \Magento\Framework\Registry                                                  $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory                                   $resultPageFactory
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory    $groupCollectionFactory
     * @param \Magento\Framework\Filter\FilterManager                                      $filterManager
     * @param \Magento\Catalog\Model\Product\Url                                           $ulrGenerator
     * @param \Aitoc\CheckoutFieldsManager\Model\Entity\AttributeFactory                          $checkoutEavFactory
     * @param \Aitoc\CheckoutFieldsManager\Helper\Data                                     $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Catalog\Model\Product\Url $ulrGenerator,
        \Aitoc\CheckoutFieldsManager\Model\Entity\AttributeFactory $checkoutEavFactory,
        \Aitoc\CheckoutFieldsManager\Helper\Data $helper
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $ulrGenerator, $checkoutEavFactory);
        $this->filterManager          = $filterManager;
        $this->helper                 = $helper;
        $this->validatorFactory       = $validatorFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $redirectBack = $this->getRequest()->getParam('back', false);
            /** @var $model \Aitoc\CheckoutFieldsManager\Model\Entity\Attribute */
            $model = $this->checkoutEavFactory->create();

            $attributeId   = $this->getRequest()->getParam('attribute_id');
            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $frontendLabel = $this->getRequest()->getParam('frontend_label');
            $attributeCode = $attributeCode ?: $this->generateCode($frontendLabel[0]);
            if (strlen($this->getRequest()->getParam('attribute_code')) > 0) {
                $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addError(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );

                    return $this->_redirect(
                        'aitoccheckoutfieldsmanager/*/edit',
                        ['attribute_id' => $attributeId, '_current' => true]
                    );
                }
            }
            $data['attribute_code'] = $attributeCode;

            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var $inputType \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator */
                $inputType = $this->validatorFactory->create();
                $inputType->addInputType('radiobutton')
                    ->addInputType('checkbox')
                    ->addInputType('label');
                if (!$inputType->isValid($data['frontend_input'])) {
                    foreach ($inputType->getMessages() as $message) {
                        $this->messageManager->addError($message);
                    }

                    return $this->_redirect(
                        'aitoccheckoutfieldsmanager/*/edit',
                        ['attribute_id' => $attributeId, '_current' => true]
                    );
                }
            }

            if ($attributeId) {
                $model->load($attributeId);
                if (!$model->getId()) {
                    $this->messageManager->addError(__('This attribute no longer exists.'));

                    return $this->_redirect('aitoccheckoutfieldsmanager/*/');
                }
                // entity type check
                if ($model->getEntityTypeId() != $this->entityTypeId) {
                    $this->messageManager->addError(__('We can\'t update the attribute.'));
                    $this->_session->setCheckoutAttributeData($data);

                    return $this->_redirect('aitoccheckoutfieldsmanager/*/');
                }

                $data['attribute_code']  = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input']  = $model->getFrontendInput();
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }
            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);

            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }
            $model->addData($data);

            if (!$attributeId) {
                $model->setEntityTypeId($this->entityTypeId);
                $model->setIsUserDefined(1);
            }

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the checkout attribute.'));

                $this->_session->setCheckoutAttributeData(false);
                if ($redirectBack) {
                    return $this->_redirect(
                        'aitoccheckoutfieldsmanager/*/edit',
                        ['attribute_id' => $model->getId(), '_current' => true]
                    );
                }

                return $this->_redirect('aitoccheckoutfieldsmanager/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setCheckoutAttributeData($data);

                return $this->_redirect(
                    'aitoccheckoutfieldsmanager/*/edit',
                    ['attribute_id' => $attributeId, '_current' => true]
                );
            }
        }

        return $this->_redirect('aitoccheckoutfieldsmanager/*/');
    }
}
