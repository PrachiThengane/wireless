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
class  AW_Hometabspro_Block_Content_Favouritetab extends AW_Hometabspro_Block_Content {

    
	protected function _prepareCollection() {
            
                $collection = parent::_prepareCollection();

                $where =  Mage::getSingleton('core/session')->getHometabsproFavourites();
                $limit =  Mage::getSingleton('core/session')->getHometabsFavouritesLimit();
                $sort = Mage::helper('hometabspro')->confSorting();
                $direction = Mage::helper('hometabspro')->confDirection();
                $column = 'entity_id';

             
            $catTable = $this->getTableName('catalog/category_product');
             
            $collection->getSelect()
                    ->joinRight(array('category'=>$catTable), "category.product_id = e.entity_id",array('category_id'))
                    ->where("e.entity_id IN (?)",$where)
                    ->group('e.entity_id')
                    ->limit($limit);
            
                    switch($sort){
                        case 'name':
                                $table = $this->getTableName('catalog/product')."_varchar";
                                $selectData = array('value');
                                $order = 'value ';
                            break;
                        case 'price':
                                if(Mage::helper('hometabspro')->checkVersion('1.4.0.0'))
                                {
                                $table = $this->getTableName('catalog/product_index_price');
                                $selectData = array('final_price','min_price');
                                $order = 'IFNULL(final_price, min_price) ';
                                }
                                else{
                                    if(Mage::helper('hometabspro')->checkVersion('1.3.2.4')){
                                        $table = $this->getTableName('catalogrule/rule_product_price');
                                        $selectData = array('rule_price');
                                        $order = 'rule_price ';
                                        $column = 'product_id';
                                    }
                                }
                            break;
                        case 'created':
                                $order = 'e.created_at ';
                            break;

                    }

                    if(isset($table)){
                        $sortTable = $this->getTableName($table);
                        $collection->getSelect()
                            ->joinLeft(array('sort'=>$sortTable), "sort.".$column." = e.entity_id",$selectData);
                    }
                    if(isset($order)){
                    $collection->getSelect()
                        ->order($order.$direction);

                    }

		return $collection;
	}

}

