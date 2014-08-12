<?php

class AW_Points_Block_Adminhtml_Coupon_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_objectId = 'id';
        $this->_blockGroup = 'points';
        $this->_controller = 'adminhtml_coupon';
        parent::__construct();
    }

    public function getHeaderText() {

        $coupon = Mage::registry('points_coupon_data');
        return ($coupon->getId()) ? (Mage::helper('points')->__('Coupon # %s ', $coupon->getId())) : Mage::helper('points')->__('New coupon');
    }

}