<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Category_Attribute_View 
		extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{

	const TEXT = 1;
	const IMAGE = 2;
	const TEXT_IMAGE = 3;
	
    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>self::TEXT, 'label' => $helper->__('Text')),
        	array('value'=>self::IMAGE, 'label' => $helper->__('Image')),
        	array('value'=>self::TEXT_IMAGE, 'label' => $helper->__('Text and Image')),        	        	        	
        );
    	
    }
        
}
