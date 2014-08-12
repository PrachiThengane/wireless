<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Category_Sort{

    /**
     * Options getter
     *
     * @return array
     */
    
    const TOP_BOTTON = 0;
    const LEFT_RIGHT = 1;
    
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>self::TOP_BOTTON, 'label' => $helper->__('Top to Bottom')),
        	array('value'=>self::LEFT_RIGHT, 'label' => $helper->__('Left to Right')),        	        	        	        	
        );
    	
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionHash(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            self::TOP_BOTTON => $helper->__('Top to Bottom'),
            self::LEFT_RIGHT => $helper->__('Left to Right'),                    	        	        	
        );
    }

}
