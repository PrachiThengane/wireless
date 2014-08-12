<?php
	
	class Mec_Navigation_Helper_Compare extends Mage_Catalog_Helper_Product_Compare{
		
		public function getEncodedUrl($url=null)
	    {
	        if (!$url) {
	            $url = $this->getCurrentUrl();
	        }
	        
	        $url = str_replace('ajax=1&', '', $url);
	        $url = str_replace('ajax=1', '', $url);
	        
	        return $this->urlEncode($url);
	    }
	    
	}
