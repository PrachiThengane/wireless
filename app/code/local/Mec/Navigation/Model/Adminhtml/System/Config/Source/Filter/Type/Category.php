<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type_Category{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT, 'label' => $helper->__('Default')),
            array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT_PRO, 'label' => $helper->__('Fly-Out')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_IMAGE, 'label' => $helper->__('Image')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN, 'label' => $helper->__('Dropdown')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_PLAIN, 'label' => $helper->__('Plain')),
        	//array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_FOLDING, 'label' => $helper->__('Folding')),
        	array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT_INBLOCK, 'label' => $helper->__('In Block')),
        	//array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_ACCORDION, 'label' => $helper->__('Accordion')),
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
            Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT_PRO	=> $helper->__('Fly-Out'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_IMAGE	=> $helper->__('Image'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN => $helper->__('Dropdown'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_PLAIN => $helper->__('Plain'),
        	//Mec_Navigation_Model_Layer::FILTER_TYPE_FOLDING => $helper->__('Folding'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT_INBLOCK => $helper->__('In Block'),
        	Mec_Navigation_Model_Layer::FILTER_TYPE_ACCORDION => $helper->__('Accordion'),
        );
    }

}
