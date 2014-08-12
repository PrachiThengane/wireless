<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Field_Output extends Varien_Object
{
    public function getValue($product, $productParent){
        
        $ret = $this->getStatic();
        
        $attribute = $this->getAttribute();
        $parent = $this->getParent();
        
        if (is_array($attribute)){
            foreach($attribute as $order => $code){
                $useParent = isset($parent[$order]) && $parent[$order] == 'on' ?
                TRUE : FALSE;
                
                $attrVal = NULL;
                
                
                if ($useParent && $productParent !== NULL){
                    $attrVal = $this->_getAttributeValue($productParent, $code);
                }
                
                if ($attrVal === NULL){
                    $attrVal = $this->_getAttributeValue($product, $code);
                }
                
                if ($attrVal !== NULL){
                    $ret = $attrVal;
                }
            }
        }
        
        return $ret;
    }
    
    protected function _getAttributeValue($product, $code){
        $ret = NULL;
        
        $attribute = $product->getResource()->getAttribute($code);

        switch($attribute->getFrontendInput()){
            case "select":
            case "multiselect":
            case "boolean":
                $ret = $product->getAttributeText($code);
                break;
            default:
                $ret = $product->getData($code);
                break;
        }
        return $ret;
    }
}
?>