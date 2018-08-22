<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Webapi;

class ServiceInputProcessor extends \Magento\Framework\Webapi\ServiceInputProcessor
{
    /**
     * @param array  $customAttributesValueArray
     * @param string $dataObjectClassName
     *
     * @return \Magento\Framework\Api\AttributeValue[]
     */
    protected function convertCustomAttributeValue($customAttributesValueArray, $dataObjectClassName)
    {
        foreach ($customAttributesValueArray as $key => $customAttribute) {
            if (is_array($customAttribute) && !array_key_exists('value', $customAttribute)) {
                $customAttributesValueArray[$key] =
                    [
                        \Magento\Framework\Api\AttributeValue::ATTRIBUTE_CODE => $key,
                        \Magento\Framework\Api\AttributeValue::VALUE => $customAttribute
                    ];
            }
        }

        return parent::convertCustomAttributeValue($customAttributesValueArray, $dataObjectClassName);
    }
}
