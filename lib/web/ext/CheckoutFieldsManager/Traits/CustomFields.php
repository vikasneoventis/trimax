<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Traits;

trait CustomFields
{
    /**
     * @var array|null
     */
    protected $checkoutFieldsData = null;

    /**
     * @var int
     */
    protected $orderId;

    /**
     * Get customer checkout fields for order
     *
     * @param int $orderId
     *
     * @return array
     */
    protected function prepareCheckoutFieldsData($orderId)
    {
        if (is_null($this->checkoutFieldsData)) {
            /** @var \Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection $collection */
            $collection = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection');
            $this->checkoutFieldsData = $collection->getAitocCheckoutfieldsByOrderId($orderId);
            if (!is_array($this->checkoutFieldsData)) {
                $this->checkoutFieldsData = [];
            }
        };

        return $this;
    }

    /**
     * Prepare attribute label and value without inputs, with HTML
     *
     * @param array $data
     *
     * @return array
     */
    protected static function getHtmlValuesSet($data = [])
    {
        $html = [];
        $valueRenderer = self::getRender();
        foreach ($data as $field) {
            $html[] = $valueRenderer->renderFieldValueHtml($field, false);
        }

        return $html;
    }

    /**
     * Set label for fields
     *
     * @param array $data
     *
     * @return array
     */
    protected static function setShowValue($data = [])
    {
        if (count($data)) {
            $valueRenderer = self::getRender();
            foreach ($data as $key => $field) {
                $data[$key]['value'] = $valueRenderer->getFormattedValue($field, true);
            }
        }

        return $data;
    }

    /**
     * Get customer checkout fields for order
     * Row contains HTML with label and value of checkout attribute
     * @return array
     */
    public function getCheckoutFieldsData()
    {
        $this->prepareCheckoutFieldsData($this->orderId);
        if (count($this->checkoutFieldsData)) {
            return self::getHtmlValuesSet($this->checkoutFieldsData);
        }

        return [];
    }

    /**
     * @return \Aitoc\CheckoutFieldsManager\Block\Element\ValueRenderer
     */
    private static function getRender()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Aitoc\CheckoutFieldsManager\Block\Element\ValueRenderer');
    }
}
