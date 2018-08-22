<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model\Config\Source;

use Magento\Framework\App\Config\Value;

class UseQuantitiesValidate extends Value
{
    /**
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        try {
            $this->useQuantitiesValidate($value);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $msg = __('Invalid Use Quantities. %1', $e->getMessage());
            $error = new \Magento\Framework\Exception\LocalizedException($msg, $e);
            throw $error;
        }
    }

    /**
     * @param $value
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate($value)
    {
        try {
            $this->useQuantitiesValidate($value);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw $e;
        }
    }

    /**
     * @param $value
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function useQuantitiesValidate($value)
    {
        if (!$value) {
            $value = $this->getConfig()->getValue('product_units_and_quantities/general_settings/use_quantities', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }

        $values = explode(',', $value);
        foreach ($values as $value) {
            if (floatval($value) != trim($value)) {
                throw new \Magento\Framework\Exception\LocalizedException(__("Invalid value of 'Use quantities' field. Please see example"));
            }
        }
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private function getConfig()
    {
        return $this->_config;
    }
}
