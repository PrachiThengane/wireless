<?php
class Mec_Catalogsearch_Helper_Data extends Mage_Core_Helper_Abstract
{
	function getCategories()
	{
		/*$arrays = array();
		$collection = Mage::getModel('catalog/category')->getCollection()
			->addNameToResult()
			->addUrlRewriteToResult()
			->addIsActiveFilter();
		foreach ($collection as $item) {
			if($item->getLevel() == 2){
				$arrays[$item->getId()] = $item->getName();
			}
			//Mage::log($item);
		}
		//Mage::log($arrays);
		*/
		$_config = Mage::getStoreConfig('mec_catalogsearch/advancedcatalogsearch/textforcategory');
		$config = explode(",", $_config);
		foreach ($config as $id) {
				$arrays[$id] = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($id)->getName();
			//Mage::log($item);
		}
		return $arrays;
    }

	function isSelected($id)
	{
		$currentCategoryId = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
		//Mage::log($currentCategoryId);
		if($currentCategoryId){
			if($id == $currentCategoryId){
				return true;
			}else{
				if($_GET){
					if($_GET['cat']){
						$catValue = $_GET['cat'];
						if($catValue == $id){
							return true;
						}else{
							return false;
						}
					}
				}
				//return false;
			}
		}else{
				return false;
		}
    }
    
    function isHome()
    {
		$category = Mage::registry('current_category');
		if (!$category) {
			return true;
		}else{
			return false;
		}
	}
	
}