<?php

class AW_Points_Block_Adminhtml_Coupon extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_coupon';
        $this->_blockGroup = 'points';
        $this->_headerText = Mage::helper('points')->__('Reward Coupons');
        $this->_addButtonLabel = Mage::helper('salesrule')->__('Add New Coupon');
        parent::__construct();
    }

}
