<?php

namespace Pektsekye\ProductExploded\Model\ResourceModel;

class Link extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    public function _construct()
    {
        $this->_init('productexploded_link', 'link_id');
    }



    public function saveLinks($productId, $links)
    {

      if (!is_array($links) || count($links) == 0)
        return;        
        
      $linkedProductIds = array_keys($links);
        
      $select = $this->getConnection()->select()        
        ->from($this->getTable('catalog_product_link'), array('linked_product_id','link_id'))         
        ->where("product_id = {$productId} AND linked_product_id IN (?)", $linkedProductIds);   
      $linkIds = $this->getConnection()->fetchPairs($select);          
      
      foreach ($links as $productId => $label){
        if (!isset($linkIds[$productId]))
          continue;
        $linkId = $linkIds[$productId];
        $this->getConnection()->insert($this->getMainTable(), array("link_id" => $linkId, 'number_on_image' => $label));    
      }                  
           
    }


    public function getLinks($productId)
    {     
      $select = $this->getConnection()->select()        
        ->from(array('cpl' => $this->getTable('catalog_product_link')), array('linked_product_id'))
        ->join(array('pel' => $this->getMainTable()), 'pel.link_id = cpl.link_id', array('number_on_image'))         
        ->where('cpl.product_id = ?', $productId);
      
      return $this->getConnection()->fetchPairs($select);            
    } 

  
}
