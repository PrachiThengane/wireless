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
 * @package    AW_Boughttogether
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
/**
 * Frequently Bought Together Data Helper
 */
class AW_Boughttogether_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Retrives List html code
     * @return string
     */
	public function getHtml() {
		return  Mage::getSingleton('core/layout')
					->createBlock('boughttogether/list')
					->setTemplate('boughttogether/list.phtml')
					->toHtml();
	}

	/*
	 * Compare param $version with magento version
	 */
	public function checkVersion($version)
	{
		return version_compare(Mage::getVersion(), $version, '>=');
	}

    /**
     * Retrives Extansion state
     * @return boolean
     */
    public function isDisabled()
    {
        return !!Mage::getStoreConfig('advanced/modules_disable_output/AW_Boughttogether');
    }
}
