<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Feed
*/
class Amasty_Feed_MainController extends Mage_Core_Controller_Front_Action
{
    
    protected function _getHistory(){
        $ret = NULL;
        $historyId = $this->getRequest()->id;
        $key = $this->getRequest()->key;
        
        $history = Mage::getModel('amacart/history')->load($historyId);
        
        if ($history->getId() && $history->getPublicKey() == $key){
            $ret = $history;
        }
        
        return $ret;
    }
    
    
    public function customAction(){
        $history = $this->_getHistory();
        
        $target = $this->getRequest()->target;
        
        if ($history && $target){
            $target = base64_decode($target);
            $schedule = Mage::getModel('amacart/schedule')->load($history->getScheduleId());
            $schedule->clickByLink($history);
            Mage::app()->getFrontController()->getResponse()->setRedirect($target);
        } else {
            $this->_redirect("/");
        }
        
        
    }
            
    public function orderAction()
    {
        $history = $this->_getHistory();
        
        if ($history){
            
            $s = Mage::getSingleton('customer/session');
            if ($s->isLoggedIn()){
                if ($history->getCustomerId() != $s->getCustomerId()){
                    $s->logout();
                }                   
            }
            
            // customer. login
            if ($history->getCustomerId()){
                $customer = Mage::getModel('customer/customer')->load($history->getCustomerId());
                if ($customer->getId())
                    $s->setCustomerAsLoggedIn($customer);
            }
            elseif ($history->getQuoteId()){
                //visitor. restore quote in the session
                $quote = Mage::getModel('sales/quote')->load($history->getQuoteId());
                if ($quote){
                    Mage::getSingleton('checkout/session')->replaceQuote($quote); 
                }

            }

            $schedule = Mage::getModel('amacart/schedule')->load($history->getScheduleId());
            $schedule->clickByLink($history);
        }
        
        $this->_redirect('checkout/cart');
    }
    
    public function unsubscribeAction()
    {
        $history = $this->_getHistory();
        if ($history){
            $schedule = Mage::getModel('amacart/schedule')->load($history->getScheduleId());
            $schedule->unsubscribe($history);
            
            Mage::getSingleton('catalog/session')->addSuccess(Mage::helper('amacart')->__('You have been unsubscribed'));
        }
        
        $this->_redirect('checkout/cart');
    }
    
    public function emailAction()
    {
        $value = $this->getRequest()->value;
        
        $quote = Mage::getModel('checkout/cart')->getQuote();
        if ($quote->getId()){
            $quote2email = Mage::getModel('amacart/quote2email')->load($quote->getId(), 'quote_id');
            
            $quote2email->setData(array(
                'quote2email_id' => $quote2email->getId(),
                'quote_id' => $quote->getId(),
                'email' => $value
            ));
            
            $quote2email->save();
        }
    }
    
    public function downloadAction()
    {
        $fileName = $this->getRequest()->getParam('file');
        $download = Mage::helper('amfeed')->getDownloadPath('feeds', $fileName);
        if (file_exists($download)) {
            header('Content-Disposition: attachment; filename="' . $fileName . '"');               
            if(function_exists('mime_content_type')) {
                header('Content-Type: ' . mime_content_type($download));                    
            }
            else if(class_exists('finfo')) {
                 $finfo = new finfo(FILEINFO_MIME);
                 $mimetype = $finfo->file($download);
                 header('Content-Type: ' . $mimetype);
            }                
            readfile($download); 
        }
        exit;
    }
       
}

?>