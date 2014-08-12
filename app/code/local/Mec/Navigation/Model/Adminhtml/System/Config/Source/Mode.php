<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Mode{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        
    	$helper = Mage::helper('mec_navigation');
    	
        $options = array(
            array('value' => 0, 'label'=>$helper->__('Default')),
        ); 
        
    	$websites = $helper->getAvailavelWebsites();
        
        if(!empty($websites)){
        	$options[] = array('value' => 1, 'label'=>$helper->__('Advanced Navigation'));
        } 
    	
        return $options;
    }

}
