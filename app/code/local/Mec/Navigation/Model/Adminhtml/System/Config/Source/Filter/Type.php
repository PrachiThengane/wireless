<?php

class Mec_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value' => Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT, 'label'=>$helper->__('Default')),
            array('value' => Mec_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN, 'label'=>$helper->__('Dropdown')),
            array('value' => Mec_Navigation_Model_Layer::FILTER_TYPE_INPUT, 'label'=>$helper->__('Input')),
            array('value' => Mec_Navigation_Model_Layer::FILTER_TYPE_SLIDER, 'label'=>$helper->__('Slider')),
            array('value' => Mec_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT, 'label'=>$helper->__('Slider and Input')),
            array('value' => Mec_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER, 'label'=>$helper->__('Input and Slider')),
            
        );
    }

}
