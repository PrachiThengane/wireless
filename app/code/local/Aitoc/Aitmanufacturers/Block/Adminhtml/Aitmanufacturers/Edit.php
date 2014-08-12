<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'aitmanufacturers';
        $this->_controller = 'adminhtml_aitmanufacturers';
        
        $this->_updateButton('save', 'label', Mage::helper('aitmanufacturers')->__('Save Brand Page'));
        $this->_updateButton('delete', 'label', Mage::helper('aitmanufacturers')->__('Delete Brand Page'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('content_editor') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content_editor');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content_editor');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('aitmanufacturers_data') && Mage::registry('aitmanufacturers_data')->getId() ) {
            return Mage::helper('aitmanufacturers')->__("Edit Brand Page '%s'", $this->htmlEscape(Mage::registry('aitmanufacturers_data')->getManufacturer()));
        } else {
            return Mage::helper('aitmanufacturers')->__('Add Brand Page');
        }
    }
}
