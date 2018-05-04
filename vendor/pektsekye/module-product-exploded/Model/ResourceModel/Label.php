<?php

namespace Pektsekye\ProductExploded\Model\ResourceModel;

class Label extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    public function _construct()
    {
        $this->_init('productexploded_label', 'label_id');
    }



    public function saveLabels($productId, $labels)
    {

      if (isset($labels['update'])){
        foreach ($labels['update'] as $labelId => $r){    
          $labelId = (int) $labelId;
          $this->getConnection()->update($this->getMainTable(), array('width' => (int) $r['width'], 'height' => (int) $r['height'], 'title' => $r['title'], 'link_to_number' => $r['link_to_number'], 'x' => (int) $r['x'], 'y' => (int) $r['y']), "label_id={$labelId}");
        }                  
      }
      
      if (isset($labels['new'])){
        $data = array();
        foreach ($labels['new'] as $r){
          $data[] = array('product_id' => (int) $productId, 'width' => (int) $r['width'], 'height' => (int) $r['height'], 'title' => $r['title'], 'link_to_number' => $r['link_to_number'], 'x' => (int) $r['x'], 'y' => (int) $r['y']);             
        } 
        $this->getConnection()->insertMultiple($this->getMainTable(), $data);                           
      }
      
      if (isset($labels['delete'])){
        $this->getConnection()->delete($this->getMainTable(), array('label_id IN (?)' => $labels['delete']));
      }               
           
    }


    public function getLabels($productId)
    {     
      $select = $this->getConnection()->select()        
        ->from($this->getMainTable(), array('label_id','width','height','title','link_to_number','x','y'))        
        ->where('product_id = ?', $productId);
      
      return $this->getConnection()->fetchAssoc($select);            
    } 

  
}
