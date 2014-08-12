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
 * Home Tabs Pro Data Helper
 */
class AW_Hometabspro_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Session Key Registry route
     */
    public static $SKEY = 'aw-hometabspro-skey';

	/**
     * Top Salers Tab
     */
	const TYPE_TOP_SELLERS = 'top_sellers';

    /**
     * Top Rated Tab
     */
	const TYPE_TOP_RATED = 'top_rated';

    /**
     * Top Reviewed Tab
     */
	const TYPE_MOST_REWIEWED = 'most_reviewed';

    /**
     * Just Added Tab
     */
	const TYPE_JUST_ADDED = 'just_added';

    /**
     * Top Wishlist Tab
     */
	const TYPE_WISHLIST_TOP = 'wishlist_top';

    /**
     * Favourite Tab
     */
	const TYPE_FAVOURITE_TAB = 'favourite_tab';

    /**
     * Items to be showed
     * @var array
     */
	protected $_items;

    /**
     * Check version Of Magento
     * @param string $version
     * @return boolean
     */
	public function checkVersion($version)
	{
        return version_compare(Mage::getVersion(), $version, '>=');
	}		

    /**
     * Retrives Flat Mode Flag state for Product Catalog
     * @return boolean
     */
	public function confProductFlatMode()
	{
		return Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
	}

    /**
     * Retrives Show at HomePage flag
     * @return boolean
     */
	public function confShowHome()
	{
		return Mage::getStoreConfig('hometabspro/general/display_at_home');
	}

    /**
     * Retrives position at HomePage
     * @return string
     */
	public function confHomePosition()
	{
		return Mage::getStoreConfig('hometabspro/general/position');
	}

    /**
     * Retrives Show out of stock products
     * @return string
     */
	public function confShowOutOfStock()
	{
		return Mage::getStoreConfig('hometabspro/general/show_out_from_stock');
	}

    /**
     * Retrives path loader image
     * @return string
     */
	public function confLoaderImagePath()
	{
		return Mage::getStoreConfig('hometabspro/general/loader_image');
	}

    /**
     * Retrives Loader Label text
     * @return string
     */
	public function confLoaderText()
	{
		return Mage::getStoreConfig('hometabspro/general/loader_text');
	}	

    /**
     * Retrives Selected Categories
     * @return string
     */
	public function confCategories()
	{
		return Mage::getStoreConfig('hometabspro/favourite_tab/categories');
	}
    /**
     * Retrives Sorting by field
     * @return string
     */
	public function confSorting()
	{
		return Mage::getStoreConfig('hometabspro/favourite_tab/sorting');
	}
    /**
     * Retrives Sorting Direction
     * @return string
     */
	public function confDirection()
	{
		return Mage::getStoreConfig('hometabspro/favourite_tab/direction');
	}

    /**
     * Retrives custom param from store config
     * @param string $route Tab index
     * @param string $param Param index
     * @return mixed
     */
	public function confGetCustomParam($route, $param)
	{
		if ( !($route && $param) ){
			return;
		}
		return Mage::getStoreConfig("hometabspro/{$route}/{$param}");				
	}	

    /**
     * Compare two objects
     * @param Varien_Object $a
     * @param Varien_Object $b
     * @return int
     */
	protected function _compareMenuItems($a, $b)
	{
        if ($a->getOrder() == $b->getOrder())
        {
            return 0;
        }
		return ($a->getOrder() < $b->getOrder()) ? -1 : 1;	
	}	

    /**
     * Retrives objct with config data for tab
     * @param string $key Tab index
     * @return Varien_Object
     */
	protected function _getItemObject($key)
	{
		$item = array(
			'title' => $this->confGetCustomParam($key, 'title'),
			'order' => $this->confGetCustomParam($key, 'order'),
			'products_count' => $this->confGetCustomParam($key, 'products_count'),
			'mode' => $this->confGetCustomParam($key, 'mode'),
			'index' => $key,
		);
		return new Varien_Object($item);			
	}	

    /**
     * Retrives array of tabs that must be displayed
     * @return array
     */
	public function getMenuItems()
	{
		if ($this->_items){
			return $this->_items;
		}
		
		$arr = array();
		
		if ($this->confGetCustomParam(self::TYPE_TOP_SELLERS, 'enabled')){
			$arr[] = $this->_getItemObject(self::TYPE_TOP_SELLERS);		
		}
		
		if ($this->confGetCustomParam(self::TYPE_TOP_RATED, 'enabled')){
			$arr[] = $this->_getItemObject(self::TYPE_TOP_RATED);				
		}
		
		if ($this->confGetCustomParam(self::TYPE_MOST_REWIEWED, 'enabled')){
			$arr[] = $this->_getItemObject(self::TYPE_MOST_REWIEWED);		
		}		
		
		if ($this->confGetCustomParam(self::TYPE_JUST_ADDED, 'enabled')){
			$arr[] = $this->_getItemObject(self::TYPE_JUST_ADDED);		
		}					
		
		if ($this->confGetCustomParam(self::TYPE_WISHLIST_TOP, 'enabled')){
			$arr[] = $this->_getItemObject(self::TYPE_WISHLIST_TOP);		
		}

		if ($this->confGetCustomParam(self::TYPE_FAVOURITE_TAB, 'enabled')){
			$arr[] = $this->_getItemObject(self::TYPE_FAVOURITE_TAB);
		}					

				
		usort($arr, array(&$this, "_compareMenuItems") );			
		return $this->_items = $arr;
	}	

    /**
     * Retrives block name in namespase of Home Tabs Pro
     * @param string $index
     * @return string
     */
	protected function _getBlockName($index)
	{
		return str_replace("_", "", $index);	
	}

    /**
     * Retrives HTML content for AJAX request
     * @param string $index Tab index
     * @param string $skey Session key
     * @return string
     */
	public function getContentHtml($index = null, $skey = null)
	{
        if(Mage::getSingleton('core/session')->getActiveTab())
            $index = Mage::getSingleton('core/session')->getActiveTab();
		if (!$index){
			return ;
		}
		if ($block = Mage::app()->getLayout()->createBlock('hometabspro/content_'.$this->_getBlockName($index))){
            $block->setSkey($skey);
			return $block->setIndex($index)->toHtml();
		}
	}
    
    /**
     * Rertives random key for save current layout for this show
     * @param int $count
     * @return string
     */
    public function getSessionKey($count = 8)
    {
        $pattern = "qwertyuiopasdfghjklzxcvbnm";
        $str = '';
        if ($count > 0){
            for ($i = 0; $i < $count; $i++){
                $str .= $pattern{mt_rand(0, strlen($pattern) - 1)};
            }
        }
        if(!(Mage::registry(self::$SKEY)))
            Mage::register(self::$SKEY, $str);
        return $str;
    }

    public function getGroupedItemPrice($id){


        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');
        if(Mage::helper('hometabspro')->checkVersion('1.4.0.0')){
            $priceTable = $resource->getTableName('catalogindex/price'); // catalog_product_index_price
            $priceField = 'min_price';
        }
        else{
            if(Mage::helper('hometabspro')->checkVersion('1.3.2.4')){
                $priceTable = $resource->getTableName('catalogindex/minimal_price'); // catalogindex_minimal_price
                $priceField = 'value';
            }
        }

        if(isset($priceTable)){
        $select = $db->select()
            ->from($priceTable, $priceField)
            ->where('entity_id = '.$id)
            ->limit(1);
  
        $price = substr($db->fetchOne($select),0,-2);
        return $price;
        }
        else
            return 0;
    }
}