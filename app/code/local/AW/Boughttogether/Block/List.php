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
 * @package    AW_Boughttogether
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
/**
 * Frequently Bought Together List
 */

class AW_Boughttogether_Block_List extends Mage_Catalog_Block_Product_List_Related
{

	/**
     * Path to config
     */
    const CONFIG_LIMIT_PATH = 'boughttogether/general/limit';

    /**
     * Path to empty template
     */
    const TEMPLATE_EMPTY = 'boughttogether/empty.phtml';

    /**
     * Current ptoduct
     * @var Mage_Catalog_Model_Product
     */
	protected $_thisProduct;

    /**
     * Array with optional products
     * @var array
     */
	protected $_optionalProducts;

    public function disableRelated()
    {        
        if (Mage::helper('boughttogether')->checkVersion('1.4.1.0') || Mage::helper('boughttogether')->isDisabled()){
            return;
        }
        $block = $this->getLayout()->getBlock('catalog.product.related');
        if ($block){
            $block->setTemplate(self::TEMPLATE_EMPTY);
        }
    }

    /**
     * Prepare data to show
     * @return AW_Boughttogether_Block_List
     */
  	protected function _prepareData() {
		parent::_prepareData();
		$this->_thisProduct = $this->getProduct();
		$this->_optionalProducts = array();
		$__configLimit = intval(Mage::getStoreConfig(self::CONFIG_LIMIT_PATH));
		$__itemsTotal = 0;
		foreach ($this->_itemCollection as $_key => $_item) {
			if (!$_item->isSaleable()) {
				$this->_itemCollection->removeItemByKey($_key);
				continue;
			}
			if ($__configLimit) {
				$__itemsTotal += 1;
				if ($__itemsTotal >= $__configLimit) {
					$this->_itemCollection->removeItemByKey($_key);
				}
			}
		}
		return $this;
	}

    /**
     * Retrives block html code
     * @return string
     */
	protected function _beforeToHtml() {
		$this->_prepareData();
        $this->disableRelated();
		return parent::_beforeToHtml();
	}
}
