<?php
class Devinc_Groupdeals_Block_Product_View extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }     
	
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
			$storeId = Mage::app()->getStore()->getId();
            $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }	
    
    public function getGroupdeals()
    {
        return Mage::registry('groupdeals');
    }
	
	public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
	
    public function getDiscountPercent()
    {		
		$_product = $this->getProduct();
		$discount = ($_product->getPrice()-$_product->getSpecialPrice())*100/$_product->getPrice();
		
		return number_format($discount,0).'%';
    }	    
	
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance(true)->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }
	
    public function getCountdown($product_id = null, $countdown_id = 'countdown')
    {
		if (is_null($product_id)) {
			$_groupdeals = $this->getGroupdeals();
		} else {
			$_groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', $product_id)->getFirstItem();
		}
		$startDate = Mage::getModel('core/date')->date('Y-m-d H,i,s');
		$endDate = $_groupdeals->getDatetimeTo();
		$jsEndDate = date("m/d/Y g:i A", strtotime($_groupdeals->getDatetimeTo()));
		$jsStartDate = Mage::getModel('core/date')->date('m/d/Y g:i A');
		$countdown_type = Mage::getStoreConfig('groupdeals/configuration/countdown_type');
		//js configuration
		$js_bg_main = Mage::getStoreConfig('groupdeals/js_countdown_configuration/bg_main');
		$js_textcolor = Mage::getStoreConfig('groupdeals/js_countdown_configuration/textcolor');
		$js_days_text = Mage::getStoreConfig('groupdeals/js_countdown_configuration/days_text');
		
		//flash configuration		
		$display_days = Mage::getStoreConfig('groupdeals/countdown_configuration/display_days');
		$bg_main = str_replace('#','0x',Mage::getStoreConfig('groupdeals/countdown_configuration/bg_main'));
		$bg_color = str_replace('#','0x',Mage::getStoreConfig('groupdeals/countdown_configuration/bg_color'));
		$textcolor = str_replace('#','0x',Mage::getStoreConfig('groupdeals/countdown_configuration/textcolor'));
		$alpha = Mage::getStoreConfig('groupdeals/countdown_configuration/alpha');
		$sec_text = Mage::getStoreConfig('groupdeals/countdown_configuration/sec_text');
		$min_text = Mage::getStoreConfig('groupdeals/countdown_configuration/min_text');
		$hour_text = Mage::getStoreConfig('groupdeals/countdown_configuration/hour_text');
		$days_text = Mage::getStoreConfig('groupdeals/countdown_configuration/days_text');
		$smh_color = str_replace('#','0x',Mage::getStoreConfig('groupdeals/countdown_configuration/txt_color'));
			
		$core_date = Mage::getModel('core/date');
		$date1 = mktime($core_date->date('H'),$core_date->date('i'),$core_date->date('s'),$core_date->date('m'),$core_date->date('d'),$core_date->date('Y'));
	    $date2 = mktime(substr($_groupdeals->getDatetimeTo(),11,2),substr($_groupdeals->getDatetimeTo(),14,2),substr($_groupdeals->getDatetimeTo(),17,2),substr($_groupdeals->getDatetimeTo(),5,2),substr($_groupdeals->getDatetimeTo(),8,2),substr($_groupdeals->getDatetimeTo(),0,4));	   
		$dateDiff = $date2 - $date1;
		
		if ($display_days==1) {
			$fullDays = floor($dateDiff/(60*60*24));
			if ($fullDays<=0) {
				$source = $this->getSkinUrl('groupdeals/flash/countdown.swf');
				$height = '56px';
			} else {
				$source = $this->getSkinUrl('groupdeals/flash/countdown_days.swf');
				$height = '44px';
			} 
		} else {
			if ($dateDiff>0) {
				$diff = abs($dateDiff); 
				$years   = floor($diff / (365*60*60*24)); 
				$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
				
				$hours_left = $days*24+$hours;
				if ($hours_left<100) {
					$source = $this->getSkinUrl('groupdeals/flash/countdown_multiple_2.swf');			
					$height = '56px';
				} else {
					$source = $this->getSkinUrl('groupdeals/flash/countdown_multiple_3.swf');		
					$height = '50px';
				}
			} else {
				$source = $this->getSkinUrl('groupdeals/flash/countdown_multiple_2.swf');			
				$height = '56px';
			}
		}
		
		$_product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($_groupdeals->getProductId());
		if ($_product->isSaleable() && $_product->getGroupdealStatus()!=4) {
			$variables = base64_encode($startDate.'&&&'.$endDate.'&&&'.$alpha.'&&&'.$bg_color.'&&&'.$textcolor.'&&&'.$bg_main.'&&&'.$smh_color); 	
		} else {
			$variables = base64_encode($startDate.'&&&'.$startDate.'&&&'.$alpha.'&&&'.$bg_color.'&&&'.$textcolor.'&&&'.$bg_main.'&&&'.$smh_color); 
			$jsEndDate = date("m/d/Y g:i A", strtotime($_groupdeals->getDatetimeFrom()));	
		}
		$variables_smhd = $sec_text.'|||'.$min_text.'|||'.$hour_text.'|||'.$days_text; 	
		
		$variables_new = '';
		$i = 0;
		while (strlen($variables)>0) {
			if ($i%2==0) {
				$variables_new .= substr($variables,0,10).'dMD';
			} else {
				$variables_new .= substr($variables,0,10).'Dmd';					
			}
			$variables = substr($variables,10,1000);
			$i++;
		}
		
		$variables_new = substr($variables_new,0,-3);
		$html = '';
		if ($countdown_type==0) {
		$html .= 	'<div id="'.$countdown_id.'" style="padding:5px 0px 5px 0px;">';
		} else {
		$html .= 	'<div id="'.$countdown_id.'" style="padding:2px 0px 0px 0px;">';
		}
		$html .= 	'<script language="javascript">
						TargetDate = "'.$jsEndDate.'";
						NowDate = "'.$jsStartDate.'";
						BackColor = "'.$js_bg_main.'";
						ForeColor = "'.$js_textcolor.'";
						CountActive = true;
						CountStepper = -1;
						LeadingZero = true;
						DisplayFormat = "%%D%% '.$js_days_text.' %%H%%:%%M%%:%%S%%";
						FinishMessage = "00:00:00";
					</script>
					<script language="javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'groupdeals/countdown.js"></script>
					</div>';
			//$html .=	'<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>';
		
			
		if ($countdown_type==1) {   
		$html .= 	'<script type="text/javascript">		
					var so = new SWFObject("'.$source.'", "countdown_object", "100%", "'.$height.'", "9");
					so.addParam("menu", "false");
					so.addParam("salign", "MT");
					so.addParam("allowFullscreen", "true");	
					if (navigator.userAgent.indexOf("Opera") <= -1) {
						so.addParam("wmode", "opaque");		
					}
					so.addVariable("vs", "'.$variables_new.'");						
					so.addVariable("smhd", "'.$variables_smhd.'");						
					so.write("'.$countdown_id.'");
				 </script>';
		}
	
        return $html;
    }
	
    public function getSalesNumber($_groupdeals)
    {
		$sold_qty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($_groupdeals);	
		
        return $sold_qty;
    }	
	
    public function getTippingTime($_groupdeals)
    {
		$sales_collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('status', array('nin' => array('holded','closed','canceled','fraud')));
		$start_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($_groupdeals->getDatetimeFrom());
		$end_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($_groupdeals->getDatetimeTo());
		
		$tipping_time = '';		
		$target = $_groupdeals->getMinimumQty();
		$sold_qty = 0;
		
		foreach($sales_collection as $sale) {
			$sale_item_collection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('created_at', array("from" =>  $start_date_time, "to" =>  $end_date_time, "datetime" => true))->addFieldToFilter('product_id', $_groupdeals->getProductId())->addFieldToFilter('order_id', $sale->getId());	
			if (count($sale_item_collection)>0) {
				foreach($sale_item_collection as $item) {	
					$sold_qty = $sold_qty+(int)$item->getQtyOrdered();	
					if ($sold_qty>=$target) {					
						$tipping_time = Mage::getModel('core/date')->date('h:iA', strtotime($item->getCreatedAt()));
						break;
					}
				}
			}
		}	
		
        return $tipping_time;
    }	
        
	public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false, $displayIfNoReviews = false)
	{
		return $this->getLayout()->createBlock('review/helper')->getSummaryHtml($product, $templateType, $displayIfNoReviews);	
	}
	
	public function getSlideshowConfiguration()
	{
		$slideshow_effect = Mage::getStoreConfig('groupdeals/configuration/slideshow_effect');
		switch ($slideshow_effect) {
			case 0:
				$effect = "effect: 'slide',";
				break;
			case 1:
				$effect = "effect: 'fade',";
				break;		
			case 2:
				$effect = "effect: 'fade',
						   crossfade: true,";
		}
		
		$html = "<script type=\"text/javascript\">
					jQuery(function(){
						// Set starting slide to 1
						var startSlide = 1;
						// Get slide number if it exists
						if (window.location.hash) {
							startSlide = window.location.hash.replace('#','');
						}
						// Initialize Slides
						jQuery('#slides').slides({
							".$effect."
							preload: true,
							preloadImage: 'img/loading.gif',
							generatePagination: true,
							play: 5000,
							pause: 2500,
							hoverPause: true,
							// Get the starting slide
							start: startSlide,
							animationComplete: function(current){
								// Set the slide number as a hash
								window.location.hash = '#' + current;
							}
						});
					});
				</script>";
		
		return $html;
	}
	
    public function getMerchant($_groupdeals)
    {
        return Mage::getModel('groupdeals/merchants')->load($_groupdeals->getMerchantId());
    } 
    
    public function getMerchantName()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_name = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getName(), $storeId);
	
        return $merchant_name;
    } 
    
    public function getMerchantDescription()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_description = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getDescription(), $storeId);
	
        return $merchant_description;
    } 
    
    public function getBusinessHours()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_hours = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getBusinessHours(), $storeId);
	
        return $merchant_hours;
    } 
    
    public function getWebsite()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_website = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getWebsite(), $storeId);
	
        return $merchant_website;
    } 
    
    public function getFacebook()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_facebook = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getFacebook(), $storeId);
	
        return $merchant_facebook;
    } 
    
    public function getTwitter()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_twitter = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getTwitter(), $storeId);
	
        return $merchant_twitter;
    } 
    
    public function getEmail()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_email = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getEmail(), $storeId);
	
        return $merchant_email;
    } 
    
    public function getPhone()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_phone = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getPhone(), $storeId);
	
        return $merchant_phone;
    } 
    
    public function getMobile()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_mobile = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getMobile(), $storeId);
	
        return $merchant_mobile;
    } 
    
    public function getRedeem()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeals = $this->getGroupdeals();
		$_merchant = $this->getMerchant($_groupdeals);	
		
	    $merchant_redeem = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getRedeem(), $storeId);
	
        return $merchant_redeem;
    } 
    
    public function getAddressCollection($_merchant)
    {
		if ($_merchant->getAddress()!='') {
			$addresses = explode('_;_',$_merchant->getAddress());			
			return $addresses;
		} else {
			return array();
		}
    } 
    
    public function getMainAddress($_merchant)
    {
		$addressCollection = $this->getAddressCollection($_merchant);
		$mainAddress = '';
		
		foreach ($addressCollection as $address) {
			$mainAddress = $address;
			break;
		}
		
        return $mainAddress;
    } 
}