<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position
      extends Mage_Eav_Model_Entity_Attribute_Source_Abstract

{
  
    const LEFT = 0;
    const TOP = 1;
    const RIGHT = 2;
    const BOTTOM = 3;
    
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => self::LEFT,
                    'label' => Mage::helper('mec_navigation')->__('Left'),
                ),
                array(
                    'value' => self::TOP,
                    'label' => Mage::helper('mec_navigation')->__('Top'),
                ),
                array(
                    'value' => self::RIGHT,
                    'label' => Mage::helper('mec_navigation')->__('Right'),
                ),
                array(
                    'value' => self::BOTTOM,
                    'label' => Mage::helper('mec_navigation')->__('Bottom'),
                )
            );
        }
        return $this->_options;
    }
    
    public static function getPositionClass($pos)
    {
        $positionclass = '';
        if (!$pos) $pos = self::LEFT;
        switch ($pos)
        { 
            case self::LEFT: 
                 $positionclass = 'gan-plain-image-left';
               break;
            case self::TOP: 
                 $positionclass = 'gan-plain-image-top';
               break;
            case self::RIGHT: 
                 $positionclass = 'gan-plain-image-right';
               break;   
            case self::BOTTOM: 
                 $positionclass = 'gan-plain-image-bottom';
               break; 
        } 
        
        return $positionclass;
    }

	public static function getOfferBlockPositionClass($pos)
    {
        $positionclass = '';
        if (!$pos) $pos = self::LEFT;
        switch ($pos)
        { 
            case self::LEFT: 
                 $positionclass = 'gan-plain-ob-left';
               break;
            case self::TOP: 
                 $positionclass = 'gan-plain-ob-top';
               break;
            case self::RIGHT: 
                 $positionclass = 'gan-plain-ob-right';
               break;   
            case self::BOTTOM: 
                 $positionclass = 'gan-plain-ob-bottom';
               break; 
        } 
        
        return $positionclass;
    }
    
	public static function getListPositionClass($pos)
    {
        $positionclass = '';
        if (!$pos) $pos = self::LEFT;
        switch ($pos)
        { 
            case self::LEFT: 
                 $positionclass = ' gan-plain-with-image-left';
               break;
            case self::TOP: 
                 $positionclass = ' gan-plain-with-image-top';
               break;
            case self::RIGHT: 
                 $positionclass = ' gan-plain-with-image-right';
               break;   
            case self::BOTTOM: 
                 $positionclass = ' gan-plain-with-image-bottom';
               break; 
        } 
        
        return $positionclass;
    }
    
    
    
            
}