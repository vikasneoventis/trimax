<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\CheckoutFieldsManager\Block\Element;

use Magento\Config\Model\Config\Source\Yesno;

/**
 * Render Checkout fields value for
 */
class ValueRenderer
{
    /**
     * @var Yesno
     */
    private $yesNo;

    /**
     * @param Yesno $yesno
     */
    public function __construct(Yesno $yesno)
    {
        $this->yesNo = $yesno;
    }

    /**
     * Prepare attribute label and value without inputs
     *
     * @param array     $field
     * @param bool|true $optionsInLine
     *
     * @return string
     */
    public function renderFieldValueHtml($field = [], $optionsInLine = true)
    {
        return '<th>' . $this->getFormattedLabel($field) . '</th>' .
        '<td>' . $this->getFormattedValue($field, $optionsInLine) . '</td>';
    }

    /**
     * @param array $field
     *
     * @return string
     */
    public function getFormattedLabel($field)
    {
        return __($field['field_name']);
    }

    /**
     * @param array          $field
     * @param bool|true $optionsInLine
     *
     * @return string without HTML (can be used for PDF)
     */
    public function getFormattedValue($field, $optionsInLine = true)
    {
        if ($field['value'] == null) {
            return '';
        }
        $value = $field['value'];
        switch ($field['type']) {
            case 'textarea':
                break;
            case 'date':
                break;
            case 'boolean':
                $options = $this->yesNo->toArray();
                $value = $options[$value];
                break;
            case 'checkbox':
            case 'select':
            case 'radiobutton':
            case 'multiselect':
                $options = $this->replaceOptionsLabels($field, explode("\n", $value));
                $value = $this->formatOptions($options, $optionsInLine);
                break;
        };

        return $value;
    }

    /**
     * replace values with option_id to option label
     *
     * @param array $field
     * @param array $selected
     *
     * @return array
     */
    private function replaceOptionsLabels($field, $selected)
    {
        if (!array_key_exists('options', $field) || !is_array($field['options']) || !is_array($selected)) {
            return [];
        }
        $result = [];
        foreach ($field['options'] as $option) {
            if (in_array($option['value'], $selected)) {
                $result[] = $option['label'];
            }
        }

        return $result;
    }

    /**
     * Implode array of options to string
     *
     * @param $options
     * @param $optionsInLine
     *
     * @return string
     */
    private function formatOptions($options, $optionsInLine)
    {
        if (!count($options)) {
            return '';
        }
        if ($optionsInLine) {
            return implode(', ', $options);
        }

        return '<div>' . implode('</div><div>', $options) . '</div>';
    }
}
