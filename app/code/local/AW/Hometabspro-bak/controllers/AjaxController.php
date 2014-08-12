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
 * @package    AW_Hometabspro
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */?>
<?php
/**
 * Home Tabs Pro AJAX Controller
 */
class AW_Hometabspro_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * Set as body of response Content Block Instance
     */
    public function requestAction()
    {
            if ($this->getRequest()->getParam('compare')){
                Mage::getSingleton('core/session')->setActiveTab($this->getRequest()->getParam('item'));
                $this->getResponse()->setRedirect(Mage::getBaseUrl());
            }
            else{
                Mage::getSingleton('core/session')->setActiveTab();
            }
            if ($index = $this->getRequest()->getParam('item')){
                $skey = $this->getRequest()->getParam('skey') ? $this->getRequest()->getParam('skey') : null;
    			$this->getResponse()->setBody( Mage::helper('hometabspro')->getContentHtml($index, $skey) );
    		}
    }	
}