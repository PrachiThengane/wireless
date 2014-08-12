<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Manufacturers extends Mage_Core_Block_Template
{
   
    public function getItems()
    {
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addStatusFilter()->addSortOrder();
            
        $c = 0;
        foreach ($collection as $item){
            
            $productIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductsByManufacturer($item->getManufacturerId(), Mage::app()->getStore()->getId());
            $productsNum = count($productIds);
            if (  ! (empty($productIds) && Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_brands_withproducts_only'))  )
            {
                // $manufacturer = $item->getManufacturer();
                $array[$item->getLetter()]['items'][] = array('item' => $item, 'number' => $productsNum);
                if (isset($array[$item->getLetter()]['count']))
                    $array[$item->getLetter()]['count']++;
                else
                    $array[$item->getLetter()]['count'] = 1;
                $c++;
            }
        }

        if (!isset($array)){
            return array();
        }
        
        $itemsPerColumn = ceil(($c + count($array)) / Mage::helper('aitmanufacturers')->getColumnsNum());        
        ksort($array);

        $col = 0;
        $c = 0;
        foreach ($array as $letter => $items){
            $a[$col][$letter]=$items['items'];
            $c += $items['count'];
            $c++;
            if ($c >= $itemsPerColumn){
                $c=0;
                $col++;
            }
        }
        return $a;
    }
    
    protected function _prepareLayout()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('manufacturers', array('label'=>Mage::helper('aitmanufacturers')->__('All Brands')));

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle(Mage::helper('aitmanufacturers')->__('All Brands'));
            //$head->setKeywords($page->getMetaKeywords());
            //$head->setDescription($page->getMetaDescription());
        }
    }
}
