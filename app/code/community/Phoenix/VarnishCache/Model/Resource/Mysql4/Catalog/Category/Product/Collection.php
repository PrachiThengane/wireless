<?php
/**
 * PageCache powered by Varnish
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@phoenix-media.eu so we can send you a copy immediately.
 * 
 * @category   Phoenix
 * @package    Phoenix_VarnishCache
 * @copyright  Copyright (c) 2011-2013 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Phoenix_VarnishCache_Model_Resource_Mysql4_Catalog_Category_Product_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize resource model and define main table
     */
    protected function _construct()
    {
        $this->_init('varnishcache/catalog_category_product');
    }

    /**
     * Filter collection by product ids
     *
     * @param array $productIds
     * @return Phoenix_VarnishCache_Model_Resource_Mysql4_Catalog_Category_Product_Collection
     */
    public function filterAllByProductIds(array $productIds)
    {
        $this->getSelect()
            ->where('product_id in (?)', $productIds)
            ->group('category_id');
        return $this;
    }
}
