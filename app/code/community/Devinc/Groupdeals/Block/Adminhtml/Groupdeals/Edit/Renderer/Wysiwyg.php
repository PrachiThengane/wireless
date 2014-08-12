<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Wysiwyg extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element) 
	{		      	
		$default_array = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('groupdeals_id', $this->getRequest()->getParam('id'))->getColumnValues($element->getId());
		if (!isset($default_array[0]) || is_null($default_array[0])) {
			$default_array[0] = '';		
		}
		$default = Mage::getModel('groupdeals/groupdeals')->getDecodeString($default_array[0], 0);  
		
        $html = '<tr><td class="label"><label for="'.$element->getId().'">'.$element->getLabelHtml().'</label></td><td class="value">'.$element->getElementHtml().'</td><td class="scope-label"><span class="nobr">[STORE VIEW]</span></td>';
		if (!Mage::app()->isSingleStoreMode() && $this->getRequest()->getParam('store', 0)!=0) {
			if ($default!=$element->getValue()) {
				$html .= '<td class="value use-default">
							<input type="checkbox" value="1" onclick="toggleValueElements(this, this.parentNode.parentNode)" id="'.$element->getId().'_default" name="'.$element->getId().'_default">
							<label class="normal" for="'.$element->getId().'_default">Use Default Value</label>
						</td>';
			} else {
				$html .= '<td class="value use-default">
							<input type="checkbox" value="1" onclick="toggleValueElements(this, this.parentNode.parentNode)" checked="checked" id="'.$element->getId().'_default" name="'.$element->getId().'_default">
							<label class="normal" for="'.$element->getId().'_default">Use Default Value</label>
						</td><script type="text/javascript">document.getElementById(\''.$element->getId().'\').disabled = \'disabled\'</script>';
			}		
		}		
		$html .= '</tr>';
		
        return $html;
    }	
	

}
