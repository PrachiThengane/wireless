<?php

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

/**
 * @property Ess_M2ePro_Model_Play_Order_Item $item
 */
class Ess_M2ePro_Model_Play_Order_Item_Proxy extends Ess_M2ePro_Model_Order_Item_Proxy
{
    // ########################################

    public function getOriginalPrice()
    {
        return $this->item->getPrice();
    }

    public function getOriginalQty()
    {
        return $this->item->getQtyPurchased();
    }

    public function getAdditionalData()
    {
        if (count($this->additionalData) == 0) {
            $this->additionalData[Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER]['items'][] = array(
                'order_item_id' => $this->item->getPlayOrderItemId()
            );
        }
        return $this->additionalData;
    }

    // ########################################
}