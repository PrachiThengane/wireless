<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Couponsent extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
	public function render(Varien_Object $row)
    {	
		$storeId = $this->getRequest()->getParam('store', 0);
		$order_id = $row->getData($this->getColumn()->getIndex());
		$itemsCollection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $order_id);
		$coupon_codes = '';
		if (count($itemsCollection)>0) {
			foreach ($itemsCollection as $item) {
				$couponCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('groupdeals_id', $this->getRequest()->getParam('groupdeals_id'))->addFieldToFilter('order_item_id', $item->getId());
				foreach($couponCollection as $coupon){
					if ($coupon->getStatus()=='sending') {
						return 'Sending';
					} elseif ($coupon->getStatus()=='complete') {
						if ($coupon->getRedeem()=='not_used') {
							$coupon_codes .= '<strong>'.$coupon->getCouponCode().'</strong> <br/><a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('groupdeals/adminhtml_groupdeals/preview', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store' => $storeId, 'coupon_id' => $coupon->getId())).'">View</a> || <a href="'.Mage::helper('adminhtml')->getUrl('groupdeals/adminhtml_groupdeals/redeem', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store' => $storeId, 'coupon_id' => $coupon->getId())).'">Redeem</a><br/>';
						} elseif ($coupon->getRedeem()=='used') {
							$coupon_codes .= '<strong>'.$coupon->getCouponCode().'</strong> <br/><a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('groupdeals/adminhtml_groupdeals/preview', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store' => $storeId, 'coupon_id' => $coupon->getId())).'">View</a> || USED<br/>';
						}
					}
				}
			}
		}
		//$coupon_codes = substr($coupon_codes,0,-5);
		if ($coupon_codes!='') {
			return $coupon_codes;
		} else {
			return 'Coupon Not Sent';			
		}
    }
	 
   
}
