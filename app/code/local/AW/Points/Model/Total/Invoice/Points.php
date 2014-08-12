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
class AW_Points_Model_Total_Invoice_Points extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    const MINIMUM_POSSIBLE_ERROR = 0.000000000001;

    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getBaseMoneyForPoints() && $order->getMoneyForPoints())
        {
            $moneyBaseToReduce = $order->getBaseMoneyForPoints();
            $moneyToReduce = $order->getMoneyForPoints();

            $invoiceGrandTotal = 0;
            $invoiceBaseGrandTotal = 0;
            $moneyForPoints = 0;
            $moneyBaseForPoints = 0;

            foreach ($invoice->getAllItems() as $item)
            {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemQty = $orderItem->getQtyOrdered();

                if ($orderItemQty)
                {
                    $itemToSubtotalMultiplier = $item->getData('base_row_total')/$invoice->getOrder()->getBaseSubtotal();
                    $moneyBaseToReduceItem = $moneyBaseToReduce * $itemToSubtotalMultiplier;
                    $moneyToReduceItem = $moneyToReduce * $itemToSubtotalMultiplier;


                    if (($item->getData('base_row_total') + $moneyBaseToReduceItem) < self::MINIMUM_POSSIBLE_ERROR)
                    {
                        $invoice->setMoneyForPoints($invoice->getMoneyForPoints() + $item->getData('row_total'));
                        $invoice->setBaseMoneyForPoints($invoice->getBaseMoneyForPoints() + $item->getData('base_row_total'));
                    }
                    else
                    {
                        $invoice->setGrandTotal($invoice->getGrandTotal() + $moneyToReduceItem);
                        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $moneyBaseToReduceItem);
                        $invoice->setMoneyForPoints($moneyToReduceItem + $invoice->getMoneyForPoints());
                        $invoice->setBaseMoneyForPoints($moneyBaseToReduceItem + $invoice->getBaseMoneyForPoints());
                    }
                }
            }
        }
        return $this;
    }
}
