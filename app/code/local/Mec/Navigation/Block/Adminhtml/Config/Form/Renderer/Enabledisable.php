<?php

class Mec_Navigation_Block_Adminhtml_Config_Form_Renderer_Enabledisable extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
    	
    	$websites = Mage::helper('mec_navigation')->getAvailabelWebsites();
		
        if(!empty($websites)){

    			$scope_website_code = $this->getRequest()->getParam('website');

    			$scope_website = Mage::getModel('core/website')->load($this->getRequest()->getParam('website'), 'code');
    			
    			if($scope_website && in_array($scope_website->getWebsiteId(), $websites)){

    				$html = $element->getElementHtml();

    			}elseif(!$scope_website_code){

    				$html = $element->getElementHtml();

    			}else{

    				$html = '<strong class="required">'.$this->__('Please buy additional domains').'</strong>';

    			}

    	}else{

    		$html = '<strong class="required">'.$this->__('Please enter a valid key').'</strong>';

    	}

    	return $html;
    }

}
