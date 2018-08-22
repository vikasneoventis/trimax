<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Api\Data;

interface WarehouseInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const WAREHOUSE_ID              = 'warehouse_id';
    const NAME                      = 'name';
    const IS_DEFAULT                = 'is_default';
    const PRIORITY                  = 'priority';
    const IS_VISIBLE_IN_CHECKOUT    = 'is_visible_in_checkout';
    const IS_VISIBLE_IN_PRODUCT     = 'is_visible_in_product';
    const IS_VISIBLE_IN_ORDER       = 'is_visible_in_order';
    const IS_VISIBLE_IN_SHIPMENT    = 'is_visible_in_shipment';
    const IS_ORDER_NOTIFICATION     = 'is_order_notification';
    const IS_LOW_STOCK_NOTIFICATION = 'is_low_stock_notification';
    const COUNTRY_ID                = 'country_id';
    const REGION_ID                 = 'region_id';
    const CITY                      = 'city';
    const STREET                    = 'street';
    const POSTCODE                  = 'postcode';
    const TELEPHONE                 = 'telephone';
    const EMAIL                     = 'email';
    const LATITUDE                  = 'latitude';
    const LONGITUDE                 = 'longitude';
    const DESCRIPTION               = 'description';
    const CUSTOMER_GROUP_IDS        = 'customer_group_ids';
    const STORE_IDS                 = 'store_ids';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getWarehouseId();

    /**
     * @return bool
     */
    public function setWarehouseId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getIsDefault();

    /**
     * @param int $flag
     *
     * @return $this
     */
    public function setIsDefault($flag);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $order
     *
     * @return $this
     */
    public function setPriority($order);

    /**
     * @return bool
     */
    public function isVisibleInCheckout();

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsVisibleInCheckout($flag);

    /**
     * @return bool
     */
    public function isVisibleInProduct();

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsVisibleInProduct($flag);

    /**
     * @return bool
     */
    public function isVisibleInOrder();

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsVisibleInOrder($flag);

    /**
     * @return bool
     */
    public function isVisibleInShipment();

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsVisibleInShipment($flag);

    /**
     * @return bool
     */
    public function isOrderNotification();

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsOrderNotification($flag);

    /**
     * @return bool
     */
    public function isLowStockNotification();

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsLowStockNotification($flag);

    /**
     * @return string
     */
    public function getCountryId();

    /**
     * @param $countryCode
     *
     * @return $this
     */
    public function setCountryId($countryCode);

    /**
     * @return int|null
     */
    public function getRegionId();

    /**
     * @param int $regionId
     *
     * @return $this
     */
    public function setRegionId($regionId);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city);

    /**
     * @return string|null
     */
    public function getStreet();

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setStreet($address);

    /**
     * @return string|null
     */
    public function getPostcode();

    /**
     * @param string $zipCod
     *
     * @return $this
     */
    public function setPostcode($zipCod);

    /**
     * @return string|null
     */
    public function getTelephone();

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setTelephone($phone);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getLatitude();

    /**
     * @param string $latitude
     *
     * @return $this
     */
    public function setLatitude($latitude);

    /**
     * @return string
     */
    public function getLongitude();

    /**
     * @param string $longitude
     *
     * @return $this
     */
    public function setLongitude($longitude);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string|null
     */
    public function getCustomerGroupIds();

    /**
     * @return string|null
     */
    public function getStoreIds();
}
