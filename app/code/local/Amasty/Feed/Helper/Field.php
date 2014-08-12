<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Helper_Field extends Mage_Core_Helper_Abstract
{
    protected $_customBlocks = array(
        'output_value' => 'amfeed/field/output_value/default.phtml',
        'output_value_static' => 'amfeed/field/output_value/static.phtml',
        'new_output' => 'amfeed/field/controls/new_output.phtml',
        'modification' => 'amfeed/field/modification/default.phtml',
        'new_condition' => 'amfeed/field/controls/new_condition.phtml',
        
        
        'actions' => 'amfeed/filter/actions/default.phtml',
        
        'new_value' => 'amfeed/filter/controls/new_value.phtml',
    );
    
    
    function getCustomBlocks(){
        return $this->_customBlocks;
    }
    
    function getDefaultConditions(){
        
        $condtions = array(
            "eq" => "equal",
            "neq" => "not equal",
            "gt" => "greater than",
            "lt" => "less than",
            "gteq" => "greater than or equal to",
            "lteq" => "less than or equal to",
            "like" => "like",
            "nlike" => "not like");
        
        return $condtions;
    }
    
    function getSelectConditions(){
        $condtions = array(
            "eq" => "equal",
            "neq" => "not equal",
        );
        
        return $condtions;
    }
}