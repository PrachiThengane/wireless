<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type_Category_Mode{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>0, 'label' => $helper->__('Breadcrumbs')),
        	array('value'=>1, 'label' => $helper->__('Ajax')),
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
            0 => $helper->__('Breadcrumbs'),
        	0 => $helper->__('Ajax'),
        );
    }

}
