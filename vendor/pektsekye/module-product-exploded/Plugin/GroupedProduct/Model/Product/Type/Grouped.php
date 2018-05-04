<?php

namespace Pektsekye\ProductExploded\Plugin\GroupedProduct\Model\Product\Type;


class Grouped
{


    public function afterGetAssociatedProductCollection(\Magento\GroupedProduct\Model\Product\Type\Grouped $subject, $collection)
    {
        $attributeCodes = array('visibility');
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $extraAttributeCode = $scopeConfig->getValue('productexploded/settings/attributecode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($extraAttributeCode)){
          $attributeCodes[] = trim($extraAttributeCode);
        }  
    
        $collection->addAttributeToSelect($attributeCodes);
       
        return $collection;   
    }


}
