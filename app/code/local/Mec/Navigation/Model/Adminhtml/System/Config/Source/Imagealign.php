<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Imagealign{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>'left', 'label' => $helper->__('Left')),
        	array('value'=>'right', 'label' => $helper->__('Right')),
        	array('value'=>'top', 'label' => $helper->__('Top')),
        	array('value'=>'bottom', 'label' => $helper->__('Bottom')),
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
            'left' => $helper->__('Left'),
            'right' => $helper->__('Right'),
            'top' => $helper->__('Top'),
        	'bottom' => $helper->__('Bottom'),
        );
    }

}
