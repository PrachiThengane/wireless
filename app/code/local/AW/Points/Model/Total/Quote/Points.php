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
 * @package    AW_Points
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Points_Model_Total_Quote_Points extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('points');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        $session = Mage::getSingleton('checkout/session');        
        $is_customer_logedIn = (bool) $quote->getCustomer()->getId();
                
        if ($session->getData('use_points') && $address->getBaseGrandTotal() && $is_customer_logedIn)
        {
            $pointsAmountUsed = abs($session->getData('points_amount'));
            $session->setData('points_amount',$pointsAmountUsed);
                       
            $pointsAmountAllowed = Mage::getModel('points/summary')
                                                        ->loadByCustomer($quote->getCustomer())
                                                        ->getPoints();
            
            if ($pointsAmountUsed > $pointsAmountAllowed) {
                $pointsAmountUsed = $pointsAmountAllowed;
            }            
            
            $rate = Mage::getModel('points/rate')->loadByDirection(AW_Points_Model_Rate::POINTS_TO_CURRENCY);

            $moneyBaseCurrencyForPoints = $rate->exchange($pointsAmountUsed);
            $moneyCurrentCurrencyForPoints = Mage::app()->getStore()->convertPrice($moneyBaseCurrencyForPoints);

            $baseSubtotalWithDiscount = $address->getData('base_subtotal') + $address->getData('base_discount_amount');
            $subtotalWithDiscount = $address->getData('subtotal') + $address->getData('discount_amount');
            /* If points amount is more then needed to pay for subtotal with disccount for order, we need to set new points amount */
            if ($moneyBaseCurrencyForPoints >= $baseSubtotalWithDiscount)
            {
                $neededAmount = ceil($baseSubtotalWithDiscount * $rate->getPoints()/$rate->getMoney());
                $neededAmountBaseCurrency = $rate->exchange($neededAmount);
                $neededAmountCurrentCurrency = Mage::app()->getStore()->convertPrice($neededAmountBaseCurrency);
                $session->setData('points_amount', $neededAmount);
                $address->setGrandTotal($address->getData('grand_total') - $subtotalWithDiscount);
                $address->setBaseGrandTotal($address->getData('base_grand_total') - $baseSubtotalWithDiscount);
                $address->setMoneyForPoints($neededAmountCurrentCurrency);
                $address->setBaseMoneyForPoints($neededAmountBaseCurrency);
                $quote->setMoneyForPoints($neededAmountCurrentCurrency);
                $quote->setBaseMoneyForPoints($neededAmountBaseCurrency);
            }
            else
            {
                $address->setGrandTotal($address->getGrandTotal() - $moneyCurrentCurrencyForPoints);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $moneyBaseCurrencyForPoints);
                $address->setMoneyForPoints($moneyCurrentCurrencyForPoints);
                $address->setBaseMoneyForPoints($moneyBaseCurrencyForPoints);
                $quote->setMoneyForPoints($moneyCurrentCurrencyForPoints);
                $quote->setBaseMoneyForPoints($moneyBaseCurrencyForPoints);
            }
        }
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $session = Mage::getSingleton('checkout/session');
        $quote = $address->getQuote();
        
        if ($address->getMoneyForPoints()) {
            $description = $session->getData('points_amount');
            $moneyForPoints = $address->getMoneyForPoints();
            
            $textForPoints = Mage::helper('points/config')->getPointUnitName();
            if ($description) {
                $title = Mage::helper('sales')->__('%s (%s)', $textForPoints, $description);
            } else {
                $title = Mage::helper('sales')->__('%s', $textForPoints);
            }
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => -$moneyForPoints
            ));
        }
        return $this;
    }
}
