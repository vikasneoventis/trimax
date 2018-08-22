<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Api\Data;

interface SupplierInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID         = 'entity_id';
    const TITLE             = 'title';
    const CONTACT_NAME      = 'contact_name';
    const PHONE             = 'phone';
    const EMAIL             = 'email';
    const ADDRESS           = 'address';
    const CAN_RECEIVE_EMAIL = 'can_receive_email';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getEntityId();

    /**
     * @param int $id
     *
     * @return bool
     */
    public function setEntityId($id);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getContactName();

    /**
     * @param string $contactName
     *
     * @return $this
     */
    public function setContactName($contactName);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone);

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
    public function getAddress();

    /**
     * @param int $address
     *
     * @return $this
     */
    public function setCanReceiveEmail($flag);

    /**
     * @return int
     */
    public function getCanReceiveEmail();

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddress($address);
}
