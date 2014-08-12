<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Autoscrolling{

    
	const NO = 0;
	const BUTTON = 1;
	const AJAX = 2;
	
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>self::NO, 'label' => $helper->__('No')),
        	array('value'=>self::BUTTON, 'label' => $helper->__('Button')),
        	array('value'=>self::AJAX, 'label' => $helper->__('Ajax')),        	        	
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
            self::NO => $helper->__('No'),
            self::BUTTON => $helper->__('Button'),
            self::AJAX => $helper->__('Ajax'),        	
        );
    }

}
