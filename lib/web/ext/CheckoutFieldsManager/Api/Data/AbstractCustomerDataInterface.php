<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Api\Data;

interface AbstractCustomerDataInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const KEY_VALUE_ID = 'value_id';

    const KEY_ATTRIBUTE_ID = 'attribute_id';

    const KEY_VALUE = 'value';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getAttributeId();

    /**
     * @return string|int|null
     */
    public function getValue();

    /**
     * @return string|null
     */
    public function getAttributeCode();

    /**
     * @param int $valueId
     *
     * @return $this
     */
    public function setId($valueId);

    /**
     * @param int $attrId
     *
     * @return $this
     */
    public function setAttributeId($attrId);

    /**
     * @param string|int|null $value
     *
     * @return $this
     */
    public function setValue($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Framework\Api\ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\Framework\Api\ExtensionAttributesInterface $extensionAttributes
    );
}
