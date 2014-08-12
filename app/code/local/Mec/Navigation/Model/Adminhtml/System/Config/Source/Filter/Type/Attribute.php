<?php

class Mec_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type_Attribute{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT, 'label' => $helper->__('Default')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_IMAGE, 'label' => $helper->__('Image')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN, 'label' => $helper->__('Dropdown')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_INPUT, 'label' => $helper->__('Input')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_SLIDER, 'label' => $helper->__('Slider')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT, 'label' => $helper->__('Slider and Input')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER, 'label' => $helper->__('Input an Slider')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT_INBLOCK, 'label' => $helper->__('In Block')),
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
            Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT	=> $helper->__('Default'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_IMAGE	=> $helper->__('Image'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN => $helper->__('Dropdown'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_INPUT	=> $helper->__('Input'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_SLIDER	=> $helper->__('Slider'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT => $helper->__('Slider and Input'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER => $helper->__('Input and Slider'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT_INBLOCK => $helper->__('In Block'),
        );
    }

}
