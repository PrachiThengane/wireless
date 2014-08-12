<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Enabledisable{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('value' => 0, 'label'=>Mage::helper('mec_navigation')->__('Disable')),
        );
        
        $websites = Mage::helper('mec_navigation')->getAvailavelWebsites();
        
        if(!empty($websites)){
        	$options[] = array('value' => 1, 'label'=>Mage::helper('mec_navigation')->__('Enable'));
        }
        
        return $options;
    }

}
