<?php

namespace Pektsekye\ProductExploded\Block\Product\View\Type;

class Grouped extends \Magento\GroupedProduct\Block\Product\View\Type\Grouped
{
    
    protected $_peLink; 
    protected $_peLabel;    
    protected $_eavEntity; 
    protected $_attrResource; 
    
    protected $_imageUrl = '';
    protected $_imageWidth = 0;
    protected $_imageHeight = 0;    
               
    public function __construct(
        \Pektsekye\ProductExploded\Model\ResourceModel\Link $peLink,
        \Pektsekye\ProductExploded\Model\Label $peLabel,
        \Magento\Eav\Model\Entity $eavEntity,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attrResource,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,                     
        array $data = array()
    ) {
        $this->_peLink       = $peLink;    
        $this->_peLabel      = $peLabel;    
        $this->_eavEntity    = $eavEntity;    
        $this->_attrResource = $attrResource;                                      
        parent::__construct($context, $arrayUtils, $data);
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
    

    public function getExtraAttributeCode()
    {
      $code = (string) $this->_scopeConfig->getValue('productexploded/settings/attributecode',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);               
      return trim($code);
    } 
    
 
    public function getExtraAttributeTitle()
    {
    
      if (!$this->getExtraAttributeTitleValue()){
        $title = '';
        $attributeCode = $this->getExtraAttributeCode();
        if (!empty($attributeCode)){  
          $entityTypeId = $this->_eavEntity->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
          $attribute = $this->_attrResource->loadByCode($entityTypeId, $attributeCode);        
          $title = $attribute->getFrontend()->getLabel();
        }
        $this->setExtraAttributeTitleValue($title);
      }
      return $this->getExtraAttributeTitleValue();
    } 
        
        
    public function getLabels()
    {
      if (!$this->getLabelsValue()){
        $labels = (array) $this->_peLabel->getLabels((int) $this->getProduct()->getId());
        
        $width  = $this->getImageWidth();
        $height = $this->getImageHeight();          
        foreach ($labels as $k => $label){
          $xp = 0;
          $yp = 0;
          $widthP = 0;
          $heightP = 0;   
                 
          $x = (int) $label['x'];
          if ($x > 0)
            $xp = 100/($width/$x);
            
          $y = (int) $label['y'];
          if ($y > 0)
            $yp = 100/($height/$y);
            
          $w = (int) $label['width'];
          if ($w > 0)
            $widthP = 100/($width/$w);
            
          $h = (int) $label['height'];
          if ($h > 0)
            $heightP = 100/($height/$h);            
                               
         $labels[$k]['xp'] = $xp;
         $labels[$k]['yp'] = $yp;
         $labels[$k]['width_p'] = $widthP;
         $labels[$k]['height_p'] = $heightP;                                       
        }
        
        $this->setLabelsValue($labels);
      }
      return $this->getLabelsValue();
    }
      
      
    public function getLinks()
    {
      return $this->_peLink->getLinks((int) $this->getProduct()->getId());             
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
    
    
    public function getNumberCode($number)
    {
      return strtolower(preg_replace('/[^\da-z]/i', '', (string) $number));      
    }    
    
             
}
