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
class AW_Hometabspro_Block_Content_Wishlisttop extends AW_Hometabspro_Block_Content
{		
	protected function _prepareCollection()
	{
		$collection = parent::_prepareCollection();
		$wishlistItem = $this->getTableName('wishlist/item');
		$where =  Mage::getSingleton('core/session')->getHometabsProWishlistTop();
		$limit =  Mage::getSingleton('core/session')->getHometabsProWishlistTopLimit();

		$collection->getSelect()
			->join(array('wishlist'=>$wishlistItem), "wishlist.product_id = e.entity_id", array('wcount'=>'COUNT(wishlist.wishlist_item_id)'))
			->where("e.entity_id IN (?)",$where)
			->group('e.entity_id')
			->order('wcount DESC')
			->limit($limit);

		return $collection;
	}	
}