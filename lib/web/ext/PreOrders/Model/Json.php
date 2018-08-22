<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model;

class Json extends \Magento\Framework\Controller\Result\Json
{
    /**
     * Decode json data
     *
     * @return mixed
     * @throws \Zend_Json_Exception
     */
    public function getJson()
    {
        return \Zend_Json::decode($this->json);
    }
}
