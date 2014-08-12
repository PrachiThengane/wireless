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
class AW_Hometabspro_Block_Content_Topsellers extends AW_Hometabspro_Block_Content
{		

	protected function _prepareCollection()
	{
		$collection = parent::_prepareCollection();
		$orderItems = $this->getTableName('sales/order_item');
		$orderMain = $this->getTableName('sales/order');


        $where =  Mage::getSingleton('core/session')->getHometabsProSellers();


			if(version_compare(Mage::getVersion(),"1.3.3.0","<=")) {

					$varChar = $this->getTableName('sales/order')."_varchar";

						$collection->getSelect()
                            ->join(array('items'=>$orderItems), "items.product_id = e.entity_id", array('count'=>'SUM(items.qty_ordered)'))
							->join(array('trus'=>$orderMain),"items.order_id = trus.entity_id",array())
							->join(array('statusle' => $varChar),'items.order_id = statusle.entity_id',array())
							->where('statusle.value = ?', 'complete')
                            ->where("e.entity_id IN (?)",$where)
                            ->group('e.entity_id')
                            ->order('count DESC');

			}

			else {

				$collection->getSelect()
                            ->join(array('items'=>$orderItems), "items.product_id = e.entity_id", array('count'=>'SUM(items.qty_ordered)'))
							->join(array('trus'=>$orderMain),"items.order_id = trus.entity_id",array())
							->where('trus.status = ?', 'complete')
                            ->where("e.entity_id IN (?)",$where)
                            ->group('e.entity_id')
                            ->order('count DESC');
				}


            return $collection;
	}
}
