<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Additional extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element) 
	{		      	
		
        $html = '<tr><td class="label"><label for="'.$element->getId().'">'.$element->getLabelHtml().'</label></td><td class="value">'.$element->getElementHtml().'<button  id="id_'.$element->getId().'" type="button" class="scalable " onclick="catalogWysiwygEditor.open(\''.Mage::getUrl('adminhtml/catalog_product/wysiwyg').'\', \''.$element->getId().'\')" style=""><span>'.$this->__('WYSIWYG Editor').'</span></button></td><td class="scope-label"></td>';
		$html .= '</tr>';
		
        return $html;
    }	
	

}
