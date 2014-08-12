<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Style{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>'default', 'label' => $helper->__('Default')),
        	array('value'=>'white', 'label' => $helper->__('White')),
        	array('value'=>'gray', 'label' => $helper->__('Gray')),
        	array('value'=>'blue', 'label' => $helper->__('Blue')),
        	array('value'=>'red', 'label' => $helper->__('Red')),
        	
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
            'default' => $helper->__('Default'),
            'white' => $helper->__('White'),
            'gray' => $helper->__('Gray'),
        	'blue' => $helper->__('Blue'),
        	'red' => $helper->__('Red'),
        	
        );
    }

}
