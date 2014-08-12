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
class AW_Points_Model_Total_Creditmemo_Points extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getBaseMoneyForPoints() && $order->getMoneyForPoints())
        {
            $moneyBaseToReduce = $order->getBaseMoneyForPoints();
            $moneyToReduce = $order->getMoneyForPoints();

            if ($creditmemo->getBaseGrandTotal() + $moneyBaseToReduce < 0)
            {
                $creditmemo->setGrandTotal(0);
                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setMoneyForPoints($creditmemo->getGrandTotal());
                $creditmemo->setBaseMoneyForPoints($creditmemo->getBaseGrandTotal());
            }
            else
            {
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $moneyToReduce);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $moneyBaseToReduce);
                $creditmemo->setMoneyForPoints($moneyToReduce);
                $creditmemo->setBaseMoneyForPoints($moneyBaseToReduce);
            }
        }
        return $this;
    }
}
