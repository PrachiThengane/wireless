<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Category_Column
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
                    array('value'=>1, 'label' => $helper->__('1')),
                	array('value'=>2, 'label' => $helper->__('2')),
                	array('value'=>3, 'label' => $helper->__('3')),
                	array('value'=>4, 'label' => $helper->__('4')),        	
                	array('value'=>5, 'label' => $helper->__('5')),        	
                ); 
    	}
    	
        return $this->_options;
    	
    }
}
