<?php

namespace Aitoc\ProductUnitsAndQuantities\Observer;

use Aitoc\ProductUnitsAndQuantities\Helper\Data;
use Aitoc\ProductUnitsAndQuantities\Model\Config\Source\UseQuantitiesValidate;
use Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface as UnitsAndQuantitiesModel;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveProductObserver implements ObserverInterface
{
    private $helper;
    protected $generalModel;
    protected $useQuantitiesValidate;

    public function __construct(
        Data $helper,
        UnitsAndQuantitiesModel $generalModel,
        UseQuantitiesValidate $useQuantitiesValidate
    ) {
        $this->helper                = $helper;
        $this->generalModel          = $generalModel;
        $this->useQuantitiesValidate = $useQuantitiesValidate;
    }

    public function execute(Observer $observer)
    {
        if ($observer->getEvent()->getObject()->getEventPrefix() == 'catalog_product') {
            $data = $observer->getData('event')->getObject();
            $productType = $data->getTypeId();
            if ($productType == 'bundle' || $productType == 'grouped') {
                return;
            }
            $error = $this->productUnitsFiledsValidation($data);
            if ($error) {
                throw new \Magento\Framework\Exception\LocalizedException(__($error));
            }
        }
    }

    private function productUnitsFiledsValidation($data)
    {
        try {
            if (!$data->getData('use_config_use_quantities')) {
                $this->useQuantitiesValidate->validate($data->getData('use_quantities'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $e->getMessage();
        }

        $this->saveFields($data);
    }

    public function saveFields($data)
    {
        $fields       = $this->helper->getFields();
        $fieldsValues = [];
        $useConfigParams = [];

        foreach ($fields as $field) {
            $fieldsValues[$field] = $data->getData($field);
            if ($data->getData('use_config_' . $field)) {
                $useConfigParams[] = $field;
                $fieldsValues[$field] = $this->helper->getValueFromConfig($field);
            }
        }

        /** @var \Aitoc\ProductUnitsAndQuantities\Model\General $currentItem */
        $currentItem = $this->generalModel->load($data->getData('entity_id'), 'product_id');
        $currentItem->setProductId($data->getData('entity_id'));
        $currentItem->setReplaceQty($fieldsValues['replace_qty']);
        $currentItem->setQtyType($fieldsValues['qty_type']);
        $currentItem->setUseQuantities($fieldsValues['use_quantities']);
        $currentItem->setStartQty($fieldsValues['start_qty']);
        $currentItem->setQtyIncrement($fieldsValues['qty_increment']);
        $currentItem->setEndQty($fieldsValues['end_qty']);
        $currentItem->setAllowUnits($fieldsValues['allow_units']);
        $currentItem->setPricePer($fieldsValues['price_per']);
        $currentItem->setDivider($fieldsValues['price_per_divider']);
        $currentItem->setUseConfigParams(implode(',', $useConfigParams));

        $currentItem->save();

        return;
    }
}
