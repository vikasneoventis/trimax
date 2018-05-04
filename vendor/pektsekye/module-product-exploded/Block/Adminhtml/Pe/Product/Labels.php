<?php

namespace Pektsekye\ProductExploded\Block\Adminhtml\Pe\Product;

class Labels extends \Magento\Backend\Block\Widget
{   
    
    const LINK_TYPE = 'associated';    
    
    protected $_peLabel;     
    protected $_imageHelper;            
    protected $_coreRegistry;  

    protected $_imageUrl = '';
    protected $_imageWidth = 0;
    protected $_imageHeight = 0;    
    

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Pektsekye\ProductExploded\Model\Label $peLabel,            
        \Magento\Catalog\Helper\Image $imageHelper,                                       
        \Magento\Framework\Registry $coreRegistry,                             
        array $data = array()
    ) {
        $this->_peLabel = $peLabel;                
        $this->_imageHelper  = $imageHelper;                        
        $this->_coreRegistry = $coreRegistry;                    
        parent::__construct($context, $data);
    }


    public function _construct() {
    
      $image = $this->getProduct()->getImage() != 'no_selection' ? (string) $this->getProduct()->getImage() : '';
      
      if (!empty($image)){      
        $helper = $this->_imageHelper->init($this->getProduct(), 'product_page_image_large')->setImageFile($image);
        
        $this->_imageUrl = $helper->getUrl();// important order getURL before getOriginalWidth
        $this->_imageWidth = $helper->getOriginalWidth();
        $this->_imageHeight = $helper->getOriginalHeight();                    
      }
      
    }


    public function getProduct()
    {
      return $this->_coreRegistry->registry('current_product');
    }


    public function getLabels()
    {
      if (!$this->getLabelsValue()){
        $labels = (array) $this->_peLabel->getLabels((int) $this->getProduct()->getId());
        $this->setLabelsValue($labels);
      }
      return $this->getLabelsValue();
    } 
    
    
    public function getNubersOnImage()
    {
      if (!$this->getNubersOnImageValue()){
        $numbers = array();     
        $peLinks = $this->_peLink->getResource()->getLinks((int)$product->getId());           
        foreach ((array)$this->productLinkRepository->getList($this->getProduct()) as $linkItem) {
          if ($linkItem->getLinkType() !== self::LINK_TYPE) {
              continue;
          }
          $linkedProductId = $linkItem->getId();
          $numbers[] = isset($peLinks[$linkedProductId]) ? $peLinks[$linkedProductId] : $linkItem->getPosition();
        }
        $this->setNubersOnImageValue($numbers);
      }
      return $this->getNubersOnImageValue();
    }      
    
    
    public function getLastLabelId()
    {
      return count($this->getLabels()) > 0 ? max(array_keys($this->getLabels())) : 0;
    }    
    
    
    public function getImageUrl()
    {
      return $this->_imageUrl;      
    } 
 
 
    public function getImageWidth()
    {
      return (int) $this->_imageWidth;      
    } 
 
 
    public function getImageHeight()
    {
      return (int) $this->_imageHeight;      
    }
     
}
