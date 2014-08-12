<?php
 		
class Mec_Navigation_Block_Adminhtml_Config_Form_Renderer_Website extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	
    	$html = '';
    	
    	$r = Mage::getStoreConfig('mec_activation/advancednavigation/ar');
    	
    	$value = explode(',', str_replace($r, '', Mage::helper('core')->decrypt($element->getValue())));
    	
    	$nameprefix = $element->getName();
    	$idprefix = $element->getId();
    	
    	$element->setName($nameprefix . '[]');
    	
    	
    	//$info = Mage::helper('mec_navigation')->ga();
        
        //if(isset($info['d']) && isset($info['c']) && intval($info['c']) > 0){
    	
    	
    	foreach (Mage::app()->getWebsites() as $website) {
    		
    		//$element->setChecked(false);
    		$element->setChecked(true);
    		
            $id = $website->getId();
            $name = $website->getName();
            
            $element->setId($idprefix.'_'.$id);
            $element->setValue($id);
            $element->setClass('mec-navigation-available-sites');
            
            //if(in_array($id, $value) !== false){
            //	$element->setChecked(true);
            //}
            
            if ($id!=0) {
            	$html .= '<div><label>'.$element->getElementHtml().' '.$name.' </label></div>';
            }
        }
        
        
        
        /*$html .= '
        	<input id="'.$idprefix.'_diasbled" type="hidden" disabled="disabled" name="'.$nameprefix.'" />
        	<script type="text/javascript">
        	
        	function updateMecNavigationWebsites(){
        		
        		$("'.$idprefix.'_diasbled").disabled = "disabled";
        		
        		if($$(".mec-navigation-available-sites:checked").length >= '.intval($info['c']).'){
    				$$(".mec-navigation-available-sites").each(function(e){
    					if(!e.checked){
    						e.disabled = "disabled";
    					}
    				});
    				
    			}else {
    				$$(".mec-navigation-available-sites").each(function(e){
    					if(!e.checked){
    						e.disabled = "";
    					}
    				});
    				if($$(".mec-navigation-available-sites:checked").length == 0){
    				
    					$("'.$idprefix.'_diasbled").disabled = "";
    				
    				}
    				
    			}
        	}
        	
        	$$(".mec-navigation-available-sites").each(function(e){
        		e.observe("click", function(){
        			updateMecNavigationWebsites();
        		});
        	});
        	
        	updateMecNavigationWebsites();
        	
        </script>';
    	*/
    	
    	$html .= '
        	<input id="'.$idprefix.'" type="hidden" name="'.$nameprefix.'" />
        	';
    	
    	//}else{
    	//	$html = sprintf('<strong class="required">%s</strong>', $this->__('Please enter a valid key'));
    	//}
    	
    	return $html;
    	
    }
}
