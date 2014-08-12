<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * This extension is only for developers as a technology exchange
 * Based on EasyTestimonial_v1.5.8 by mage-world.com
 * Fixed the bug that when compilation has been enabled, the testimonial tab in the backend will be blank page.
 *****************************************************
 * @category   Cc
 * @package    Cc_Testimonial
 * @Author     Chimy
 */
?>
<?php

class Cc_Testimonial_Block_Adminhtml_Testimonial_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'testimonial';
        $this->_controller = 'adminhtml_testimonial';
        
        $this->_updateButton('save', 'label', Mage::helper('testimonial')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('testimonial')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('testimonial_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'testimonial_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'testimonial_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('testimonial_data') && Mage::registry('testimonial_data')->getId() ) {
            return Mage::helper('testimonial')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('testimonial_data')->getClientName()));
        } else {
            return Mage::helper('testimonial')->__('Add Item');
        }
    }
}
