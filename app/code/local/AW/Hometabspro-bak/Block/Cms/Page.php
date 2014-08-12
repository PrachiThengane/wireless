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

class AW_Hometabspro_Block_Cms_Page extends Mage_Cms_Block_Page
{
	public function __construct()
	{

	}
	
    protected function _prepareLayout()
    {

    }
	
    public function disableBlock($alias)
    {
        if ($alias && !Mage::getStoreConfig('advanced/modules_disable_output/AW_Hometabspro') )
        {
            $this->getParentBlock()->unsetChild($alias);
        }
    }
} 