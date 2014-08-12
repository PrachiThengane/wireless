<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Field_Condition extends Varien_Object
{
    public function validate($product){
        $ret = TRUE;
        
        $type = $this->getType();
        $attribute = $this->getAttribute();
        $operator = $this->getOperator();
        $value = $this->getValue();
        
        if (is_array($type) && is_array($attribute) && is_array($operator) && is_array($value)){
        
            foreach($type as $order => $typeCode){
                $operatorCode = $operator[$order];
                $valueCode = $this->_getOutputValue($value[$order]);
                $attributeCode = $attribute[$order];
                
                switch($typeCode){
                    case Amasty_Feed_Model_Filter::$_TYPE_ATTRIBUTE:
                        $ret = $this->_compare($product, $attributeCode, $operatorCode, $valueCode);
                        break;
                    case Amasty_Feed_Model_Filter::$_TYPE_OTHER:
                        if ($attributeCode == 'category'){
                            
                            $ret = $this->_compareCategories($product, $operatorCode, $valueCode);
                        }
                        break;
                }
                
                
                
                if ($ret === FALSE){
                    break;
                }
            }
        }
        
        return $ret;
    }
    
    protected function _compareCategories($product, $operator, $value){
        $ret = FALSE;
        $ids = $product->getCategoryIds();
        
        switch ($operator){
            case "eq":
                $ret = in_array($value, $ids);
                break;
            case "neq":
                $ret = !in_array($value, $ids);
                break;
        }
        return $ret;
    }
    
    protected function _compare($product, $code, $operator, $value){
        $ret = FALSE;
        $attributeValue = $product->getData($code);
        
        
//        var_dump($product->getId(), $attributeValue, $operator, $value);
//        exit(1);
        switch ($operator){
            case "eq":
                $ret = $attributeValue == $value;
                break;
            case "neq":
                $ret = $attributeValue != $value;
                break;
            case "gt":
                $ret = $attributeValue > $value;
                break;
            case "lt":
                $ret = $attributeValue < $value;
                break;
            case "gteq":
                $ret = $attributeValue >= $value;
                break;
            case "lteq":
                $ret = $attributeValue <= $value;
                break;
            case "like":
                $ret = mb_strpos($attributeValue, $value) !== FALSE;
                break;
            case "nlike":
                $ret = mb_strpos($attributeValue, $value) === FALSE;
                break;
        }
        
        return $ret;
    }
    
    protected function _getOutputValue($val){
        $ret = $val;
        
        return $ret;
    }
}