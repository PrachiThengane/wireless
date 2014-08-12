<?php
/**
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Model_Mysql4_Aitmanufacturers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the manufacturer_id refers to the key field in your database table.
        $this->_init('aitmanufacturers/aitmanufacturers', 'id');
        $this->_optionValueTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');
    }
    
    
	/**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {


        if (!$this->getIsUniqueManufacturerToStores($object)) {
            Mage::throwException(Mage::helper('aitmanufacturers')->__('Brand Page for specified store already exists'));
        }
        
        if (!$this->getIsUniqueUrlKeyToStores($object)) {
            Mage::throwException(Mage::helper('aitmanufacturers')->__('URL Key for specified store already exists'));
        }
        
        
        if($object->getImage() && $object->getData('image_'))
        {
        	if($object->getImage() != $object->getData('image_'))
        	{
        		$path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . $object->getData('image_');
        		@unlink($path);
        	}
        }
        elseif (!$object->getImage() && $object->getData('image_')){
            $object->setImage($object->getData('image_'));
        }
        
        if ($object->getFeatured()){
            if (null == $object->getImage() && $object->getId()){
                $select = $this->_getReadAdapter()->select()->from($this->getMainTable(), array('image'))
                    ->where('id = ?', $object->getId());
                if (!$this->_getReadAdapter()->fetchOne($select)){
                    Mage::throwException(Mage::helper('aitmanufacturers')->__('Image must be uploaded for Featured Brand'));
                }
            }
            elseif (null == $object->getImage()){
                Mage::throwException(Mage::helper('aitmanufacturers')->__('Image must be uploaded for Featured Brand'));
            }
        }
        
        
        if($object->getSmallLogo() && $object->getData('small_logo_'))
        {
        	if($object->getSmallLogo() != $object->getData('small_logo_'))
        	{
        		$path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'logo'. DS. $object->getData('small_logo_');
        		@unlink($path);
        	}
        }
        elseif (!$object->getSmallLogo() && $object->getData('small_logo_')){
            $object->setSmallLogo($object->getData('small_logo_'));
        }
        
    	if($object->getListImage() && $object->getData('list_image_'))
        {
        	if($object->getListImage() != $object->getData('list_image_'))
        	{
        		$path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'list'. DS. $object->getData('list_image_');
        		@unlink($path);
        	}
        }
        elseif (!$object->getListImage() && $object->getData('list_image_')){
            $object->setListImage($object->getData('list_image_'));
        }

		if ($object->getCreatedTime() == NULL || $object->getUpdateTime() == NULL) {
			$object->setCreatedTime(now())
				->setUpdateTime(now());
		} else {
			$object->setUpdateTime(now());
		}

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
    
    
    

    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $stores = (array)$object->getData('stores');
        if (Mage::registry('aitmanufacturers_update_get_stores') AND $object->getId())
        {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('aitmanufacturers/aitmanufacturers_stores'), 'store_id')
                ->where('id = ?', $object->getId())
                ->where('manufacturer_id = ?', $object->getManufacturerId())
                ;
            $stores = $this->_getReadAdapter()->fetchCol($select);
        }
        if (!empty($stores))
        {
            $condition = $this->_getWriteAdapter()->quoteInto('manufacturer_id = ? AND ', $object->getManufacturerId())
                       . $this->_getWriteAdapter()->quoteInto('store_id IN (?)', $stores);
            $this->_getWriteAdapter()->delete($this->getTable('aitmanufacturers/aitmanufacturers_stores'), $condition);
            
            foreach ($stores as $store) {
                $storeArray = array();
                $storeArray['id'] = $object->getId();
                $storeArray['manufacturer_id'] = $object->getManufacturerId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('aitmanufacturers/aitmanufacturers_stores'), $storeArray);
            }
        }
        
        if (!Mage::registry('aitmanufacturers_fillout_inprogress'))
        {
            if ($logo = $object->getData('small_logo'))
            {

                $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'logo' . DS;
                if (file_exists($path.$logo))
                {
                	if(Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_rename_pic') &&
                		$object->getData('small_logo') != $object->getData('small_logo_'))
                    	$newlogo = $object->getId().'_'.$logo;
                    else 
                    	$newlogo = $logo;
                    	
                    if ($logo != $newlogo
                    ){
                        if (file_exists($path.$newlogo))
                        {
                            unlink($path.$newlogo);
                        }
                        
                        rename($path.$logo, $path.$newlogo);
                        $this->_getWriteAdapter()->update($this->getMainTable(), array('small_logo'=>$newlogo), $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $object->getId()));
                    }
                }
            }
            
            if ($image = $object->getData('image'))
            {
                $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS;
                if (file_exists($path.$image))
                {
                	if(Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_rename_pic') &&
                		$object->getData('image') != $object->getData('image_'))
                    	$newimage = $object->getId().'_'.$image;
                    else 
                    	$newimage = $image;
                    	
                    if ($image != $newimage)
                    {
                        if (file_exists($path.$newimage))
                        {
                            unlink($path.$newimage);
                        }
                        
                        rename($path.$image, $path.$newimage);
                        $this->_getWriteAdapter()->update($this->getMainTable(), array('image'=>$newimage), $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $object->getId()));
                    }
                }
            }
            
        	if ($listimage = $object->getData('list_image'))
            {
                $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'list' . DS;
                if (file_exists($path.$listimage))
                {


                	if(Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_rename_pic')&&
                		$object->getData('list_image') != $object->getData('list_image_'))
                    	$newlistimage = $object->getId().'_'.$listimage;
                    else 
                    	$newlistimage = $listimage;
                    	
                    if ($listimage != $newlistimage)
                    {
                        if (file_exists($path.$newlistimage))
                        {
                            unlink($path.$newlistimage);
                        }
                        
                        rename($path.$listimage, $path.$newlistimage);
                        $this->_getWriteAdapter()->update($this->getMainTable(), array('list_image'=>$newlistimage), $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $object->getId()));
                    }
                }
            }
        }
        /**
        * @desc adding url key to core/url_rewrite
        */
        if (!empty($stores))
        {
            foreach ($stores as $storeId)
            {
                $rewriteModel = Mage::getModel('core/url_rewrite');
                $rewriteCollection = $rewriteModel->getCollection();
                $rewriteCollection->addStoreFilter($storeId, false)
                                  ->addFieldToFilter('target_path', 'brands/index/view/id/' . $object->getId())
                                  ->setPageSize(1)
                                  ->load();
                if (count($rewriteCollection) > 0)
                {
                    foreach ($rewriteCollection as $rewrite) 
                    {
                        $rewriteModel->setData($rewrite->getData());
                    }
                }
                $rewritePattern = Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_url_pattern', $storeId);
                
                $rewriteModel->setData('store_id', $storeId);
                $rewriteModel->setData('request_path', str_replace('[brand]',$object->getUrlKey(),$rewritePattern));
                $rewriteModel->setData('id_path', 'brands/' . $object->getId());
                $rewriteModel->setData('target_path', 'brands/index/view/id/' . $object->getId());
                
                $cmsPageModel = Mage::getModel('cms/page');
                                
                $routes = array();
                $routesScope = array('frontend','admin');
                foreach ($routesScope as $scope)
                {
                    $routersGlobal = Mage::getConfig()->getNode($scope.'/routers')->asArray();
                    foreach ($routersGlobal as $routerLocal)
                    {
                        if(!in_array($routerLocal['args']['frontName'], $routes))
                        {
                            $routes[] = $routerLocal['args']['frontName'];
                        }
                    }
                }
                
                // in fillout should rename, but in brand edit should show an error
                if (true === Mage::registry('aitmanufacturers_fillout_inprogress'))
                {
                    $bError = false;
                    $iAttempt = 0;
                    do
                    {
                        try
                        {
                            if (in_array($rewriteModel->getData('request_path'), $routes) 
                            OR $cmsPageModel->checkIdentifier($rewriteModel->getData('request_path'),$storeId))
                            {
                                Mage::throwException("This URL pattern is not allowed!");
                            }
                            else
                            {
                                $rewriteModel->save();
                            }
                        } 
                        catch (Exception $e)
                        {
                            $iAttempt++;
                            $rewriteModel->setData('request_path', str_replace('[brand]',$object->getUrlKey() . '-' . $iAttempt ,$rewritePattern));
                            $bError = true;
                            continue;
                        }
                        
                        if ($iAttempt > 0)
                        {
                            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('aitmanufacturers')->__('"%s" was renamed to "%s" as a category with the same request path already exists', $object->getUrlKey(), $rewriteModel->getData('request_path')));
                            $object->setUrlKey($object->getUrlKey() . '-' . $iAttempt);
                            $object->save();
                        }
                        
                        $bError = false;
                    }
                    while ($bError && $iAttempt < 31);
                } 
                else 
                {
                    try
                    {
                        if (in_array($rewriteModel->getData('request_path'), $routes) 
                            OR $cmsPageModel->checkIdentifier($rewriteModel->getData('request_path'),$storeId))
                        {
                           Mage::throwException("This URL key is not allowed!");
                        }
                        else
                        {
                            $rewriteModel->save();
                        }
                    } 
                    catch (Exception $e)
                    {
                        Mage::throwException(Mage::helper('aitmanufacturers')->__('Request path %s is already used. Please select some other url key.', $rewriteModel->getData('request_path')));
                    }
                }
            }
        }
        
        
        return parent::_afterSave($object);
    }
    
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $storeId = Mage::app()->getStore()->getId();
        
        $select->join(array('store_default_value'=>$this->_optionValueTable),
                'store_default_value.option_id='.$this->getMainTable().'.manufacturer_id',
                array('default_value'=>'value'))
            ->joinLeft(array('store_value'=>$this->_optionValueTable),
                'store_value.option_id='.$this->getMainTable().'.manufacturer_id AND '.$this->getReadConnection()->quoteInto('store_value.store_id=?', $storeId),
                array('store_value'=>'value',
                'manufacturer' => new Zend_Db_Expr('IFNULL(store_value.value,store_default_value.value)')))
            ->where($this->getReadConnection()->quoteInto('store_default_value.store_id=?', 0));
        return $select;
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('aitmanufacturers/aitmanufacturers_stores'))
            ->where('manufacturer_id = ?', $object->getManufacturerId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }
    
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);
        
        $rewrites = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('id_path', 'brands/' . $object->getId());
        foreach($rewrites->getItems() as $item)
        {
            $item->delete();
        }
        return $this;
    }
    
    public function getIsUniqueManufacturerToStores(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->join(array('ms' => $this->getTable('aitmanufacturers/aitmanufacturers_stores')), $this->getMainTable().'.id = `ms`.id')
                ->where($this->getMainTable().'.manufacturer_id = ?', $object->getData('manufacturer_id'));
        if ($object->getId()) {
            $select->where($this->getMainTable().'.id <> ?',$object->getId());
        }
        
        $stores = (array)$object->getData('stores');
        /*if (in_array('0', $stores)){
            $select->where('`ms`.store_id <> 0 OR `ms`.store_id IN (?)', $stores);
        }
        else {
            $select->where('`ms`.store_id IN (?) OR `ms`.store_id = 0', $stores);
        }*/
        $select->where('`ms`.store_id IN (?)', $stores);
        //print_r($select->__toString());exit;
        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }
    
    public function getIsUniqueUrlKeyToStores(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->join(array('ms' => $this->getTable('aitmanufacturers/aitmanufacturers_stores')), $this->getMainTable().'.id = `ms`.id')
                ->where($this->getMainTable().'.url_key = ?', $object->getData('url_key'));
        if ($object->getId()) {
            $select->where($this->getMainTable().'.id <> ?',$object->getId());
        }
        
        $stores = (array)$object->getData('stores');
        /*if (in_array('0', $stores)){
            $select->where('`ms`.store_id <> 0 OR `ms`.store_id IN (?)', $stores);
        }
        else {
            $select->where('`ms`.store_id IN (?) OR `ms`.store_id = 0', $stores);
        }*/
        $select->where('`ms`.store_id IN (?)', $stores);

        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }
    
    public function getAttributeOptionValue($optionId)
    {
        $storeId = Mage::app()->getStore()->getId();
        
        $select = $this->_getReadAdapter()->select()
            ->from($this->_optionValueTable, 'value')
            ->where('option_id = ?', $optionId)
            ->where('store_id IN (?)', array(0, $storeId))
            ->order('store_id DESC')
            ->limit(1);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    public function checkUrlKey($urlKey, $storeId)
    {
        $select = $this->_getReadAdapter()->select()->from(array('main_table'=>$this->getMainTable()), 'id')
            ->join(
                array('ms' => $this->getTable('aitmanufacturers/aitmanufacturers_stores')),
                'main_table.id = `ms`.id'
            )
            ->where('main_table.url_key=?', $urlKey);
            if (Mage::helper('aitmanufacturers')->getAttributeCode(0) != Mage::helper('aitmanufacturers')->getAttributeCode($storeId)){
                $select->where('main_table.status=1 AND `ms`.store_id = ?', $storeId);
            }
            else {
                $select->where('main_table.status=1 AND `ms`.store_id in (0, ?)', $storeId);
            }
            $select->order('store_id DESC');

        return $this->_getReadAdapter()->fetchOne($select);
    }
}
