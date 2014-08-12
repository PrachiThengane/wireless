<?php

class Devinc_Multipledeals_Helper_Data extends Mage_Core_Helper_Abstract
{
	//$toDate format(year-month-day hour:minute:second) = 0000-00-00 00:00:00
    public function getCountdown($toDate, $countdownId = 'main', $finished = false, $params = false)
    {
    	//from/to date variables
		$toDate = str_replace(',',':',$toDate);
		$fromDate = Mage::getModel('core/date')->date('Y-m-d H:i:s');
		$jsFromDate = date('F d, Y H:i:s', strtotime($fromDate));
		$jsToDate = date('F d, Y H:i:s', strtotime($toDate));			
		if ($finished) {
			$toDate = $fromDate;
			$jsToDate = $jsFromDate;	
		}	
		
		$countdownType = Mage::getStoreConfig('multipledeals/configuration/countdown_type');
		//js configuration
		$jsTextColor = Mage::getStoreConfig('multipledeals/js_countdown_configuration/textcolor');
		$jsDaysText = Mage::getStoreConfig('multipledeals/js_countdown_configuration/days_text');
		
		//flash configuration		
		$displayDays = Mage::getStoreConfig('multipledeals/countdown_configuration/display_days');
		$bgMain = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/bg_main'));
		$bgColor = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/bg_color'));
		$digitColor = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/textcolor'));
		$alpha = Mage::getStoreConfig('multipledeals/countdown_configuration/alpha');
		$secText = Mage::getStoreConfig('multipledeals/countdown_configuration/sec_text');
		$minText = Mage::getStoreConfig('multipledeals/countdown_configuration/min_text');
		$hourText = Mage::getStoreConfig('multipledeals/countdown_configuration/hour_text');
		$daysText = Mage::getStoreConfig('multipledeals/countdown_configuration/days_text');
		$textColor = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/txt_color'));
					
		if (isset($params['bg_main'])) $bgMain = str_replace('#','0x',$params['bg_main']);
    	$width = (isset($params['width'])) ? $params['width'] : '100%';
    	$height = (isset($params['height'])) ? $params['height'] : '100%';
		
		//get flash source
		$date1 = strtotime($fromDate);
	    $date2 = strtotime($toDate);	   
		$dateDiff = $date2 - $date1;
		
		if ($displayDays==1) {
			$fullDays = floor($dateDiff/(60*60*24));
			if ($fullDays<=0) {
				$swfPath = 'multipledeals/flash/countdown.swf';
			} else {
				$swfPath = 'multipledeals/flash/countdown_days.swf';
			} 
		} else {
			if ($dateDiff>0) {
				$diff = abs($dateDiff); 
				$years   = floor($diff / (365*60*60*24)); 
				$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
				
				$hoursLeft = $days*24+$hours;
				if ($hoursLeft<100) {
					$swfPath = 'multipledeals/flash/countdown_multiple_2.swf';	
				} else {
					$swfPath = 'multipledeals/flash/countdown_multiple_3.swf';		
				}
			} else {
				$swfPath = 'multipledeals/flash/countdown_multiple_2.swf';		
			}
		}	
		$store = Mage::app()->getStore();
		$source = Mage::getDesign()->getSkinUrl($swfPath, array('_area' => 'frontend', '_store' => $store));
		
		//encode flash variables		
		$variables = $this->encodeFlashVariables($fromDate, $toDate, $alpha, $bgColor, $digitColor, $bgMain, $textColor);
		$textVariables = $secText.'|||'.$minText.'|||'.$hourText.'|||'.$daysText; 	
		
		$random = rand(10e16, 10e20);
		$html = '<div class="countdown-container">
					<div id="countdown-'.$countdownId.'-'.$random.'">
						<script type="text/javascript">
				 			var jsCountdown = new JsCountdown("'.$jsFromDate.'", "'.$jsToDate.'", "countdown-'.$countdownId.'-'.$random.'", "'.$jsDaysText.'", "'.$jsTextColor.'");
				 		</script>
				 	</div>
				</div>';
		
		//if countdown type flash and flash present, replace default javascript countdown
		if ($countdownType==1) {   
			$html .= '<script type="text/javascript">						
					     var params = {}; 	
					     var flashvars = {};
					     var attributes = {};
					     
 					     params.menu = "false"; 
 					     params.salign = "MT";
 					     params.allowFullscreen = "true";
					     if (navigator.userAgent.indexOf("Opera") <= -1) {
 					         params.wmode = "opaque";
					     }				 	
					     flashvars.vs = "'.$variables.'";
					     flashvars.smhd = "'.$textVariables.'";				 		
					     
					     swfobject.embedSWF("'.$source.'", "countdown-'.$countdownId.'-'.$random.'", "'.$width.'", "'.$height.'", "9.0.0", false, flashvars, params, attributes);				 			
					 </script>';
		}
	
        return $html;
    }
    
    public function encodeFlashVariables($fromDate, $toDate, $alpha, $bgColor, $digitColor, $bgMain, $textColor) {
    	$encodedVars = base64_encode($fromDate.'&&&'.$toDate.'&&&'.$alpha.'&&&'.$bgColor.'&&&'.$digitColor.'&&&'.$bgMain.'&&&'.$textColor);
    	$signatureEncodedVars = '';
		$i = 0;		
		
		while (strlen($encodedVars)>0) {
			if ($i%2==0) {
				$signatureEncodedVars .= substr($encodedVars,0,10).'dMD';
			} else {
				$signatureEncodedVars .= substr($encodedVars,0,10).'Dmd';					
			}
			$encodedVars = substr($encodedVars,10,1000);
			$i++;
		}	
			
		$signatureEncodedVars = substr($signatureEncodedVars,0,-3);
		
		return $signatureEncodedVars;
    }
    
    //called on product list pages
    public function getProductCountdown($_product, $_params = false, $_timeLeftText = true) {
		$multipledeal = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', 3)->addFieldToFilter('product_id', $_product->getId())->getFirstItem();
		$html = '';		
		if ($multipledeal->getId()) {
			$toDate = $multipledeal->getDateTo().' '.$multipledeal->getTimeTo();
    		$finished = ($_product->isSaleable()) ? false : true;
    		$html .= ($_timeLeftText) ? '<b>'.$this->__('Time left to buy:').'</b>' : '';
			$html .= $this->getCountdown($toDate, $_product->getId(), $finished, $_params);
		}
				
		return $html;

    }
}