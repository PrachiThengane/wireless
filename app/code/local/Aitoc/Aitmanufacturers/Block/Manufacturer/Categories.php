<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Manufacturer_Categories extends Mage_Core_Block_Template
{
    
    protected $_manufacturer;
    
    protected $_displayCategoryIds = array();
    
    protected $_treeCategoriesList = array();
    
    public function __construct()
    {
        parent::__construct();
        $manufacturers = Mage::registry('aitmanufacturers_manufacturers');
        if (isset($manufacturers[$this->getRequest()->getParam('id')])){
            $this->_manufacturer = $manufacturers[$this->getRequest()->getParam('id')];
        }
        else {
            $this->_manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($this->getRequest()->getParam('id'))
                ->getManufacturerId();
            $manufacturers[$this->getRequest()->getParam('id')] = $this->_manufacturer;
            Mage::register('aitmanufacturers_manufacturers', $manufacturers);
        }
    }
    
    protected function _getCategoryIds()
    {
        $storeCategoryIds = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId())->getAllChildren(true);
        $productIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductsByManufacturer($this->_manufacturer, Mage::app()->getStore()->getId());
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->addIdFilter($productIds);
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
        $categoryIds = array();
        foreach ($productCollection as $product){
            $categoryIds = array_merge($categoryIds, $product->getCategoryIds());
        }
        $categoryIds = array_intersect($storeCategoryIds, $categoryIds);
        return $categoryIds;
    }

    public function getCategories()
    {
        $categoryIds = $this->_getCategoryIds();
        
        $catCollection = Mage::getResourceModel('catalog/category_collection');
        $catCollection
            ->addAttributeToSelect('name', 'left')
            ->addIdFilter($categoryIds)
            ->setProductStoreId(Mage::app()->getStore()->getId())
            ->setStoreId(Mage::app()->getStore()->getId())
            //->addIsActiveFilter()
            ->addAttributeToFilter('is_active', 1)
            ->joinUrlRewrite();
            
        $catCollection->getSelect()->where('parent_id > 1'); // to get rid of default category            
        
        $catCollection->load();

        return $catCollection;
    }
    
    public function getCategoryTreeList()
    {
        $categoryIds = $this->_getCategoryIds();

        $tree = Mage::getBlockSingleton('aitmanufacturers/manufacturer_categories_tree')->getTree();
        $this->_convertTreeToFlat($tree, $categoryIds);
        return $this->_treeCategoriesList;
    }
    
    protected function _convertTreeToFlat($tree, $categoryIds)
    {
        $this->_detectDisplayCategories($tree, $categoryIds);
        $this->_buildTreeList($tree, $categoryIds);
    }
    
    protected function _buildTreeList($tree, $categoryIds)
    {
        foreach ($tree as $category)
        {
            if (in_array($category['id'], $this->_displayCategoryIds))
            {
                $cat = $category;
                $cat['is_link'] = in_array($category['id'], $categoryIds);
                unset($cat['children']);
                $this->_treeCategoriesList[] = $cat;
            }
            if (isset($category['children']) && !empty($category['children']))
            {
                $this->_buildTreeList($category['children'], $categoryIds);
            }
        }
    }
    
    protected function _detectDisplayCategories($tree, $categoryIds)
    {
        foreach ($tree as $category)
        {
            if (in_array($category['id'], $categoryIds))
            {
                foreach (explode('/', $category['path']) as $pathCategoryId)
                {
                    $this->addDisplayCategoryId($pathCategoryId);
                }
            }
            if (isset($category['children']) && !empty($category['children']))
            {
                $this->_detectDisplayCategories($category['children'], $categoryIds);
            }
        }
    }
    
    public function addDisplayCategoryId($id)
    {
        if (!in_array($id, $this->_displayCategoryIds))
        {
            $this->_displayCategoryIds[] = $id;
        }
    }
    
    
    public function getCategoryUrl($category)
    {
        return $category->getUrl().'?'.Mage::helper('aitmanufacturers')->getAttributeCode().'='.$this->_manufacturer;
    }
}
