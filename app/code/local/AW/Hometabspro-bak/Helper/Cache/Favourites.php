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
 
class AW_Hometabspro_Helper_Cache_Favourites extends AW_Hometabspro_Helper_Cache
{

            protected $_productT;
     
		public function __construct() {
                    
			 parent::__construct();	
			 $this->_initVars();
		}
		
		 
		public function setCache($conf) {
			 
                $limit = $conf['limit'];

                $selectedCategories = $this->_getSelectedCategories();
                $selectData = $this->_getSelectData();               
			 
                    $select = $this->_connection->select()
                        ->from(array('products'=>$this->_productT), array('entity_id'))
                        ->join(array('category'=>$this->_categoryT), "category.product_id = products.entity_id",array())
                        ->where('category_id IN (?)',$selectedCategories)
                        ->group('products.entity_id')
                        ->limit($limit);
					
                    if($selectData["table"] !== NULL) {
                        $sortTable = $this->_resource->getTableName($selectData["table"]);
                        $select =  $select
                         ->joinLeft(array('sort'=>$sortTable), "sort.{$selectData['column']} = products.entity_id",$selectData["selectData"]);
                    }

                    if($selectData["order"] !== NULL) {
                       $createdAt = trim($selectData['order']);
                        $select = $select
                        ->order("{$createdAt} {$this->_direction}");
                    }
		 
                    $select = parent::_joinBase($select,array("key"=>"products.entity_id"));

                    $where = array_unique($this->_connection->fetchCol($select));
                    $this->_session->setHometabsproFavourites($where);
                    $this->_session->setHometabsproFavouritesLimit($limit);
		 
		}
		
		private function _initVars() {
		
                    $this->_productT = $this->_resource->getTableName('catalog/product');
                    $this->_categoryT = $this->_resource->getTableName('catalog/category_product');

                    $this->_sortOrder = Mage::helper('hometabspro')->confSorting();
                    $this->_direction = Mage::helper('hometabspro')->confDirection();
				 
		}
		
		private function _getSelectedCategories() {
					
                        $categories = Mage::helper('hometabspro')->confCategories();
			$categories = preg_replace("#(^,|,$)#is","",preg_replace("#\D*,\D*#is",",",$categories));		 
			return explode(",",$categories); 
		 
		}
		
		private function _getSelectData() {
		 
			$table = $selectData = $order = $column = NULL;
		 
			$sort = Mage::helper('hometabspro')->confSorting();
			
			 switch($sort){
                             case 'name':
                                    $table = "{$this->_productT}_varchar";
                                    $selectData = array('value');
                                    $order = 'value ';
                            break;

                            case 'price':
                                if(Mage::helper('hometabspro')->checkVersion('1.4.0.0')) {
                                    $table =  $this->_resource->getTableName('catalog/product_index_price');
                                    $selectData = array('final_price','min_price');
                                    $order = 'IFNULL(final_price, min_price) ';
                                }
                            else {
                                if(Mage::helper('hometabspro')->checkVersion('1.3.2.4')){
                                    $table = $this->_resource->getTableName('catalogrule/rule_product_price');
                                    $selectData = array('rule_price');
                                    $order = 'rule_price ';
                                    $column = 'product_id';
                                }
                            }
                         break;

                        case 'created':
                            $order = 'products.created_at ';
                        break;

                      }
		
			$data = array(			
				"table" => $table,
				"selectData" => $selectData,
				"order" => $order,
				"column" => ($column === NULL)?'entity_id':$column
			);
		 
			return $data;
		
		}
 
}