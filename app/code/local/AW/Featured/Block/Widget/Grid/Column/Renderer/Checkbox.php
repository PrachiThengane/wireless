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
 * @package    AW_Featured
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Featured_Block_Widget_Grid_Column_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox {
    public function renderHeader()
    {
        if($this->getColumn()->getHeader()) {
            return parent::renderHeader();
        }

        $checked = '';
        if ($filter = $this->getColumn()->getFilter()) {
            $checked = $filter->getValue() ? ' checked="checked"' : '';
        }

        $disabled = '';
        if ($this->getColumn()->getDisabled()) {
            $disabled = ' disabled="disabled"';
        }
        return '<input type="checkbox" name="'.$this->getColumn()->getFieldName().'" onclick="'.$this->getColumn()->getGrid()->getJsObjectName().'.checkCheckboxes(this)" class="checkbox"'.$checked.$disabled.' title="'.Mage::helper('adminhtml')->__('Select All').'"/>';
    }
}
