<?php
class Amasty_Feed_Block_Adminhtml_Control extends Mage_Core_Block_Template
{
    public static $_OTHER_CONDITION_CATEGORY = 'category';
    
    protected $_attribute = null;
    protected $_otherCondition = null;
    protected $_templates = 'filter';
    
    public function initTemplate($attribute){
        $this->_attribute = $attribute;
        
        if ($attribute){
            switch($attribute->getFrontendInput()){
                case "select":
                case "multiselect":
                case "boolean":
                    $this->setTemplate('amfeed/' . $this->_templates . '/conditions/select.phtml');
                    break;
                default:
                    $this->setTemplate('amfeed/' . $this->_templates . '/conditions/default.phtml');
                    break;
            }            
        }
        return $this;
    }
    
    public function initOtherConditionTemplate($code){
        
        $this->_otherCondition = $code;
        
        switch ($code){
            case self::$_OTHER_CONDITION_CATEGORY:
                $this->setTemplate('amfeed/' . $this->_templates . '/conditions/category.phtml');
                break;
        }
        return $this;
    }
    
    public function getConditions(){
        $ret = array();
        $helper = Mage::helper('amfeed/field');
        
        if ($this->_attribute){
            switch($this->_attribute->getFrontendInput()){
                case "select":
                    $ret = $helper->getSelectConditions();
                    break;
                default:
                    $ret = $helper->getDefaultConditions();
                    break;
            }            
        }
        
        
        return $ret;
    }
    
    public function getAttributes($withEmpty = FALSE, $emptyTitle = "None"){
        
        $attributes = array();
        
        if ($withEmpty){
            $attributes = array_merge(
                    $attributes, 
                    array("" => $emptyTitle)
                );
        }
        
        $collection = $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setEntityTypeFilter(Mage::getResourceModel('catalog/product')->getTypeId());
        
        foreach($collection as $attribute){
            $label = $attribute->getFrontendLabel();
            if (!empty($label))
                $attributes[$attribute->getAttributeCode()] = $label;
        }
        
        return $attributes;
    }
    
    public function getCategories(){
        return Mage::helper('amfeed/category')->getOptionsForFilter();
    }
    
    public function getOtherConditions(){
        $other = array(
            self::$_OTHER_CONDITION_CATEGORY => $this->__('Category')
        );
        return $other;
    }    
}
?>