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
 * Frequently Bought Together Cart Model
 */
class AW_Boughttogether_Model_Cart extends Mage_Checkout_Model_Cart
{
    /**
     * Check array of products for Composite Product Type
     * @param array $productIds array of product's id
     * @return boolean
     */
    protected function _isSomebodyComposite( $productIds )
    {
        foreach ( $productIds as $productId )
        {
            $product = $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ( $product && ($product->isComposite() || $this->_hasRequiredOptions($product)) )
            {
                return true;
            }
        }
        return false;
    }

    protected function _hasRequiredOptions(Mage_Catalog_Model_Product $product)
    {
        if ($options = $product->getProductOptionsCollection()){
            foreach ($options as $option){
                if ($option->getIsRequire()){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get array of product ids.
     * If product not need option, then it will go to cart with success mmessage.
     * If product need to option's selection print message about it.
     * 
     * @param arrai|int  $productIds
     * @return AW_Boughttogether_Model_Cart
     */
    public function addProductsByIds($productIds)
    {
	if ( $productIds && is_array( $productIds ) )			
	{
	    if ($this->_isSomebodyComposite( $productIds ))
	    {
            try
            {
                foreach ($productIds as $productId)
                {
                    $product = Mage::getModel('catalog/product')
                            ->setStoreId(Mage::app()->getStore()->getId())
                            ->load($productId);
                    if ( isset($product) && !($product->isComposite() || $this->_hasRequiredOptions($product)) && $product->isSaleable() )
                    {
                        $this->addProduct($product);
                        $message = Mage::helper('checkout')->__('%s was successfully added to your shopping cart.', $product->getName() );
                        $this->getCheckoutSession()->addSuccess( $message );
                    }
                    elseif( isset($product) && ($product->isComposite() || $this->_hasRequiredOptions($product)) && $product->isSaleable()   )
                    {
                        $link = '<a href="'.Mage::getUrl('catalog/product/view', array( 'id'=>$productId ) ).'">'.$product->getName().'</a>';
                        $error = Mage::helper('boughttogether')->__('Product %s can\'t be added to cart. Please specify the product option(s) first.', $link );
                        $this->getCheckoutSession()->addError( $error );
                    }
                }
            }
            catch (Exception $e)
            {
                $this->getCheckoutSession()->addException($e, $e->getMessage());
            }
	    }
	    else
	    {
            parent::addProductsByIds($productIds);
	    }    	    	    	    	   
	}
        return $this;
    }    
}





