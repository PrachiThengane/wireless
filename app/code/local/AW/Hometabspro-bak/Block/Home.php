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
class AW_Hometabspro_Block_Home extends Mage_Core_Block_Template
{
	public function isHome()
	{
		return (Mage::getSingleton('cms/page')->getIdentifier() == Mage::getStoreConfig('web/default/cms_home_page'));
	} 
	
	public function showHome()
	{
		return ($this->isHome() && Mage::helper('hometabspro')->confShowHome() && ($this->getPosition() == Mage::helper('hometabspro')->confHomePosition()) );	
	}	
	
	public function getTabsHtml()
	{
		return $this->getLayout()->createBlock('hometabspro/tabs')->setName('aw.hometabspro.tabs.'.$this->getPosition())->toHtml();
	}
}

