<?php

namespace Pektsekye\ProductExploded\Plugin\GroupedProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form;

class Grouped
{

    protected $_peLink;    
    protected $locator;
        
    public function __construct(
        \Pektsekye\ProductExploded\Model\Link $peLink,
        \Magento\Catalog\Model\Locator\LocatorInterface $locator                   
    ) {
        $this->_peLink = $peLink;  
        $this->locator = $locator;                 
    }


    public function afterModifyData(\Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\Grouped $subject, $data)
    {
    
      $product = $this->locator->getProduct(); 
      $peLinks = $this->_peLink->getResource()->getLinks((int)$product->getId());  
      $modelId = $product->getId();    
 
      if (!isset($data[$modelId]['links'][$subject::LINK_TYPE]))
        return $data;
        
      $links = $data[$modelId]['links'][$subject::LINK_TYPE];
      foreach($links as $k => $link){
        $linkedProductId = $link['id'];
        $data[$modelId]['links'][$subject::LINK_TYPE][$k]['number_on_image'] = isset($peLinks[$linkedProductId]) ? $peLinks[$linkedProductId] : '';
      } 
      
      return $data; 
    }


    public function afterModifyMeta(\Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\Grouped $subject, $meta)
    {

        if (!isset($meta[$subject::GROUP_GROUPED]['children'][$subject::LINK_TYPE]['children']['record']['children']['position'])){
          return $meta;
        }

        $meta[$subject::GROUP_GROUPED]['children'][$subject::LINK_TYPE]['arguments']['data']['config']['dndConfig']['enabled'] = false;

        $position = $meta[$subject::GROUP_GROUPED]['children'][$subject::LINK_TYPE]['children']['record']['children']['position'];

        $position['arguments']['data']['config']['label'] = __('Position');
        $position['arguments']['data']['config']['sortOrder'] = 85;        
        $position['arguments']['data']['config']['visible'] = true;
        $position['arguments']['data']['config']['additionalClasses'] = 'data-grid-actions-cell';
        
        $numberOnImage = $this->getNumberOnImageColumn();
        
        $row = $meta[$subject::GROUP_GROUPED]['children'][$subject::LINK_TYPE]['children']['record']['children'];
        $this->array_insert($row, 8, array('position_field' => $position));     
        $this->array_insert($row, 9, array('number_on_image' => $numberOnImage));        
        $meta[$subject::GROUP_GROUPED]['children'][$subject::LINK_TYPE]['children']['record']['children'] = $row;     

        unset($meta[$subject::GROUP_GROUPED]['children'][$subject::LINK_TYPE]['children']['record']['children']['position']);
       
        return $meta;   
    }
    
    
     protected function getNumberOnImageColumn()
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => Form\Element\DataType\Text::NAME,
                        'formElement' => Form\Element\Input::NAME,
                        'componentType' => Form\Field::NAME,
                        'dataScope' => 'number_on_image',
                        'label' => __('Number on Image'),
                        'sortOrder' => 86,
                    ],
                ],
            ],
        ];
        return $column;
    }
 
 
    protected function array_insert(&$array, $position, $insert_array) {
      $first_array = array_splice ($array, 0, $position);
      $array = array_merge ($first_array, $insert_array, $array);
    } 


}
