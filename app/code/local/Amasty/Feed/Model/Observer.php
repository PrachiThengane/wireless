<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Observer
{
    /**
     * Run process generate product feeds
     *
     * @return Amasty_Feed_Model_Observer
     */
    public function process()
    {
        // set memory limit (Mb)
        //ini_set('memory_limit', Mage::getStoreConfig('amfeed/system/max_memory') . 'M');
        
        $message = '';
        $feeds = Mage::getModel('amfeed/profile')->getCollection();
        foreach ($feeds as $feed) {
            if (($feed->getMode()) && 
                ((Amasty_Feed_Model_Profile::STATE_READY == $feed->getStatus()) || (Amasty_Feed_Model_Profile::STATE_ERROR == $feed->getStatus())) &&
                ($this->_onSchedule($feed))) {
                
                $isCompleted = false;
                
                while (!$isCompleted) {
                    try {
                        $hasGenerated = $feed->generate();
                        $total = $feed->getInfoTotal();
                        
                        if (!$total) {
                            $message = Mage::helper('amfeed')->__('There are no products to export for feed `%s`', $feed->getTitle());
                            $isCompleted = true;
                        } elseif ($hasGenerated) {
                            $message = Mage::helper('amfeed')->__('The `%s` feed has been generated.', $feed->getTitle());
                            $isCompleted = true;
                        }
                    } catch (Exception $e) {
                        $message = Mage::helper('amfeed')->__('The `%s` feed generation has failed: %s', $feed->getTitle(), $e->getMessage());
                        $isCompleted = true;
                        $feed->setStatus(Amasty_Feed_Model_Profile::STATE_ERROR);
                        $feed->save();
                    }
                }
                echo $message;
            }
        }
        
        return $this;
    }
    
    protected function _onSchedule($feed)
    {
        $threshold = 24; // Daily
        switch ($feed->getMode()) {
            case '2': // Weekly
                $threshold = 168;
                break;
            case '3': // Monthly
                $threshold = 5040;
                break;
        }
        if ($threshold <= (strtotime('now') - strtotime($feed->getGeneratedAt()))/3600) {
            return true;
        }
        return false;
    }
    
    public function processConfigDataSave(Varien_Event_Observer $observer)
    {
        $configData = $observer->getEvent()->getConfigData();
        if ($configData->getPath() == 'amfeed/system/templates'){
            $fileContent = @file_get_contents($configData->getFilePath());
            if ($fileContent){
                
                $importObjects = unserialize($fileContent);
                
                if (is_array($importObjects) && count($importObjects) > 0){
                    $message = 'Following templates has been installed:';
                    
                    foreach($importObjects as $importObject){
                        $template = Mage::getModel('amfeed/template')->load($importObject['title'], 'title');
                        
                        if (!$template->getId()){
                            unset($importObject['feed_id']);
                        }
                        
                        $template->setData($importObject);
                        if ($template->save()){
                            
                            $message .= '<br/> - '.$template->getTitle();
                        }
                    }
                    Mage::getSingleton('core/session')->addSuccess($message);
                }
            }
        }
    } 
}