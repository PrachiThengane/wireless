<?php
class Devinc_Groupdeals_Block_Popup extends Mage_Core_Block_Template
{	
    public function getActiveRecentIds()
    {
    	$prod_ids = array();
 		$attribute_set_id = Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToFilter('attribute_set_name','Group Deal')->getFirstItem()->getId();
		$productCollection = Mage::getModel('catalog/product')->getCollection()
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addAttributeToFilter('attribute_set_id', $attribute_set_id)
			->addAttributeToFilter('groupdeal_status', array('in' => array(1, 4)));
			
		$magento_version = (int)str_replace(".", "", Mage::getVersion());
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
        
		if ($magento_version>=1410) {	
        	Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($productCollection);
        }
					
		$prod_ids = $productCollection->getColumnValues('entity_id');
		
        return $prod_ids;
    } 
    
    public function getCountryCollection($active_recent_ids)
    {
 		$countryCollection = Array();
 		$country_ids = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('country_id', array('neq' => ''))->addFieldToFilter('product_id', array('in' => $active_recent_ids))->setOrder('country_id', 'ASC')->getColumnValues('country_id');
		$unique_country_ids = array_unique($country_ids);
		
		$i = 0;
		if (count($unique_country_ids)>0) {
			foreach($unique_country_ids as $country_id){
    		    $countryCollection[$i]['value'] = $country_id;
    		    $countryCollection[$i]['label'] = Mage::app()->getLocale()->getCountryTranslation($country_id);
			    $i++;
			}
		}
		
        return $countryCollection;
    } 
	
	public function getRegionCollection($country_id, $active_recent_ids)
    {
 		$regionCollection = Array();
 		$regions = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('country_id', $country_id)->addFieldToFilter('product_id', array('in' => $active_recent_ids))->setOrder('region', 'ASC')->getColumnValues('region');
		$unique_regions = array_unique($regions);
		
		if (count($unique_regions)>0) {
			foreach($unique_regions as $region){
    			$regionCollection[] = $region;
			}
		}
		
        return $regionCollection;
    } 	
    
    public function getCityCollection($region, $active_recent_ids)
    {
 		$cityCollection = Array();
		if ($region != null) {
			$cities = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('region', $region)->addFieldToFilter('product_id', array('in' => $active_recent_ids))->setOrder('city', 'ASC')->getColumnValues('city');
		} else {
			$cities = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', array('in' => $active_recent_ids))->setOrder('city', 'ASC')->getColumnValues('city');
		}
		$unique_cities = array_unique($cities);
		
		if (count($unique_cities)>0) {
			foreach($unique_cities as $city){
				$cityCollection[] = $city;
			}
		}		
		
        return $cityCollection;
    }
   
	public function getCityUrl($city)
    {
		$groupdeals_id = '';			
		$groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', $city)->setOrder('groupdeals_id', 'DESC');
		$past_deals = false;
		$url_key = '';

		foreach ($groupdealsCollection as $groupdeals) {	
			$groupdeals_status = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($groupdeals->getProductId())->getGroupdealStatus();
			if ($groupdeals_status==1) {
				$groupdeals_id = $groupdeals->getId();
				$product_id = $groupdeals->getProductId();
				break;
			} elseif ($groupdeals_status==4) {
				$past_deals = true;
			}
		}
		
		if ($groupdeals_id!='') {
			$_product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($product_id);				
					
			if ($_product->getUrlPath()!='') {
				$url_key = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$_product->getUrlPath().'?city='.urlencode($city);
			} else {
				$url_key = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'groupdeals/product/view/id/'.$product_id.'/groupdeals_id/'.$groupdeals_id.'/'.'?city='.urlencode($city);			
			}
		} elseif ($past_deals) {
			$url_key = $this->getUrl('groupdeals/product/recent').'city/'.urlencode($city);
		}
		
        return $url_key;
    } 
   
	public function getNoDealsMessage()
    {		
		$url_key = $this->getUrl('groupdeals/product/nodeals');
		
        return $url_key;
    } 
	
	public function getCurrentCrc()
    {
		$city = Mage::getSingleton('core/session')->getCity();
		if (isset($city) && $city!='' && $city!='Universal') {
			$crc = Array();
			$groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', $city);	
			
			foreach ($groupdealsCollection as $groupdeals) {	
				$crc['country'] = $groupdeals->getCountryId();
				$crc['region'] = $groupdeals->getRegion();
				$crc['city'] = $groupdeals->getCity();
				break;
			}
			
			return $crc;
		} elseif (isset($city) && $city!='' && $city=='Universal') {
			$crc['country'] = 'Universal';
			$crc['region'] = 'Universal';
			$crc['city'] = 'Universal';
			
			return $crc;
		} else {
			$crc['country'] = '';
			$crc['region'] = '';
			$crc['city'] = '';
			
			return $crc;
		}
    }
	
	public function getFormActionUrl() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'groupdeals/product/subscribe/';
	} 	
}