<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Model_Aitmanufacturers extends Mage_Core_Model_Abstract
{
    protected $_collection = null;
    protected $_optionCollection = null;
    protected static $_url = null;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitmanufacturers/aitmanufacturers');
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ('' == $this->getData('root_template'))
            $this->setData('root_template', 'two_columns_left');
    }
    
    public function getManufacturerName($manufacturerId)
    {
        return $this->getResource()->getAttributeOptionValue($manufacturerId);
    }
    
    public function loadByManufacturer($manufacturerId)
    {
        $storeId = Mage::app()->getStore()->getId();
        return $this->getCollection()->addStoreFilter($storeId)
            ->addFieldToFilter('main_table.manufacturer_id', array("eq"=>$manufacturerId))->getFirstItem();
    }
    
    public function toManufacturersOptionsArray($storeId = null)
    {
        return $this->getCollection()->toManufacturersOptionsArray($storeId);
    }
    
    public function checkUrlKey($urlKey, $storeId)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $storeId);
    }
    
    public function isUniqueUrlKey($urlKey, $id = 0, $storeId = null)
    {
        if (is_null($storeId)){
            $storeId = Mage::app()->getStore()->getId();
        }
        $id = $this->_getResource()->checkUniqueUrlKey($urlKey, $id, $storeId);
        return (bool)empty($id);
    }
    
    public function getUrl($storeId = null)
    {
        if ($this->getId())
        {
            if (is_null($storeId))
            {
                $storeId = Mage::app()->getStore()->getId();
            }
            
            $rewriteModel = Mage::getModel('core/url_rewrite');
            $rewriteCollection = $rewriteModel->getCollection();
            $rewriteCollection->addStoreFilter($storeId, true)
                              ->setOrder('store_id', 'DESC')
                              ->addFieldToFilter('target_path', 'brands/index/view/id/' . $this->getId())
                              ->setPageSize(1)
                              ->load();
            if (count($rewriteCollection) > 0)
            {
                foreach ($rewriteCollection as $rewrite) 
				{
                    $rewriteModel->setData($rewrite->getData());
                }
                
                return Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $rewriteModel->getRequestPath();
            } 
			else 
            {
                return $this->getUrlInstance()->getUrl('brands/index/view', array('id' => $this->getId()));
            }
        }
        return '';
    }
    
    /**
     * Retrieve URL instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('core/url');
        }
        return self::$_url;
    }
    
    public function getProductsByManufacturer($manufacturerId, $storeId)
    {
        $resource = Mage::getResourceModel('catalogindex/attribute');
        $select = $resource->getReadConnection()->select();
        
        $select->from($resource->getMainTable(), 'entity_id')
            ->distinct(true)
            ->where('store_id = ?', $storeId)
            ->where('attribute_id = ?', Mage::helper('aitmanufacturers')->getAttributeId())
            ->where('value = ?', $manufacturerId);
        return $resource->getReadConnection()->fetchCol($select);
    }
    
    public function getManufacturersByProducts($productIds, $storeId)
    {
        $resource = Mage::getResourceModel('catalogindex/attribute');
        $select = $resource->getReadConnection()->select();

        if (empty($productIds))
        {
            return array();
        }
        
        $select->from($resource->getMainTable(), 'value')
            ->distinct(true)
            ->where('store_id = ?', $storeId)
            ->where('attribute_id = ?', Mage::helper('aitmanufacturers')->getAttributeId())
            ->where('entity_id IN (?)', $productIds);

        return $resource->getReadConnection()->fetchCol($select);
    }
    
    public function isImageExists($iManufacturerId, $sFileName)
    {
        return $this->_isFileExists('image', $iManufacturerId, $sFileName);     	
    }
    
    public function isLogoExists($iManufacturerId, $sFileName)
    {
        return $this->_isFileExists('small_logo', $iManufacturerId, $sFileName);     	
    } 
    
    public function isListImageExists($iManufacturerId, $sFileName)
    {
        return $this->_isFileExists('list_image', $iManufacturerId, $sFileName);    	
    }
    
    public function deletePictures($iId)  
    {
    	$this->load($iId);
    	if($this->getImage())
    		@unlink(Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . $this->getImage());
    	if($this->getSmallLogo())
    		@unlink(Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'logo' . DS . $this->getSmallLogo());
    	if($this->getListImage())
    		@unlink(Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'list' . DS . $this->getListImage());
    }
    
    protected function _isFileExists($sField, $iManufacturerId, $sFileName)
    {
    	$resource = $this->getResource();
        $select = $resource->getReadConnection()->select();
        
        $select->from($resource->getMainTable(), 'count(*)')
            ->where('id != ?', $iManufacturerId)
            ->where($sField.' = ?', $sFileName);

        return $resource->getReadConnection()->fetchOne($select);    
    }
    
    public function fillOut($storeId = 0)
    {
        Mage::register('aitmanufacturers_fillout_inprogress', true);
        //$stores = array_keys(Mage::app()->getStores(true));
        $resource = $this->getResource();
        $select = $resource->getReadConnection()->select();
        $select->from(array('main_table' => $resource->getMainTable()), array('manufacturer_id', 'url_key'))
            ->distinct(true)
            ->join(
                array('stores_table' => $resource->getTable('aitmanufacturers/aitmanufacturers_stores')),
                'main_table.manufacturer_id = stores_table.manufacturer_id'
                //array('store_id')
            )
            ->where('stores_table.store_id = ?', $storeId);
        //print_r($select->__toString());exit;
        $array = $resource->getReadConnection()->fetchPairs($select);
        $optionIds = array_keys($array);
        $urlKeys = array_values($array);
        
        
        $manufacturersCollection = $this->getCollection()->getManufacturersCollection($optionIds, $storeId);
        //print_r($manufacturersCollection->getItems());exit;
        $showBriefIcons = Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_brief_image', $storeId);
        $showListIcons = Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_list_image', $storeId);
        foreach ($manufacturersCollection as $manufacturer){
            $urlKey = Mage::helper('aitmanufacturers')->toUrlKey($manufacturer->getValue());
            while (in_array($urlKey, $urlKeys)){
                $urlKey .= rand(0, 99);
            }
            $urlKeys[] = $urlKey;
            $this->load(0);
            $data = array(
                'manufacturer_id' => $manufacturer->getOptionId(),
                'title' => $manufacturer->getValue(),
                'url_key' => $urlKey,
                'status' => 1,
            	'show_brief_image' => $showBriefIcons,
            	'show_list_image' => $showListIcons,
                'stores' => array($storeId),
            );
            $this->setData($data);
            $this->_afterLoad();
            $this->save();
        }
        Mage::unregister('aitmanufacturers_fillout_inprogress');
    }
}
