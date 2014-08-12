<?php
class Amasty_Feed_Block_Adminhtml_Control_Profile extends Amasty_Feed_Block_Adminhtml_Control
{
    public static $_OTHER_CONDITION_QTY = 'qty';
    
    public function getOtherConditions(){
        $other = array(
            self::$_OTHER_CONDITION_CATEGORY => $this->__('Category'),
            self::$_OTHER_CONDITION_QTY => $this->__('Qty'),
        );
        return $other;
    }
    
    
    public function initOtherConditionTemplate($code){
        
        parent::initOtherConditionTemplate($code);
        
        switch ($code){
            case self::$_OTHER_CONDITION_QTY:
                $this->setTemplate('amfeed/' . $this->_templates . '/conditions/other.phtml');
                break;
        }
        return $this;
    }
    
    public function getConditions(){
        $ret = array();
        if ($this->_otherCondition == self::$_OTHER_CONDITION_QTY){
            $helper = Mage::helper('amfeed/field');
            $ret = $helper->getDefaultConditions();
        } else {
            $ret = parent::getConditions();
        }
        return $ret;
    }
}
?>