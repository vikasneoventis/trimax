<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model;

use Aitoc\MultiLocationInventory\Api\Data\SupplierInterface;

class Supplier extends AbstractModel implements SupplierInterface
{
    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\MultiLocationInventory\Model\ResourceModel\Supplier');
    }

    /**
     * Return entity id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getEntityId();
    }

    /**
     * Set id of entity
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->setEntityId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getContactName()
    {
        return $this->getData(self::CONTACT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setContactName($contactName)
    {
        return $this->setData(self::CONTACT_NAME, $contactName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone()
    {
        return $this->getData(self::PHONE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPhone($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getCanReceiveEmail()
    {
        return (int)$this->getData(self::CAN_RECEIVE_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCanReceiveEmail($flag)
    {
        return $this->setData(self::CAN_RECEIVE_EMAIL, $flag);
    }
}
