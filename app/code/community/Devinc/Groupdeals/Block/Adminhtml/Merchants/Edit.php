<?php

class Devinc_Groupdeals_Block_Adminhtml_Merchants_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'groupdeals';
        $this->_controller = 'adminhtml_merchants';
        
		$this->_removeButton('reset');	
        $this->_updateButton('save', 'label', Mage::helper('groupdeals')->__('Save Merchant'));
        $this->_updateButton('save', 'onclick', 'save()');
        $this->_updateButton('delete', 'label', Mage::helper('groupdeals')->__('Delete Merchant'));
		if(Mage::getModel('groupdeals/merchants')->isMerchant()){
			$this->_removeButton('back');
			$this->_removeButton('delete');
			$this->_removeButton('save');
		}	
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

		$storeId = $this->getRequest()->getParam('store', 0);
		
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/store/".$storeId."/');
            }		
			
            function save(){
                editForm.submit($('edit_form').action+'store/".$storeId."/');
            }	
			
			if (document.getElementById('redeem').value=='') { document.getElementById('redeem').value = 'Redeem at location' }
			if (document.getElementById('merchant_info').value=='1') { document.getElementById('merchant_info').checked = true }
			if (document.getElementById('add_edit').value=='1') { document.getElementById('add_edit').checked = true }
			if (document.getElementById('approve').value=='1') { document.getElementById('approve').checked = true }
			if (document.getElementById('delete').value=='1') { document.getElementById('delete').checked = true }
			if (document.getElementById('sales').value=='1') { document.getElementById('sales').checked = true }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('groupdeals_data') && Mage::registry('groupdeals_data')->getId() ) {
            return Mage::helper('groupdeals')->__("Edit Merchant");
        } else {
            return Mage::helper('groupdeals')->__('New Merchant');
        }
    }
}