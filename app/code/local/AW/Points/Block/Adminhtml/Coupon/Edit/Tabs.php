<?php

class AW_Points_Block_Adminhtml_Coupon_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('coupon_id');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('points')->__('Reward Coupon'));
    }

    protected function _beforeToHtml() {
        $helper = Mage::helper('points');

        $this->addTab('main_section', array(
            'label' => $helper->__('Coupon Information'),
            'title' => $helper->__('Coupon Information'),
            'content' => $this->getLayout()->createBlock('points/adminhtml_coupon_edit_tab_main')->toHtml(),
            'active' => true
        ));

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Points
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */        if ($this->getRequest()->getParam('id')) {
            $this->addTab('form_section1', array(
                'label' => $helper->__('Transactions'),
                'title' => $helper->__('Transactions'),
                'url' => $this->getUrl('*/*/transactions', array('_current' => true)),
                'class' => 'ajax',
            ));
        }
        return parent::_beforeToHtml();
    }

}