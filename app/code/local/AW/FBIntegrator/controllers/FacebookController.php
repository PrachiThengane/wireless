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
 * @package    AW_FBIntegrator
 * @version    2.1.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_FBIntegrator_FacebookController extends Mage_Core_Controller_Front_Action {

    public function checkappAction() {
        return $this->getResponse()->setBody(Mage::helper('fbintegrator')->checkApp($this->getRequest()->getParam('app_id'), $this->getRequest()->getParam('app_secret')));
    }

    public function connectAction() {
        $me = NULL;
        $facebook = new AW_FBIntegrator_Model_Facebook_Api(Mage::helper('fbintegrator')->getAppConfig());

        $access_token = $this->getRequest()->getParam('accessToken');
        if ($access_token) {
            Mage::getSingleton('customer/session')->setFBIAccessToken($access_token);
            $facebook->setAccessToken($access_token);
        } else {
            $access_token = Mage::getSingleton('customer/session')->getFBIAccessToken();
            if ($access_token) {
                $facebook->setAccessToken($access_token);
            }
        }

        $session = $facebook->getUser();
        if ($session) {
            try {
                $me = $facebook->api('/me');
            } catch (Exception $exc) {
                
            }
        }
        if ($me && isset($me['email'])) {

            $redirectToDashboard = Mage::getStoreConfigFlag('customer/startup/redirect_dashboard');

            $fbUser = Mage::getModel('fbintegrator/users')->getUser($me['email']);
            if ($fbUser->getCustomerId()) {
                $customer = Mage::getModel('customer/customer')->load($fbUser->getCustomerId());
                Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                return ($redirectToDashboard) ? $this->_redirect('customer/account') : $this->_redirectReferer();
            } else {
                //new FBI customer
                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
                $customer->loadByEmail($me['email']);

                if ($customer->getId()) {
                    // store customer was before, new FBI customer
                    $session = Mage::getSingleton('customer/session');
                    try {
                        Mage::getModel('fbintegrator/users')->createUser($me['id'], $customer->getEmail(), $customer->getId());
                    } catch (Exception $exc) {
                        //echo $exc->getTraceAsString();
                    }
                    $session->setCustomerAsLoggedIn($customer);
                    return ($redirectToDashboard) ? $this->_redirect('customer/account') : $this->_redirectReferer();
                } else {
                    // new store customer, new FBI customer
                    if (Mage::helper('fbintegrator')->getCountRequiredFields() && !$this->getRequest()->getParam('from-required-form')) {
                        return $this->getResponse()->setRedirect(Mage::helper('fbintegrator')->getRequiredFormUrl());
                    }
                    $gender = '';
                    $isGenderAvailiable = Mage::getResourceSingleton('customer/customer')->getAttribute('gender');
                    if ($isGenderAvailiable) {
                        $gender = (isset($me['gender']) && $me['gender'] == 'female') ? 'female' : 'male';
                        foreach (Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getAllOptions() as $v) {
                            if (strtolower($v['label']) == strtolower($gender))
                                $gender = $v['value'];
                        }
                    }

                    $pass = uniqid();
                    $data = array(
                        'firstname' => $me['first_name'],
                        'lastname' => $me['last_name'],
                        'email' => $me['email'],
                        'gender' => $gender,
                        'password' => $pass,
                        'confirmation' => $pass,
                        'dob' => date('Y-m-d', strtotime(isset($me['birthday']) ? $me['birthday'] : '0000-00-00')),
                        'prefix' => $this->getRequest()->getParam('prefix'),
                        'suffix' => $this->getRequest()->getParam('suffix'),
                        'taxvat' => $this->getRequest()->getParam('taxvat'),
                    );

                    $session = Mage::getSingleton('customer/session');
                    try {
                        $customer = Mage::getModel('fbintegrator/customer')->registerCustomer($me['id'], $data);
                        if ($customer->getId()) {
                            Mage::getModel('fbintegrator/users')->createUser($me['id'], $me['email'], $customer->getId());
                            $customer->sendNewAccountEmail('registered');
                            $session->addSuccess(Mage::helper('fbintegrator')->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName()));
                            $session->setCustomerAsLoggedIn($customer);
                        }
                    } catch (Exception $exc) {
                        $session->addError(Mage::helper('fbintegrator')->__('Error during registering with %s.', Mage::app()->getStore()->getFrontendName()));
                        //echo $exc->getTraceAsString();
                        return $this->_redirectReferer();
                    }
                    return ($redirectToDashboard) ? $this->_redirect('customer/account') : $this->_redirectReferer();
                }
            }
        } else {
            //no data from FB
        }
        return $this->_redirectReferer();
    }

    public function formAction() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('fbintegrator/fb', 'fb_required_form', array('template' => 'fbintegrator/fb_required_form.phtml'));
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

}
