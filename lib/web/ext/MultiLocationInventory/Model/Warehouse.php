<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model;

use Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface;

class Warehouse extends AbstractModel implements WarehouseInterface
{
    protected function _construct()
    {
        $this->_init('Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse');
    }

    /**
     * {@inheritdoc}
     */
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWarehouseId($id)
    {
        return $this->setData(self::WAREHOUSE_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function isDefault()
    {
        return $this->getIsDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDefault()
    {
        return (int)$this->getData(self::IS_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDefault($flag)
    {
        return $this->setData(self::IS_DEFAULT, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($order)
    {
        return $this->setData(self::PRIORITY, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisibleInCheckout()
    {
        return (bool)$this->getData(self::IS_VISIBLE_IN_CHECKOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleInCheckout($flag)
    {
        return $this->setData(self::IS_VISIBLE_IN_CHECKOUT, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisibleInProduct()
    {
        return (bool)$this->getData(self::IS_VISIBLE_IN_PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleInProduct($flag)
    {
        return $this->setData(self::IS_VISIBLE_IN_PRODUCT, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisibleInOrder()
    {
        return (bool)$this->getData(self::IS_VISIBLE_IN_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleInOrder($flag)
    {
        return $this->setData(self::IS_VISIBLE_IN_ORDER, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisibleInShipment()
    {
        return (bool)$this->getData(self::IS_VISIBLE_IN_SHIPMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleInShipment($flag)
    {
        return $this->setData(self::IS_VISIBLE_IN_SHIPMENT, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function isOrderNotification()
    {
        return (bool)$this->getData(self::IS_ORDER_NOTIFICATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsOrderNotification($flag)
    {
        return $this->setData(self::IS_ORDER_NOTIFICATION, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function isLowStockNotification()
    {
        return (bool)$this->getData(self::IS_LOW_STOCK_NOTIFICATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsLowStockNotification($flag)
    {
        return $this->setData(self::IS_LOW_STOCK_NOTIFICATION, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryId($countryCode)
    {
        return $this->setData(self::COUNTRY_ID, $countryCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * {@inheritdoc}
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * {@inheritdoc}
     */
    public function setStreet($address)
    {
        return $this->setData(self::STREET, $address);
    }

    /**
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostcode($zipCod)
    {
        return $this->setData(self::POSTCODE, $zipCod);
    }

    /**
     * {@inheritdoc}
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTelephone($phone)
    {
        return $this->setData(self::TELEPHONE, $phone);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->getData(self::LATITUDE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLatitude($latitude)
    {
        return $this->setData(self::LATITUDE, $latitude);
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get warehouse customer group Ids
     *
     * @return array|null
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * Get warehouse store Ids
     *
     * @return array|null
     */
    public function getStoreIds()
    {
        if (!$this->hasStoreIds()) {
            $storeIds = $this->_getResource()->getStoreIds($this->getId());
            $this->setData('store_ids', (array)$storeIds);
        }
        return $this->_getData('store_ids');
    }

    /**
     * {@inheritdoc}
     */
    public function validateData(\Magento\Framework\DataObject $data)
    {
        /*
         * TODO: warehouse data validation
         */
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Aitoc\MultiLocationInventory\Api\Data\WarehouseExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Aitoc\MultiLocationInventory\Api\Data\WarehouseExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aitoc\MultiLocationInventory\Api\Data\WarehouseExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
