<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Filter_Image_Align{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            0 => $helper->__('Vertically'),
            1 => $helper->__('Horizontally'),
            2 => $helper->__('2 columns'),
        );
    }

}
