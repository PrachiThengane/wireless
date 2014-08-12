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
 * Frequently Bought Together Observer
 */
class AW_Boughttogether_Model_Observer
{
    const EMPTY_TEMPLATE = 'boughttogether/empty.phtml';

    /**
     * Array with object names to clear
     * @var array
     */
    protected $_clear = array(
        'Enterprise_TargetRule_Block_Catalog_Product_List_Related',
        'Mage_Catalog_Block_Product_List_Related'
    );



    /**
     * Event observer set empty template to Related Product blocks
     * @param Mage_Core_Model_Observer $observer
     */
    public function unsetRelatedProductsBlock($observer)
    {
        if (Mage::helper('boughttogether')->checkVersion('1.4.1.0')){
            if (Mage::helper('boughttogether')->isDisabled()){
                return;
            }
            $block = $observer->getEvent()->getBlock();
            if (is_object($block)){
                $className = get_class($block);
                if (in_array($className, $this->_clear)){
                    $name = $block->getNameInLayout();
                    $alias = $block->getBlockAlias();
                    $newBlock = $block->getLayout()->createBlock('boughttogether/empty')->setBlockAlias($alias);
                    $block->getLayout()->setBlock($name, $newBlock);
                }
            }
        }
    }



}
