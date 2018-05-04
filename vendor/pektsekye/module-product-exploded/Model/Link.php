<?php

namespace Pektsekye\ProductExploded\Model;

class Link extends \Magento\Framework\Model\AbstractModel
{ 
    
    public function __construct(   
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry, 
        \Pektsekye\ProductExploded\Model\ResourceModel\Link $resource,                               
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,                 
        array $data = array()
    ) {            
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }



    public function getLinks($productId)
    {
      return $this->getResource()->getLinks($productId);           
    }


}
