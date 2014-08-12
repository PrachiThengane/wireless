<?php

class AW_Points_Block_Adminhtml_Coupon_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {

        parent::__construct();
        $this->setId('points_coupon_form');
        $this->setTitle(Mage::helper('salesrule')->__('Coupon Information'));
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form(
                        array(
                            'id' => 'edit_form',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}