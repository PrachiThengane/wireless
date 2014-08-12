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
 */
class AW_Hometabspro_Model_Observer
{

    public function beforeBlocksGenerate($Event) {	
	 
	 if($count = AW_Hometabspro_Helper_Cache::getFlag('top_sellers')) {	
            $TS = new AW_Hometabspro_Helper_Cache_Topsellers();
            $TS->setCache(array("limit"=>$count)); 
	 }	
	  
      if($count = AW_Hometabspro_Helper_Cache::getFlag('top_rated')) {	
            $TR = new AW_Hometabspro_Helper_Cache_Toprated();
            $TR->setCache(array("limit"=>$count));
	  }
	  
	  if($count = AW_Hometabspro_Helper_Cache::getFlag('most_reviewed')) {	
            $MR = new AW_Hometabspro_Helper_Cache_Mostreviewed();
            $MR->setCache(array("limit"=>$count));
	  }
	  
	 if($count = AW_Hometabspro_Helper_Cache::getFlag('just_added')) {	
            $JA = new AW_Hometabspro_Helper_Cache_Justadded();
            $JA->setCache(array("limit"=>$count));
	  } 
			
	 if($count = AW_Hometabspro_Helper_Cache::getFlag('wishlist_top')) {	
            $WT = new AW_Hometabspro_Helper_Cache_Wishlisttop();
            $WT->setCache(array("limit"=>$count));
	 }

		 
	if($count = AW_Hometabspro_Helper_Cache::getFlag('favourite_tab')) {	
            $FT = new AW_Hometabspro_Helper_Cache_Favourites();
            $FT->setCache(array("limit"=>$count));
	 }
		 
	}
	
}