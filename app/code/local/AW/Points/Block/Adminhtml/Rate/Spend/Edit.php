<?php
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
 */
class AW_Points_Block_Adminhtml_Rate_Spend_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
		$this->_objectId = 'id';
        $this->_blockGroup = 'points';
        $this->_controller = 'adminhtml_rate_spend';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('points')->__('Save Rate'));
        $this->_updateButton('delete', 'label', Mage::helper('points')->__('Delete Rate'));
    }

    public function getHeaderText()
    {
        $rate = Mage::registry('points_rate_data');
        if ($rate->getId()) {
            return Mage::helper('points')->__("Edit Rate #%s", $this->htmlEscape($rate->getId()));
        }
        else {
            return Mage::helper('points')->__('Add Rate');
        }
    }

}
