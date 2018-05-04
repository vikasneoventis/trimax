<?php

namespace Pektsekye\ProductExploded\Controller\Adminhtml\Pe\Product\Labels;

class Index extends \Pektsekye\ProductExploded\Controller\Adminhtml\Pe\Product\Labels
{


  public function execute()
  {
    $this->_initProduct();
    $this->getResponse()->setBody(
        $this->_view->getLayout()->createBlock('Pektsekye\ProductExploded\Block\Adminhtml\Pe\Product\Labels')->setTemplate('product/labels.phtml')->toHtml()
    );    
  }

}
