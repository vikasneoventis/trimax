<?php

namespace Pektsekye\ProductExploded\Model;

class Label extends \Magento\Framework\Model\AbstractModel
{ 
    
    public function __construct(   
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry, 
        \Pektsekye\ProductExploded\Model\ResourceModel\Label $resource,                               
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,                 
        array $data = array()
    ) {            
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }



    public function getLabels($productId)
    {
      return $this->getResource()->getLabels($productId);           
    }


}
