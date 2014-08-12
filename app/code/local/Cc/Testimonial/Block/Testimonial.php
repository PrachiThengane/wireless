<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * This extension is only for developers as a technology exchange
 * Based on EasyTestimonial_v1.5.8 by mage-world.com
 * Fixed the bug that when compilation has been enabled, the testimonial tab in the backend will be blank page.
 *****************************************************
 * @category   Cc
 * @package    Cc_Testimonial
 * @Author     Chimy
 */
?>
<?php
class Cc_Testimonial_Block_Testimonial extends Mage_Core_Block_Template
{

    public function getTestimonials()     
    {
    	if (!Mage::app()->isSingleStoreMode()) {
		    $collection = Mage::getModel('testimonial/testimonial')->getCollection()
		    ->addStoreFilter(Mage::app()->getStore()->getId())
		        ->setOrder('created_time', 'desc');
    	}else{
    		$collection = Mage::getModel('testimonial/testimonial')->getCollection()->setOrder('created_time','desc');
    	}

	    $route = Mage::helper('testimonial')->getRoute();
	    Mage::getSingleton('testimonial/status')->addEnabledFilterToCollection($collection);
	    
	    //$collection->setPageSize(Mage::getStoreConfig('cc_testimonial/general/num_testimonial') ? Mage::getStoreConfig('cc_testimonial/general/num_testimonial'):5);
	    
	    foreach ($collection as $item) 
	    {
	        $tempAddress = $item->getWebsite();
	    	if($tempAddress != ''){
			   if(substr($tempAddress,0,4)=='http'){
				  $item->setWebsite($tempAddress);        	
			   }
			   else{
				  $item->setWebsite('http://'.$tempAddress);
			    }
		    }
		 
		    $maxword =  utf8_decode(Mage::getStoreConfig('cc_testimonial/general/maxword'));	
	        $item->setCreatedTime($this->formatTime($item->getCreatedTime(),'d-m-y', true));
	        $item->setUpdateTime($this->formatTime($item->getUpdateTime(),'d-m-y', true));
	        $content = $item->getDescription();
	        $content = $this->closetags($content);
			if($maxword>0){
	        $item->setPostContent(Mage::getModel('testimonial/testimonial')->word_trim($content,$maxword,true));
			}else{
				$item->setPostContent($item->getDescription());
			}
	        
	        
	    }
	    return $collection;
 
        
    }
    
    public function getRecents()     
    {
    	if (!Mage::app()->isSingleStoreMode()) {
		    $collection = Mage::getModel('testimonial/testimonial')->getCollection()
		    ->addStoreFilter(Mage::app()->getStore()->getId())
		        ->setOrder('created_time', 'desc');
    	}else{
    		$collection = Mage::getModel('testimonial/testimonial')->getCollection()->setOrder('created_time','desc');
    	}

	    $route = Mage::helper('testimonial')->getRoute();
	    Mage::getSingleton('testimonial/status')->addEnabledFilterToCollection($collection);
	    
	    //$collection->setPageSize(Mage::getStoreConfig('cc_testimonial/general/num_testimonial') ? Mage::getStoreConfig('cc_testimonial/general/num_testimonial'):5);
      $total = Mage::getStoreConfig('cc_testimonial/general/total'); 
      if (!$total) $total = 5; 
	    $collection->setPageSize($total);
      	    
	    foreach ($collection as $item) 
	    {
	        $tempAddress = $item->getWebsite();
	    	if($tempAddress != ''){
			   if(substr($tempAddress,0,4)=='http'){
				  $item->setWebsite($tempAddress);        	
			   }
			   else{
				  $item->setWebsite('http://'.$tempAddress);
			    }
		    }
		 
		    $maxword =  utf8_decode(Mage::getStoreConfig('cc_testimonial/general/maxword'));	
	        $item->setCreatedTime($this->formatTime($item->getCreatedTime(),'d-m-y', true));
	        $item->setUpdateTime($this->formatTime($item->getUpdateTime(),'d-m-y', true));
	        $content = $item->getDescription();
	        $content = $this->closetags($content);
			if($maxword>0){
	        $item->setPostContent(Mage::getModel('testimonial/testimonial')->word_trim($content,$maxword,true));
			}else{
				$item->setPostContent($item->getDescription());
			}
	        
	        
	    }
	    return $collection;
 
        
    }
    
    public function closetags($html){
        return Mage::helper('testimonial/data')->closetags($html);
    }
    
    public function getMediaUrl($media){
    	if($media){
    		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$media;
    	}
    }
    
    public function getWidthMedia(){
    	return (Mage::getStoreConfig('cc_testimonial/general/width')) ? (Mage::getStoreConfig('cc_testimonial/general/width')):200;
    }
    
    public function getHeightMedia(){
    	return (Mage::getStoreConfig('cc_testimonial/general/width')) ? (Mage::getStoreConfig('cc_testimonial/general/height')):200;
    }
}
