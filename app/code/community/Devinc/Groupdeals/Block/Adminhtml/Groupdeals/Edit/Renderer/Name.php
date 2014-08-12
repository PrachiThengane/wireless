<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Name extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element) 
	{	
		$productId  = (int) $this->getRequest()->getParam('id');
		$default_name = Mage::getModel('catalog/product')->setStoreId(0)->load($productId)->getName();
		
        $html = '<tr><td class="label"><label for="'.$element->getId().'">'.$element->getLabelHtml().'</label></td><td class="value">'.$element->getElementHtml().'</td><td class="scope-label"><span class="nobr">[STORE VIEW]</span></td>';
		if (!Mage::app()->isSingleStoreMode() && $this->getRequest()->getParam('store', 0)!=0) {
			if ($default_name!=$element->getValue()) {
				$html .= '<td class="value use-default">
							<input type="checkbox" value="'.$element->getId().'" onclick="toggleValueElements(this, this.parentNode.parentNode)" id="'.$element->getId().'_default" name="use_default[]">
							<label class="normal" for="'.$element->getId().'_default">Use Default Value</label>
						</td>';
			} else {
				$html .= '<td class="value use-default">
							<input type="checkbox" value="'.$element->getId().'" onclick="toggleValueElements(this, this.parentNode.parentNode)" checked="checked" id="'.$element->getId().'_default" name="use_default[]">
							<label class="normal" for="'.$element->getId().'_default">Use Default Value</label>
						</td><script type="text/javascript">document.getElementById(\'name\').disabled = \'disabled\'</script>';
			}
		}
		
		$html .= '</tr>';
		
        return $html;
    }	

}
