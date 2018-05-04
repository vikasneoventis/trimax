<?php

namespace Pektsekye\ProductExploded\Controller\Adminhtml\Pe\Product;

abstract class Labels extends \Magento\Backend\App\AbstractAction
{

  protected $_productFactory;
  protected $_coreRegistry;    
  
  public function __construct(
      \Magento\Catalog\Model\ProductFactory $productFactory, 
      \Magento\Framework\Registry $coreRegistry,             
      \Magento\Backend\App\Action\Context $context
  ) {
      $this->_productFactory  = $productFactory;  
      $this->_coreRegistry    = $coreRegistry;       
      parent::__construct($context);
  }


  protected function _initProduct()
  {
    $productId = (int) $this->getRequest()->getParam('id');    
    $product = $this->_productFactory->create();
    
    if ($productId)
      $product->load($productId);
      
    $this->_coreRegistry->register('current_product', $product);
  }         

    
  protected function _isAllowed()
  {
    return true;
  }    
  	
}
