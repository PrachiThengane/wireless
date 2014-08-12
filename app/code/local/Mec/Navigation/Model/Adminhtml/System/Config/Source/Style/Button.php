<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Style_Button{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>'default', 'label' => $helper->__('Default')),
        	array('value'=>'blue', 'label' => $helper->__('Blue')),
        	array('value'=>'orange', 'label' => $helper->__('Orange')),
        	array('value'=>'blue-violet', 'label' => $helper->__('Blue violet')),
        	array('value'=>'pale-pink', 'label' => $helper->__('Pale pink')),
        	array('value'=>'green', 'label' => $helper->__('Green')),
        	array('value'=>'yellow', 'label' => $helper->__('Yellow')),
        	array('value'=>'red', 'label' => $helper->__('Red')),
        	array('value'=>'black', 'label' => $helper->__('Black')),
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
        	'blue' => $helper->__('Blue'),
        	'orange' => $helper->__('Orange'),
        	'blue-violet' => $helper->__('Blue violet'),
        	'pale-pink' => $helper->__('Pale pink'),
        	'green' => $helper->__('Green'),
        	'yellow' => $helper->__('Yellow'),
        	'red' => $helper->__('Red'),
        	'black' => $helper->__('Black'),
        );
    }

}
