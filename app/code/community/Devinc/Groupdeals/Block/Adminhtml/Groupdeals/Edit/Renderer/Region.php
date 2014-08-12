<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Region extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element) 
	{		      
        $html = '<tr><td class="label"><label for="region">'.$element->getLabel().' <span class="required">*</span></label></td><td class="value"><div id="region_container">'.$element->getElementHtml().'</div></td></tr>';

        return $html;
    }	
	

}
