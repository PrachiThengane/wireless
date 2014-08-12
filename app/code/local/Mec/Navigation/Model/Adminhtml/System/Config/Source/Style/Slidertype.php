<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Style_Slidertype{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            /*array('value'=>'default', 'label' => $helper->__('Default')),*/
            array('value'=>'cone', 'label' => $helper->__('Cone')),
        	array('value'=>'rectangle', 'label' => $helper->__('Rectangle')),
        	
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
        	'rectangle' => $helper->__('Rectangle'),
        	'cone' => $helper->__('Cone'),
        );
    }

}
