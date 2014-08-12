<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Field extends Amasty_Feed_Model_Filter
{
    public function _construct()
    {    
        $this->_init('amfeed/field');
    }
    
    public function getAdvencedAttributes(){
        $retAttrs = array();
        
        $condition = $this->getCondition();
        
        foreach($condition as $value){
            if (array_key_exists("condition", $value) &&
                array_key_exists("type", $value["condition"])){
                foreach($value["condition"]["type"] as $order => $type){
                    if ($type == Amasty_Feed_Model_Filter::$_TYPE_ATTRIBUTE){
                    $attributeCode = $value["condition"]["attribute"][$order];
                    $retAttrs[$attributeCode] = $attributeCode;
                }
            }
            }
            
            if (array_key_exists("output", $value) &&
                array_key_exists("attribute", $value["output"])){
                foreach($value["output"]["attribute"] as $attributeCode){
                    $retAttrs[$attributeCode] = $attributeCode;
                }
            }
        }
        return array_values($retAttrs);
    }
    
    public function hasCategory(){
        
        $condition = $this->getCondition();
        
        foreach($condition as $value){
            if (array_key_exists("condition", $value) &&
                array_key_exists("type", $value["condition"])){
                foreach($value["condition"]["type"] as $order => $type){
                    if ($type == Amasty_Feed_Model_Filter::$_TYPE_OTHER){
                        $code = $value["condition"]["attribute"][$order];
                         
                        if ($code == 'category'){
                            return true;
                        }
                    }
                }
                
            }
        }
       
        return false;
    }
    
    public function fetchByAdvancedCondition($product, $productParent){
        $ret = null;
        $condition = $this->getCondition();
        
        foreach($condition as $value){
            $condition = Mage::getModel('amfeed/field_condition');
            
            if (array_key_exists("condition", $value)){
                $condition->setData($value['condition']);
            }
            
            if ($condition->validate($product)){
            
                $ouput = Mage::getModel('amfeed/field_output');
                $modification = Mage::getModel('amfeed/field_modification');
                
                if (array_key_exists("output", $value)){
                    $ouput->setData($value['output']);
                }
                
                
                if (array_key_exists("modification", $value)){
                    $modification->setData($value['modification']);
                }
                
                $outputVal = $ouput->getValue($product, $productParent);
                
                $ret = $modification->modify($outputVal);
                
                break;
            }
        }
        
        return $ret;
    }
    
    
}