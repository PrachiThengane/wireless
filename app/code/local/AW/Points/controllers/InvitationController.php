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
 */class AW_Points_InvitationController extends Mage_Core_Controller_Front_Action {

    /**
     * Check customer authentication
     */
    public function preDispatch() {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();

        if (Mage::helper('points/config')->isReferalSystemEnabled() && Mage::helper('points/config')->isPointsEnabled()) {

            if ($action != 'createAccount') {
                $loginUrl = Mage::helper('customer')->getLoginUrl();

                if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                }
            }
        } else {
            $this->_redirect('customer/account/');
        }
    }

    public function indexAction() {

        $this->loadLayout();
        $this->_initPage();
        $block = $this->getLayout()->getBlock('points.invitation.list');
        if ($block)
            $block->setRefererUrl($this->_getRefererUrl());


        $this->getLayout()->getBlock('head')->setTitle($this->__('My Invitation'));
        $this->renderLayout();
    }

    public function createAccountAction() {

        Mage::getModel('points/invitation')->validateSecureCode($this->getRequest()->getParam('invitation'));
        $this->_redirect(Mage::helper('points')->getPageToRedirect());
    }

    public function sendInvitationAction() {

        $postData = $this->getRequest()->getPost();
        if ($postData) {

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $store = Mage::app()->getStore();

            $sessionData = $this->_getWrapped($postData, $customer, $store);
            $emails = $sessionData->getEmails();

            $sessionData->unsetData('emails');

            foreach ($emails as $email) {

                $sessionData->setData('email', $email);

                try {

                    $registeredCustomer = Mage::getModel('customer/customer')
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->loadByEmail($email);

                    if ($registeredCustomer->getId())
                        Mage::throwException(AW_Points_Model_Invitation::REGISTERED_CUSTOMER);

                    $preparedInvitation = Mage::getModel('points/invitation')->getSavedInvitation($sessionData);

                    if ($preparedInvitation->getStatus() == AW_Points_Model_Invitation::INVITATION_NEW)
                        $sentSuccessfully = $preparedInvitation->sendEmail($email, $store, $customer);

                    if ($sentSuccessfully) {
                        Mage::getSingleton('customer/session')->addSuccess(Mage::helper('points')->__('Invitation for %s has been sent.', $email));
                    } else {
                        Mage::getSingleton('customer/session')->addNotice(Mage::helper('points')->__('Invitation for %s is not sent. Please try later.', $email));
                    }
                } catch (Exception $e) {

                    if ($e->getMessage() == AW_Points_Model_Invitation::INVITEE_EXIST) {

                        Mage::getSingleton('customer/session')->addError(Mage::helper('points')->__('Invitation for email: %s has already been  registered', $email));
                    } elseif ($e->getMessage() == AW_Points_Model_Invitation::REGISTERED_CUSTOMER) {

                        Mage::getSingleton('customer/session')->addError(Mage::helper('points')->__('Customer with email: %s has already been  registered', $email));
                    } else {

                        Mage::getSingleton('customer/session')->addError($e->getMessage());

                        Mage::helper('awcore/logger')->log($this, Mage::helper('points')->__('Error on saving invitation data for email: %s', $email), AW_Core_Model_Logger::LOG_SEVERITY_ERROR, $e->getTraceAsString());
                    }
                }
            }
            return $this->_redirect('points/invitation/');
        }

        $this->loadLayout();
        $this->_initPage();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Send Invitations'));
        $this->renderLayout();
    }

    private function _initPage() {

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock)
            $navigationBlock->setActive('points/invitation');
    }

    /**
     * Wraps post, customer, store data into Varien_Object
     *
     * @return Varien_Object
     */
    private function _getWrapped($postData, $customer, $store) {

        $objToReturn = new Varien_Object;

        $storeId = $store->getStoreId();
        $message = isset($postData['message']) ? $postData['message'] : '';

        $emailAddresses = $postData['email'];

        foreach ($emailAddresses as $key => $emailAddress) {
            if (empty($emailAddress) || !Zend_Validate::is($emailAddress, 'EmailAddress'))
                unset($emailAddresses[$key]);
        }

        $objToReturn->setData(array(
            'emails' => $emailAddresses,
            'customer_id' => $customer->getEntityId(),
            'message' => $message,
            'store_id' => $storeId
        ));

        return $objToReturn;
    }

}