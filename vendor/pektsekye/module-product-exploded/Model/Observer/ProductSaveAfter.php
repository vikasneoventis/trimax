<?php

namespace Pektsekye\ProductExploded\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductSaveAfter implements ObserverInterface
{

  protected $_peLink;   
  protected $_peLabel;    
  protected $_request;
  
  public function __construct(    
        \Pektsekye\ProductExploded\Model\Link $peLink,
        \Pektsekye\ProductExploded\Model\Label $peLabel,        
        \Magento\Framework\App\RequestInterface $request             
    ) {
        $this->_peLink = $peLink;
        $this->_peLabel = $peLabel;        
        $this->_request = $request;                           
  } 
 


  public function execute(\Magento\Framework\Event\Observer $observer)
  {

    $product = $observer->getEvent()->getProduct();
    
    $linkData = $this->_request->getParam('links');   
    if (!$linkData || !isset($linkData['associated'])){ 
      return $this;
    }  
 
    $links = array();    
    foreach((array) $linkData['associated'] as $link){
      $linkedProductId = $link['id'];
      if (isset($link['number_on_image'])){
        $links[$linkedProductId] = $link['number_on_image'];
      }  
    } 
              
    $this->_peLink->getResource()->saveLinks((int) $product->getId(), $links);
    
    
    $labels = $product->getPeLabel();
    if (!empty($labels)){
      $this->_peLabel->getResource()->saveLabels((int) $product->getId(), $labels);
    }
    
    
    return $this;
  }
  
  
}
