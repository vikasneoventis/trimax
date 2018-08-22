<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\CustomerDataCheckoutAttribute\CheckoutFields\Create;

use \Aitoc\CheckoutFieldsManager\Block\Adminhtml\GeneralForm;

class Form extends GeneralForm
{
    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Framework\Data\FormFactory       $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        array $data
    ) {
        parent::__construct($context, $registry, $formFactory, $yesNo, $data);
        $this->setLegend('Add Checkout Fields');
    }
}
