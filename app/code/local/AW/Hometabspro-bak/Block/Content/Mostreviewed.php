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
class AW_Hometabspro_Block_Content_Mostreviewed extends AW_Hometabspro_Block_Content
{		
	protected function _prepareCollection()
	{
            $collection = parent::_prepareCollection();
            /* Most reviewed */
            $reviewSummary = $this->getTableName('review/review_aggregate');
            $where =  Mage::getSingleton('core/session')->getHometabsProMostReviewed();

             $collection->getSelect()
                    ->join(array('summary'=>$reviewSummary), "summary.entity_pk_value = e.entity_id" , array())
                    ->where("e.entity_id IN (?)",$where)
                    ->group('e.entity_id')
                    ->order('summary.reviews_count DESC');

            return $collection;
	}	
}