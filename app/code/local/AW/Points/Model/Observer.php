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
 */class AW_Points_Model_Observer extends Mage_Core_Block_Abstract {

    protected static $_moduleDisabledChanged = false;
    protected static $_customerNotSet = true;

    /**
     * sales_order_invoice_pay event.
     * @param Object $observer
     */
    public function invoicePay($observer) {
        $invoice = $observer->getInvoice();
        if (!Mage::helper('points/config')->isPointsEnabled($invoice->getStoreId())
                || Mage::getStoreConfig('advanced/modules_disable_output/AW_Points', $invoice->getStoreId()))
            return $this;

        $this
                ->_addPointsForRules($observer)
                ->_addPointsAfterOrderInvoicing($observer)
                ->_addPointsAfterReferralOrderInvoicing($observer);
    }

    protected function _addPointsForRules($observer) {
        $order = $observer->getInvoice()->getOrder();

        $items = $order->getAllItems();

        foreach ($items as $item) {
            /* If partial invoice, return */
            if ($item->getData('qty_ordered') != $item->getData('qty_invoiced'))
                return $this;
        }

        $quote = Mage::getModel('sales/quote')->setSharedStoreIds(array($order->getStoreId()))->load($order->getQuoteId());

        /* If guest, return */
        if (!$quote->getCustomer() || !$quote->getCustomer()->getId())
            return $this;

        $ruleCollection = Mage::getModel('points/rule')
                ->getCollection()
                ->addAvailableFilter()
                ->addFilterByCustomerGroup($quote->getCustomer()->getGroupId())
                ->addFilterByWebsiteId($order->getStore()->getWebsiteId())
                ->setOrder('priority', Varien_Data_Collection::SORT_ORDER_ASC);
        foreach ($ruleCollection as $rule) {
            if ($rule->checkRule($quote)) {
                Mage::getModel('points/api')->addTransaction(
                        $rule->getPointsChange(), 'order_invoiced', $quote->getCustomer(), $order, array('order_increment_id' => $order->getIncrementId()), array('notice' => Mage::helper('points')->__('Rule #%s, %s', $rule->getId(), $rule->getName()))
                );
                if ($rule->getStopRules())
                    break;
            }
        }
        return $this;
    }

    protected function _addPointsAfterOrderInvoicing($observer) {
        $invoice = $observer->getInvoice();
        $order = $observer->getInvoice()->getOrder();

        /* If guest, return */
        if (!$order->getCustomerId())
            return $this;

        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $orderWebsite = $order->getStore()->getWebsite();

        try {
            /* $order->getBaseDiscountAmount() is negative for some Magento versions and positive for others */
            $invoiceDiscount = $invoice->getBaseDiscountAmount() < 0 ? $invoice->getBaseDiscountAmount() : -$invoice->getBaseDiscountAmount();
            $invoiceBaseMoneyForPoints = $invoice->getBaseMoneyForPoints() < 0 ? $invoice->getBaseMoneyForPoints() : -$invoice->getBaseMoneyForPoints();
            $invoiceMoneyAvailableToPointsConversion = $invoice->getBaseSubtotal() + $invoiceDiscount + $invoiceBaseMoneyForPoints;
            $pointsAmount = Mage::getModel('points/api')->changeMoneyToPoints($invoiceMoneyAvailableToPointsConversion, $customer, $orderWebsite);

            Mage::getModel('points/api')->addTransaction(
                    $pointsAmount, 'order_invoiced', $customer, $order, array('order_increment_id' => $order->getIncrementId())
            );
        } catch (Exception $ex) {
            Mage::helper('awcore/logger')->log($this, $ex->getMessage(), null, null, $ex->getLine());
        }
        return $this;
    }

    public function orderPlaceBefore($observer) {
        $session = Mage::getSingleton('checkout/session');
        if (!$session->getData('use_points') || !$session->getData('points_amount') || (int) $session->getData('points_amount') <= 0) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        if ($order->getCustomerIsGuest()) {
            return $this;
        }

        if ($order->getCustomerId()) {
            $pointsAmount = (int) $session->getData('points_amount');
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $customerPoints = Mage::getModel('points/summary')->loadByCustomer($customer)->getPoints();
            if ($customerPoints < $pointsAmount || !Mage::helper('points')->isAvailableToRedeem($customerPoints)) {
                Mage::throwException($this->__('Incorrect points amount'));
            }

            $amountToSubtract = -$pointsAmount;
            $moneyForPointsBase = Mage::getModel('points/api')->changePointsToMoney($amountToSubtract, $customer, $order->getStore()->getWebsite());
            $moneyForPoints = $order->getBaseCurrency()->convert($moneyForPointsBase, $order->getOrderCurrencyCode());
            $order->setAmountToSubtract($amountToSubtract);
            $order->setBaseMoneyForPoints($moneyForPointsBase);
            $order->setMoneyForPoints($moneyForPoints);
        }
    }

    public function orderPlaceAfter($observer) {
        $order = $observer->getEvent()->getOrder();
        if ($order->getAmountToSubtract()) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            Mage::getModel('points/api')->addTransaction(
                    $order->getAmountToSubtract(), 'spend_on_order', $customer, $order, array('order_increment_id' => $order->getIncrementId())
            );
        }
    }

    public function paypalPrepare($observer) {
        $session = Mage::getSingleton('checkout/session');

        if (Mage::helper('points')->magentoLess142()) {
            $salesEntity = $observer->getSalesEntity();
            $additional = $observer->getAdditional();
            if ($salesEntity && $additional) {
                $items = $additional->getItems();
                $items[] = new Varien_Object(array(
                            'name' => Mage::helper('points/config')->getPointUnitName(),
                            'qty' => 1,
                            'amount' => -1.00 * (abs((float) $salesEntity->getBaseMoneyForPoints())),
                        ));
                $additional->setItems($items);
            }
        } else {
            $paypalCart = $observer->getEvent()->getPaypalCart();
            if ($paypalCart && $paypalCart->getSalesEntity()->getBaseMoneyForPoints()) {
                $salesEntity = $paypalCart->getSalesEntity();
                $paypalCart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, abs((float) $salesEntity->getBaseMoneyForPoints()), Mage::helper('points/config')->getPointUnitName() . '(' . $session->getData('points_amount') . ')'
                );
            }
        }
    }

    protected function _addPointsInfo($objectToAdd, $order) {
        $transaction = Mage::getModel('points/transaction')->loadByOrder($order);
        $objectToAdd->setMoneyForPoints($transaction->getData('points_to_money'));
        $objectToAdd->setBaseMoneyForPoints($transaction->getData('base_points_to_money'));
    }

    public function orderLoadAfter($observer) {
        $order = $observer->getEvent()->getOrder();
        $this->_addPointsInfo($order, $order);
    }

    public function invoiceLoadAfter($observer) {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $this->_addPointsInfo($order, $order);
        if ($order->getBaseMoneyForPoints() && $order->getMoneyForPoints()) {
            $moneyBaseToReduce = $order->getBaseMoneyForPoints();
            $moneyToReduce = $order->getMoneyForPoints();

            $invoiceGrandTotal = 0;
            $invoiceBaseGrandTotal = 0;
            $moneyForPoints = 0;
            $moneyBaseForPoints = 0;

            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemQty = $orderItem->getQtyOrdered();

                if ($orderItemQty) {
                    $itemToSubtotalMultiplier = $item->getData('base_row_total') / $invoice->getOrder()->getBaseSubtotal();
                    $moneyBaseToReduceItem = $moneyBaseToReduce * $itemToSubtotalMultiplier;
                    $moneyToReduceItem = $moneyToReduce * $itemToSubtotalMultiplier;


                    if ($item->getData('base_row_total') + $moneyBaseToReduceItem < 0) {
                        $invoice->setMoneyForPoints($invoice->getMoneyForPoints() + $item->getData('row_total'));
                        $invoice->setBaseMoneyForPoints($invoice->getBaseMoneyForPoints() + $item->getData('base_row_total'));
                    } else {
                        $invoice->setMoneyForPoints($moneyToReduceItem + $invoice->getMoneyForPoints());
                        $invoice->setBaseMoneyForPoints($moneyBaseToReduceItem + $invoice->getBaseMoneyForPoints());
                    }
                }
            }
        }
    }

    public function creditmemoLoadAfter($observer) {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();
        $this->_addPointsInfo($creditmemo, $order);
    }

    public function paymentAddPoints(Varien_Event_Observer $observer) {
        $input = $observer->getEvent()->getInput();
        $session = Mage::getSingleton('checkout/session');

        $session->setData('use_points', $input->getData('use_points'));
        $session->setData('points_amount', $input->getData('points_amount'));

        if ($session->getData('use_points') && !$input->getData('method')) {
            $input->setMethod('free');
        }
        return $this;
    }

    protected function _isModuleDisabled($storeId) {

        if (!Mage::helper('points/config')->isPointsEnabled($storeId)
                || Mage::getStoreConfig('advanced/modules_disable_output/AW_Points', $storeId))
            return true;
    }

    protected function _findAffiliateForCustomer($customer) {

        $customerName = $customer->getFirstname() . " " . $customer->getLastname();
        $acceptedInvitation = Mage::getModel('points/invitation')->loadAcceptedBy($customer);
        $affiliateId = $acceptedInvitation->getCustomerId();
        $referralId = $customer->getId();

        if ($acceptedInvitation->getId() && $affiliateId != $referralId) {

            $acceptedInvitation->setCustomerAsReferral($customer);

            $pointsForInitation = Mage::helper('points/config')
                    ->getInvitationToRegistrationConversion();

            $affiliate = Mage::getModel('customer/customer')
                    ->load($acceptedInvitation->getCustomerId());

            Mage::getModel('points/api')->addTransaction(
                    $pointsForInitation, 'invitee_registered', $affiliate, $affiliate, array('referral_name' => $customerName)
            );
        }
    }

    // function observes customer save on frontend
    public function customerSaveAfter($observer) {

        if ($this->_isModuleDisabled(Mage::app()->getStore()->getStoreId()))
            return $this;

        $customer = $observer->getEvent()->getCustomer();

        $summary = Mage::getModel('points/summary')
                ->loadByCustomer($customer);

        /* Check if customer saved for the first time (is new) */
        if ($customer->getCreatedAt() == $customer->getUpdatedAt() && self::$_customerNotSet) {

            self::$_customerNotSet = false;

            $isSubscribedByDefault = Mage::helper('points/config')->getIsSubscribedByDefault();

            if ($isSubscribedByDefault) {

                $summary = Mage::getModel('points/summary')->loadByCustomer($customer);

                $summary
                        ->setBalanceUpdateNotification(1)
                        ->setPointsExpirationNotification(1)
                        //->setPointsForRegistrationGranted(1)
                        ->setUpdateDate(true)
                        ->save();
            }

            $pointsForRegistration = Mage::helper('points/config')
                    ->getPointsForRegistration();

            Mage::getModel('points/api')->addTransaction(
                    $pointsForRegistration, 'customer_register', $customer, $customer
            );
        }

        if (is_null($customer->getConfirmation()))
            $this->_findAffiliateForCustomer($customer);
    }

    // this function is calling by observer
    // to set up subscription for the customer created from backend
    public function subscribeForBalanceUpdate($observer) {

        $customer = $observer->getEvent()->getCustomer();

        if (!$customer->getOrigData()) {

            $isSubscribedByDefault = Mage::helper('points/config')->getIsSubscribedByDefault();

            if ($isSubscribedByDefault) {

                $summary = Mage::getModel('points/summary')->loadByCustomer($customer);

                $summary
                        ->setBalanceUpdateNotification(1)
                        ->setPointsExpirationNotification(1)
                        ->setUpdateDate(true)
                        ->save();
            }
        }
    }

    public function updatePointsFromCustomerEdit($observer) {

        if ($request = $observer->getRequest()) {

            $pointsToAdd = $request->getPost('aw_update_points');
            $comment = $request->getPost('aw_update_points_comment');
            $customer = $observer->getCustomer();

            Mage::getModel('points/api')->addTransaction(
                    $pointsToAdd, 'added_by_admin', $customer, null, array('comment' => $comment)
            );
        }
    }

    public function updatePointsNotificationFromCustomerEdit($observer) {

        if ($request = $observer->getRequest()) {

            $balanceUpdateNotification = (int) $request->getPost('balance_update_notification');
            $pointsExpireNotification = (int) $request->getPost('points_expire_notification');

            $summary = Mage::getModel('points/summary')
                    ->loadByCustomer(
                    $observer->getCustomer()
            );

            $summary
                    ->setBalanceUpdateNotification($balanceUpdateNotification)
                    ->setPointsExpirationNotification($pointsExpireNotification)
                    ->setUpdateDate(true)
                    ->save();
        }
    }

    protected function _addPointsAfterReferralOrderInvoicing($observer) {

        /* TODO: Work with invoice, not with order */
        $invoice = $observer->getInvoice();
        $order = $invoice->getOrder();

        $affiliate = Mage::getModel('points/invitation')
                ->loadByReferralId(
                $order->getCustomerId()
        );

        if ($affiliateId = $affiliate->getCustomerId()) {

            $pointsForOrder = Mage::helper('points/config')->getPointsForOrder();

            if ($pointsForOrder == AW_Points_Helper_Config::FIRST_ORDER_ONLY) {
                if ($this->_isFirstOrderFor($order->getCustomerId()))
                    $this->_addTransactionThroughApi($affiliateId, $invoice);
            }elseif ($pointsForOrder == AW_Points_Helper_Config::EACH_ORDER) {
                $this->_addTransactionThroughApi($affiliateId, $invoice);
            }
        }
        return $this;
    }

    public function addPointsForReview($observer) {

        $review = $observer->getDataObject();
        $this->_processReviewObject($review);
    }

    /* Magento 1.3 stub. */

    protected function _addPointsForReview($observer) {

        $object = $observer->getObject();

        if (($review = $object) instanceof Mage_Review_Model_Review) {

            $this->_processReviewObject($review);
        }
    }

    /* Magento 1.3 stub ends */

    private function _processReviewObject($review) {

        if ($this->_isModuleDisabled($review->getStoreId()))
            return $this;

        $givePointsForReview = true;

        $oldStatusId = $review->getOrigData('status_id');
        $newStatusId = $review->getStatusId();
        $customerId = $review->getCustomerId();
        $productId = $review->getEntityPkValue();

        if (Mage::helper('points/config')->isForBuyersOnly($review->getStoreId())) {
            $givePointsForReview =
                    $this->_hasCustomerBoughtThisProduct($customerId, $productId);
        }

        if ($givePointsForReview &&
                $newStatusId == Mage_Review_Model_Review::STATUS_APPROVED &&
                $customerId &&
                $newStatusId != $oldStatusId) {

            $customer = Mage::getModel('customer/customer')
                    ->load($customerId);

            $pointsForReview = Mage::helper('points/config')
                    ->getPointsForReviewingProduct($review->getStoreId());

            $product = Mage::getModel('catalog/product')
                    ->load($productId);

            Mage::getModel('points/api')->addTransaction(
                    $pointsForReview, 'review_approved', $customer, $review, array('product_name' => $product->getName())
            );
        }
    }

    public function addPointsForSubscription($observer) {

        if ($this->_isModuleDisabled(Mage::app()->getStore()->getStoreId()))
            return $this;

        $currentSubscriber = $observer->getEvent()->getSubscriber();
        if (!$currentSubscriber->getCustomerId() || !$currentSubscriber->getIsStatusChanged())
            return;

        $customer = Mage::getModel('customer/customer')
                ->load($currentSubscriber->getCustomerId());

        $summary = Mage::getModel('points/summary')
                ->loadByCustomer($customer);

        if ($summary->getPointsForSubscriptionGranted() == 0 && $currentSubscriber->isSubscribed()) {

            $pointsForSubscription = Mage::helper('points/config')
                    ->getPointsForNewsletterSingup();

            Mage::getModel('points/api')->addTransaction(
                    $pointsForSubscription, 'customer_subscription', $customer, $currentSubscriber
            );

            $summary = Mage::getModel('points/summary')
                    ->loadByCustomer($customer);
            $summary
                    ->setPointsForSubscriptionGranted(1)
                    ->setUpdateDate(true)
                    ->save();
        }
    }

    public function addPointsForSubscriptionInAdminAdrea($observer) {
        if ($this->_isModuleDisabled(Mage::app()->getStore()->getStoreId()))
            return $this;

        $currentSubscriber = $observer->getEvent()->getSubscriber();
        if (!$currentSubscriber->getCustomerId() || !$currentSubscriber->getIsStatusChanged())
            return;

        $customer = Mage::getModel('customer/customer')
                ->load($currentSubscriber->getCustomerId());

        $summary = Mage::getModel('points/summary')
                ->loadByCustomer($customer);
        if (
                Mage::helper('points/config')->isConsiderNewsletterSignupByAdmin() &&
                ($summary->getPointsForSubscriptionGranted() == 0) &&
                $currentSubscriber->isSubscribed()
        ) {
            $summary
                    ->setPointsForSubscriptionGranted(1)
                    ->setUpdateDate(true)
                    ->save();
        }
        return;
    }

    public function modelSaveAfter($observer) {

        $this
                ->_addPointsForParticipateInPoll($observer)
                ->_addPointsForTagging($observer);

        /* Magento 1.3 stub. */
        if (Mage::helper('points')->magentoLess14()) {
            $this->_addPointsForReview($observer);
        }
        /* Magento 1.3 stub ends */
    }

    protected function _addPointsForParticipateInPoll($observer) {

        $object = $observer->getObject();

        if (($pollVote = $object) instanceof Mage_Poll_Model_Poll_Vote) {

            if ($this->_isModuleDisabled(Mage::app()->getStore()->getStoreId()))
                return $this;

            if ($customerId = $pollVote->getCustomerId()) {

                $customer = Mage::getModel('customer/customer')
                        ->load($customerId);

                $pointsForParticipateInPoll = Mage::helper('points/config')
                        ->getPointsForParticipatingInPoll();

                Mage::getModel('points/api')->addTransaction(
                        $pointsForParticipateInPoll, 'customer_participate_in_poll', $customer, null
                );
            }
        }
        return $this;
    }

    protected function _hasCustomerBoughtThisProduct($_customerId, $_productIdToVerify) {

        $result = false;
        $childrenIds = array();
        $groupedProductChildrenIds = array();
        $productIsGrouped = false;

        $collectionOfOrders = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToFilter('customer_id', $_customerId);

        $product = Mage::getModel('catalog/product')
                ->load($_productIdToVerify);

        if ($product->isGrouped()) {
            $productIsGrouped = true;
            $childrenIds = $product->getTypeInstance()->getChildrenIds($_productIdToVerify);
            $groupedProductChildrenIds = $childrenIds[Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED];
        }

        foreach ($collectionOfOrders as $order) {

            foreach ($order->getItemsCollection() as $item) {

                $masterStatus = $item->getStatusName(Mage_Sales_Model_Order_Item::STATUS_MIXED);

                //fix for virtual and downloadable products
                if ((bool) $item->getData("is_virtual")) {
                    $masterStatus = $item->getStatusName(Mage_Sales_Model_Order_Item::STATUS_INVOICED);
                }

                //fix for Grouped product
                if ($productIsGrouped) {
                    if (in_array($item->getProductId(), $groupedProductChildrenIds) && $item->getStatus() == $masterStatus) {
                        $result = true;
                    }
                }

                if ($item->getProductId() == $_productIdToVerify && $item->getStatus() == $masterStatus) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    protected function _addPointsForTagging($observer) {

        $object = $observer->getObject();
//getting points for existing tags (tag_relation)
        /*
          if (($tagRelation = $object) instanceof Mage_Tag_Model_Tag_Relation) {

          $tag = Mage::getModel('tag/tag')
          ->load($tagRelation->getTagId());

          if ($this->_isModuleDisabled($tagRelation->getStoreId()))
          return $this;

          if (!$tagRelation->getCustomerId()) return $this;

          $customer = Mage::getModel('customer/customer')
          ->load($tagRelation->getCustomerId());

          if ($tag->getStatus() &&
          $this->_isNotSetInSummary($customer, $tagRelation->getTagRelationId())) {

          $pointsForTagging = Mage::helper('points/config')
          ->getPointsForTaggingProduct();

          $product = Mage::getModel('catalog/product')
          ->load($tagRelation->getProductId());

          Mage::getModel('points/api')->addTransaction(
          $pointsForTagging,
          'customer_tag_product',
          $customer,
          $tagRelation,
          array('product_name' => $product->getName())
          );

          $this->_addRelationIdToSummary($customer, $tagRelation->getTagRelationId());
          }
          }
         */
        if (($tagToApprove = $object) instanceof Mage_Tag_Model_Tag) {

            if ($this->_isModuleDisabled($tagToApprove->getStoreId()))
                return $this;

            $tagCollection = Mage::getModel('tag/tag')
                    ->getCollection()
                    ->joinRel()
                    ->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED);

            $tagCollection
                    ->getSelect()
                    ->where('main_table.tag_id = ?', $tagToApprove->getTagId());

            foreach ($tagCollection->getData() as $tag) {

                $tagObject = new Varien_Object;
                unset($tag['tag_id']);
                $tagObject->setData($tag);

                $customer = Mage::getModel('customer/customer')
                        ->load($tagObject->getCustomerId());

                if ($this->_isNotSetInSummary($customer, $tagObject->getTagRelationId())) {

                    $pointsForTagging = Mage::helper('points/config')
                            ->getPointsForTaggingProduct();

                    $product = Mage::getModel('catalog/product')
                            ->load($tagObject->getProductId());

                    Mage::getModel('points/api')->addTransaction(
                            $pointsForTagging, 'customer_tag_product', $customer, $tagObject, array('product_name' => $product->getName())
                    );

                    $this->_addRelationIdToSummary($customer, $tagObject->getTagRelationId());
                }
            }
        }
    }

    private function _addRelationIdToSummary($customer, $tagRelationId) {

        $summary = Mage::getModel('points/summary')
                ->loadByCustomer($customer);

        $arrayOfTagRelationIds = explode(',', $summary->getPointsForTagsGranted());

        $arrayOfTagRelationIds[] = $tagRelationId;

        $string = implode(",", $arrayOfTagRelationIds);

        $summary
                ->setPointsForTagsGranted($string)
                ->setUpdateDate(true)
                ->save();
    }

    private function _isNotSetInSummary($customer, $tagRelationId) {
        $result = true;

        $summary = Mage::getModel('points/summary')
                ->loadByCustomer($customer);

        $arrayOfTagRelationIds = explode(',', $summary->getPointsForTagsGranted());

        if (in_array($tagRelationId, $arrayOfTagRelationIds)) {
            $result = false;
        }
        return $result;
    }

    private function _isFirstOrderFor($referralId) {

        $result = false;

        $collection = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter('customer_id', $referralId);
        $collection->getSelect()->where('total_paid = base_grand_total');

        if ($collection->getSize() < 1)
            $result = true;

        return $result;
    }

    private function _addTransactionThroughApi($inviterId, $invoice) {

        if ($points = $this->_getPointsForOrder($invoice)) {

            $affiliate = Mage::getModel('customer/customer')->load($inviterId);

            Mage::getModel('points/api')->addTransaction(
                    $points, 'order_invoiced_by_referral', $affiliate, $affiliate
            );
        }
    }

    private function _getPointsForOrder($invoice) {

        $order = $invoice->getOrder();
        $customer = Mage::getModel('customer/customer')->load($invoice->getOrder()->getCustomerId());
        $website = $order->getStore()->getWebsite();
        $pointsFromSubtotalPrecent = 0;
        $fixedPoints = 0;

        $totalPaid = $order->getData('total_paid');
        $baseGrandTotal = $order->getData('base_grand_total');

        // add  fixed count of points only for fully payed order
        if ($totalPaid == $baseGrandTotal) {
            $fixedPoints = (int) Mage::helper('points/config')->getFixedPointsForOrder();
        }
        $percentOf = (int) Mage::helper('points/config')->getPercentPointsForOrder();

        if ($percentOf > 0) {
            /* $order->getBaseDiscountAmount() is negative for some Magento versions and positive for others */
            $invoiceDiscount = $invoice->getBaseDiscountAmount() < 0 ? $invoice->getBaseDiscountAmount() : -$invoice->getBaseDiscountAmount();
            $invoiceBaseMoneyForPoints = $invoice->getBaseMoneyForPoints() < 0 ? $invoice->getBaseMoneyForPoints() : -$invoice->getBaseMoneyForPoints();
            $baseSubtotalWithoutDiscount = $invoice->getBaseSubtotal() + $invoiceDiscount + $invoiceBaseMoneyForPoints;
            $percentOfsubtotal = round(($percentOf / 100) * $baseSubtotalWithoutDiscount);

            try {

                $pointsFromSubtotalPrecent = Mage::getModel('points/api')->changeMoneyToPoints($percentOfsubtotal, $customer, $website);
            } catch (Exception $e) {
                Mage::helper('awcore/logger')->log($this, Mage::helper('points')->__('Unable to add points for invoice of order: %s', $invoice->getOrder()->getIncrementId()), AW_Core_Model_Logger::LOG_SEVERITY_ERROR, $e->getMessage());
            }
        }
        return $fixedPoints + $pointsFromSubtotalPrecent;
    }

    public function pageLoadBeforeFront($observer) {
        /* If extension disabled and output enabled */
        if (!Mage::helper('points/config')->isPointsEnabled() && !self::$_moduleDisabledChanged) {
            Mage::app()->getStore()->setConfig('advanced/modules_disable_output/AW_Points', true);
            self::$_moduleDisabledChanged = true;
        }

        /* If extension output enabled */
        if (!Mage::getStoreConfig('advanced/modules_disable_output/AW_Points')) {
            $node = Mage::getConfig()->getNode('global/blocks/checkout/rewrite');
            $dnode = Mage::getConfig()->getNode('global/blocks/checkout/drewrite/onepage_payment_methods');
            $node->appendChild($dnode);
        }

        /* Discard points amount if left the Checkout page */
        $req = $observer->getControllerAction()->getRequest();

        $contr = $req->getControllerModule();
        $contrName = $req->getControllerName();
        $module = $req->getModuleName();

        if (($contr == 'Mage_Checkout' && $contrName == 'cart') ||
                ($contr == 'Mage_Catalog' && $contrName == 'product') ||
                ($contr == 'Mage_Cms')
        ) {

            $session = Mage::getSingleton('checkout/session');
            $session->setData('use_points', null);
            $session->setData('points_amount', null);
        }
    }

    public function quoteDistroy($observer) {
        $session = Mage::getSingleton('checkout/session');
        $session->setData('use_points', null);
        $session->setData('points_amount', null);
    }

    public function pageLoadBeforeGlobal($observer) {
        /* If extension output enabled */
        if (!Mage::getStoreConfig('advanced/modules_disable_output/AW_Points')) {
            /* Magento 1.3 stub. Not used for Magento >= 1.4 */
            if (Mage::helper('points')->magentoLess14()) {
                $node = Mage::getConfig()->getNode('global/blocks/sales/rewrite');
                $dnodes = Mage::getConfig()->getNode('global/blocks/sales/drewrite');

                foreach ($dnodes->children() as $dnode) {
                    $node->appendChild($dnode);
                }

                $node1 = Mage::getConfig()->getNode('global/models/paypal/rewrite');
                $dnodes1 = Mage::getConfig()->getNode('global/models/paypal/drewrite');

                foreach ($dnodes1->children() as $dnode1) {
                    $node1->appendChild($dnode1);
                }
            }
            /* Magento 1.3 stub ends */
        }
    }

    public function subscriberModelDRewrite($observer) {

        /* Magento 1.3 stub. Not used for Magento >= 1.4 */
        if (Mage::helper('points')->magentoLess14()) {
            $node = Mage::getConfig()->getNode('global/models/newsletter/rewrite');
            $dnodes = Mage::getConfig()->getNode('global/models/newsletter/drewrite');

            foreach ($dnodes->children() as $dnode) {
                $node->appendChild($dnode);
            }
        }
        /* Magento 1.3 stub ends */
    }

    public function checkIfQuoteIsFree($observer) {
        $quote = $observer->getQuote();
        if ($quote->getData('grand_total') == 0) {
            $quote->removePayment();
            $quote->getPayment()->setMethod('free');
        }
    }

}

?>
