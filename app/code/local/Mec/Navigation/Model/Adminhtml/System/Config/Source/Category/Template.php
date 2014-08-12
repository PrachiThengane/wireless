<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Category_Template
      extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{

    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {    	
    	$helper = Mage::helper('mec_navigation');

    	if (!$this->_options) {
    	    $this->_options = array(
                    array('value'=>1, 'label' => $helper->__('Style 1')),
                	array('value'=>2, 'label' => $helper->__('Style 2')),                	        	
                ); 
    	}
    	
        return $this->_options;
    	
    }
}
