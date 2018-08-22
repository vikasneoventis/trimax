<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml;

class GeneralForm extends \Magento\Backend\Block\Widget\Form\Generic
{

    const TYPE_LABEL = 'label';

    protected $legend;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $yesNo;

    protected $attributeValuesForHtml = [];

    /**
     * GeneralForm constructor.
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
        parent::__construct($context, $registry, $formFactory, $data);
        $this->yesNo = $yesNo;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $fieldset           = $form->addFieldset(
            'checkoutfields_fieldset',
            ['legend' => __($this->getLegend())]
        );
        $checkoutFieldsData = $this->_coreRegistry->registry('checkout_fields_data');
        if (is_array($checkoutFieldsData)) {
            foreach ($checkoutFieldsData as $field) {
                if ($field['type'] != self::TYPE_LABEL) {
                    $this->renderField($field, $fieldset);
                }
            }
            $form->setValues($this->getAttributeValuesForHtml());
            $form->setUseContainer(true);
            $this->setForm($form);
        }

        return parent::_prepareForm();
    }

    /**
     * Add fields in form
     *
     * @param $field
     * @param $fieldset
     */
    public function renderField($field, $fieldset)
    {
        $attributeIdForHtml = 'cf_' . $field['increment_id'];
        $params             = [
            'name'  => 'text_' . $field['increment_id'],
            'label' => __($field['field_name']),
            'title' => __($field['field_name']),
            'value' => $field['value']
        ];

        switch ($field['type']) {
            case 'textarea':
                $params['content'] = $field['value'];
                break;
            case 'date':
                $params['date_format'] = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
                break;
            case 'boolean':
                $field['type'] = 'select';
                $params['values'] = $this->yesNo->toOptionArray();
                break;
            case 'select':
                $params['values'] = (isset($field['options']) && is_array($field['options'])) ? $field['options'] : [];
                break;
            case 'multiselect':
                $params['values'] = (isset($field['options']) && is_array($field['options'])) ? $field['options'] : [];
                $field['field_value'] = explode("\n", $field['field_value']);
                break;
            case 'checkbox':
                $moduleName = $this->getRequest()->getModuleName();
                if ($moduleName == 'aitoccheckoutfieldsmanager') $params['name'] = $params['name'] . '[]';
                $field['type'] = 'checkboxes';
                $params['values'] = (isset($field['options']) && is_array($field['options'])) ? $field['options'] : [];
                $field['field_value'] = explode("\n", $field['field_value']);
                break;
            case 'radiobutton':
                $field['type'] = 'radios';
                $params['values'] = (isset($field['options']) && is_array($field['options'])) ? $field['options'] : [];
                break;
            default:
                break;
        };

        $fieldset->addField(
            $attributeIdForHtml,
            $field['type'],
            $params
        );

        $this->setAttributeValuesForHtml($attributeIdForHtml, $field['field_value']);
    }

    /**
     * @return array
     */
    public function getAttributeValuesForHtml()
    {
        return $this->attributeValuesForHtml;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setAttributeValuesForHtml($key, $value)
    {
        $this->attributeValuesForHtml[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * @param $legend
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;
    }
}
