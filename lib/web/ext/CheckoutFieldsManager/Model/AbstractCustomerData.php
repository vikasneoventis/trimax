<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\CheckoutFieldsManager\Model;

use Aitoc\CheckoutFieldsManager\Api\Data\AbstractCustomerDataInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

abstract class AbstractCustomerData extends AbstractExtensibleModel implements AbstractCustomerDataInterface
{
    /**
     * @var Entity\Attribute
     */
    protected $attr;

    /**
     * AbstractModel constructor.
     *
     * @param Entity\Attribute                                             $attr
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory            $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory                 $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        Entity\Attribute $attr,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->attr = $attr;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::KEY_VALUE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId()
    {
        return $this->getData(self::KEY_ATTRIBUTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(self::KEY_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        if (!$this->attr->getId()) {
            $this->attr->load($this->getAttributeId());
        }
        return $this->attr->getAttributeCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($valueId)
    {
        $this->setData(self::KEY_VALUE_ID, $valueId);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeId($attrId)
    {
        $this->setData(self::KEY_ATTRIBUTE_ID, $attrId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->setData(self::KEY_VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Magento\Framework\Api\ExtensionAttributesInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
