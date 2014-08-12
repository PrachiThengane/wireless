<?php

class Devinc_Groupdeals_Model_Coupons extends Mage_Core_Model_Abstract
{		
	const ITERATIONS = 10;
    const CSV_SEPARATOR = ',';
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/coupons');
    }
    
    public function updateCoupons()
    {
        $active_recent_ids = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('groupdeal_status')			
				->addAttributeToFilter('groupdeal_status', array('in', array(1, 4)))
				->load()->getColumnValues('entity_id');
				
		$groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('target_met_email', 1)->addFieldToFilter('product_id', array('in', $active_recent_ids));
		$groupdealsCollection->getSelect()->where('sold_qty>=minimum_qty');
		
		if (count($groupdealsCollection)>0) {
			$groupdeals_ids = array();
			foreach ($groupdealsCollection as $groupdeals){
				$groupdeals_ids[] = $groupdeals->getId();
			}
			
			$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('status', 'pending')->addFieldToFilter('groupdeals_id', array('in', $groupdeals_ids));
			if (count($couponsCollection)>0) {		
				foreach ($couponsCollection as $coupon) {
			  		$coupon->setStatus('sending')->save();
				}
			}			
		}
    }
    
	public function email()
    {			
		$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->setOrder('coupon_id', 'DESC')->setOrder('order_item_id', 'DESC')->addFieldToFilter('status', 'sending');
		if (count($couponsCollection)>0) {				
			$pdf_attachment = Mage::getStoreConfig('groupdeals/configuration/pdf_attachment');
			$sender = Mage::getStoreConfig('groupdeals/notifications/email_sender');
			$reply_to = Mage::getStoreConfig('trans_email/ident_'.$sender.'/email');
			$i = 0;
			
			foreach ($couponsCollection as $coupon) {
				$i++;
				$groupdeals = Mage::getModel('groupdeals/groupdeals')->load($coupon->getGroupdealsId());	
				$order_item = Mage::getModel('sales/order_item')->load($coupon->getOrderItemId());
				$order = Mage::getModel('sales/order')->load($order_item->getOrderId());
				$storeId = $order->getStoreId();
				$product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($groupdeals->getProductId());
				$product_name = $product->getName();
				
				$customer_name = $order->getBillingAddress()->getName();
				if($order->getGroupdealsCouponToEmail()==''){
					$customer_email = $order->getCustomerEmail();
				} else {
					$customer_email = $order->getGroupdealsCouponToEmail();
				}
				
				$html = $this->getCouponHtml($groupdeals, $coupon, $order_item, $customer_name, $storeId);	
				$pdf_html = $this->getCouponPdfHtml($groupdeals, $coupon, $order_item, $customer_name, $storeId);
				
				$email_data['name'] = $product_name;
				$email_data['coupon_code'] = $coupon->getCouponCode();
				$email_data['content'] = $html;
			
				$postObject = new Varien_Object();
				$postObject->setData($email_data);	
								
				$mailTemplate = Mage::getModel('core/email_template');
				
				if ($pdf_attachment==1) {
					require_once("dompdf/dompdf_config.inc.php");
					spl_autoload_register('DOMPDF_autoload'); 
					
					$dompdf = new DOMPDF();
					$dompdf->set_paper("a4", "portrait"); 
					$dompdf->load_html(utf8_decode($pdf_html));
					$dompdf->render(); 
					$pdfoutput = $dompdf->output();	
					
					$this->addAttachment($mailTemplate, $pdfoutput, 'coupon'.$coupon_code.'.pdf');				
				}
					
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($reply_to)
					->sendTransactional(
						'groupdeals_notifications_email_coupon_template',
						$sender , 
						$customer_email,
						null,
						array('data' => $postObject)
					);	
					
				$couponModel = Mage::getModel('groupdeals/coupons')->load($coupon->getId())
									->setStatus('complete')
									->save();	
									
				if ($i>=self::ITERATIONS) {							
					break;
				}
			}			
		}
    }

	public function getCouponHtml($groupdeals, $coupon = null, $order_item = null, $customer_name = 'JOHN DOE', $store_id)
	{
		$merchant = Mage::getModel('groupdeals/merchants')->getCollection()->addFieldToFilter('merchants_id', array('eq' => $groupdeals->getMerchantId()))->getFirstItem();
		$addresses = '';
		
		if ($merchant->getAddress()!='') {
			$addressCollection = explode('_;_',$merchant->getAddress());
		
			$j = 1;
			foreach ($addressCollection as $address){
				$addresses .= $j.'. '.$address.'<br/>';
				$j++;
			}
		} else {
			$addresses = '';
		}
		
		$this->_locale = new Zend_Locale(Mage::getStoreConfig('general/locale/code', $store_id));
		$localeCode = $this->_locale->toString();
		$file = 'app/locale/'.$localeCode.'/Groupdeals.csv';
		
		if (!file_exists($file)) {
			$file = 'app/locale/en_US/Groupdeals.csv';
		}
		
		$data = array();
		if (file_exists($file)) {
			$parser = new Varien_File_Csv();
			$parser->setDelimiter(self::CSV_SEPARATOR);
			$data = $parser->getDataPairs($file);
		}
		
		if ($merchant->getId() && $merchant->getStatus()==1) {
			$data['Redeamable in %s.'] = '';
			$temp_redeem_array = explode('%s', $data['Redeamable at %s in %s.']);
			if ($groupdeals->getCity()=='Universal') {
				$data['Redeamable at %s in %s.'] = $temp_redeem_array[0].'<strong>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getName(),$store_id).'</strong>'.$temp_redeem_array[2];
			} else {
				$data['Redeamable at %s in %s.'] = $temp_redeem_array[0].'<strong>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getName(),$store_id).'</strong>'.$temp_redeem_array[1].'<strong>'.$groupdeals->getCity().'</strong>'.$temp_redeem_array[2];
			} 
		} else {
			$data['Redeamable at %s in %s.'] = '';
			$temp_redeem_array = explode('%s', $data['Redeamable in %s.']);
			if ($groupdeals->getCity()=='Universal') {
				$data['Redeamable in %s.'] = '';
			} else {
				$data['Redeamable in %s.'] = $temp_redeem_array[0].'<strong>'.$groupdeals->getCity().'</strong>'.$temp_redeem_array[1];
			} 			
		}
		
		$product = Mage::getModel('catalog/product')->setStoreId($store_id)->load($groupdeals->getProductId());
		$product_name = $product->getName();
		if ($product->getImage() != 'no_selection' && $product->getImage()){
			$product_image = Mage::helper('catalog/image')->init($product, 'image');
		} else {
			$product_image =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/images/catalog/product/placeholder/image.jpg';
		}
		
		$gift = '';
		if(is_null($coupon) || is_null($order_item)){
			$coupon_code = 'LXEANN9T9T0';
			$value = Mage::getModel('groupdeals/groupdeals')->getFormatedPrice($product, $product->getSpecialPrice());
			$custom_options = 'The selected custom options will appear here.';
		} else {
			$coupon_code = $coupon->getCouponCode();
			$value = Mage::getModel('groupdeals/groupdeals')->getFormatedPrice($product, $order_item->getPrice());
			$custom_options = '';
			$options = $this->getItemOptions($groupdeals->getProductId(),$order_item);
			if ($options) {
				foreach ($options as $option) {
					$_formatedOptionValue = $this->getFormatedOptionValue($option);
					$custom_options = '<span style="color:#333333;">'.$option['label'].':</span> '.$_formatedOptionValue['value'].'<br/>';
				}
			}
			
			$order = Mage::getModel('sales/order')->load($order_item->getOrderId());
			if($order->getGroupdealsCouponTo()!=''){
				$customer_name = $order->getGroupdealsCouponTo();
				$temp_gift_array = explode('%s', $data['This is a Gift Coupon sent to you by %s.']);
				$data['This is a Gift Coupon sent to you by %s.'] = $temp_gift_array[0].'<strong>'.$order->getGroupdealsCouponFrom().'</strong>'.$temp_gift_array[1];
				$gift = '<br/><br/><span style="width:413px; font-size:13px; color:#333333; ">'.$data['This is a Gift Coupon sent to you by %s.'].'</span><br/><br/><span style="font-size:13px; color:#333333; "><strong>'.$data['Gift Message:'].'</strong> '.$order->getGroupdealsCouponMessage().'</span>';
			}
		}
	
		$html = 
		   '<html><body style="font-family:Arial, Helvetica, sans-serif;">
			<table width="700" style="background:#f0f1f3;padding:10px;">
				<tbody>
					<tr>
						<td>
							<table style="width:100%;" >
								<thead>
									<tr  style="background:#313131;">
										<th style="padding:20px 25px;text-align:left;border-bottom:2px solid #f1461e;">
											<img width="150px" style="float:left;" src="'.Mage::getModel('core/design_package')->getSkinUrl(Mage::getStoreConfig('design/header/logo_src')).'" alt="" />';
											if ($groupdeals->getCouponMerchantLogo()!='' && $merchant->getId() && $merchant->getStatus()==1 && $merchant->getMerchantLogo()!='') $html .= '<img style="width:100px;float:right;margin:10px 0px 0 0;" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$merchant->getMerchantLogo().'" alt="" />';										
										$html .= '</th>
									</tr>
								</thead>
								<tbody>
									<tr style="background:#fff; ">
										<td style="font-size:11px; padding:15px; color:#808080; ">
											<div style="text-align:left;clear:both;margin:0 0 10px 0;">
												<h3 style="padding:0px; font-size:22px; font-weight:bold; margin:0px; color:#333333;">'.$product_name.'</h3> 
												<span style="width:413px; font-size:13px; color:#333333; ">'.$data['Redeamable at %s in %s.'].$data['Redeamable in %s.'].'</span>
												'.$gift.'
											</div>	
											<div style="float:left; width:46%;"><a href=""><img width="100%" style="border:2px solid #D6D7D9;" src="'.$product_image.'" alt="" /></a></div>
											<div style="float:right; width:50%;">	
												<div style="margin-top:10px;clear:both;margin:0 0 3px; padding:0 0 0 2px;">
													<span style="font-size:12px; color:#333333; ">'.$data['Customer Name:'].' <span style="font-weight:bold;">'.$customer_name.'</span></span><br/>
												</div>
												<table width="100%" style="font-size:11px;margin:0px 0px 0px; border-top:1px dashed #DDDDDD; padding-bottom:5px;">
													<tr><td style="color:#333333; padding:0; width:70px; padding-top:4px; ">'.$data['Coupon #:'].'</td> <td style="padding:0; padding-top:4px;">'.$coupon_code;
												if ($groupdeals->getCouponExpirationDate()!='0000-00-00') $html .= '<tr><td style="color:#333333; padding:0; ">'.$data['Valid Until:'].'</td> <td style="padding:0;">'.Mage::app()->getLocale()->date($groupdeals->getCouponExpirationDate())->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)).' </td></tr>';
												if ($groupdeals->getCouponPrice()==1) $html .= '
													<tr><td style="color:#333333; ">'.$data['Value:'].'</td> <td style="padding:0;">'.$value.'</td></tr>';
												if ($product->getHasOptions()==1) {
													$html .= '<tr><td colspan="2" style="padding:5px 0 0;"><span style="font-size:11px; color:#333333">'.$data['CUSTOM OPTIONS:'].'</span><br/>'.$custom_options.'</td></tr>';	
												} 
												if ($groupdeals->getCouponBarcode()!='') $html .= '<tr><td colspan="2"><img style="width:110px; margin-top:3px;" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$groupdeals->getCouponBarcode().'" alt="" /></td></tr>';	
												$html .= '</table>';
												if ($merchant->getId() && $merchant->getStatus()==1){ 
													$html .='<div style=" padding:12px 0 0 0px; border-top:1px dashed #DDDDDD;">';
													if ($groupdeals->getCouponMerchantAddress()==1 && $addresses!='') {
														$html .= '<span style="font-size:11px; color:#333333">'.$data['ADDRESS:'].'</span><br/>'.$addresses.'<br/>';
													} elseif ($groupdeals->getCouponMerchantAddress()==1) {
														$html .= '<span style="font-size:11px; color:#333333">'.$data['ADDRESS:'].'</span><br/>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getRedeem(),$store_id).'<br/><br/>';
													}
													if ($groupdeals->getCouponMerchantContact()==1) {
														$html .= '<table width="100%" style="font-size:11px;margin:0px 0px 0px; padding:10px 0px 3px 0; border-top:1px dashed #DDDDDD; " cellspacing="0" cellpadding="0">
																	<tr>
														<td colspan="2"><span style="font-size:11px; color:#333333">'.$data['CONTACT INFO:'].'</span></td>
														</tr>';
														if (Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getPhone(),$store_id)!='') $html .= '<tr><td style="color:#333333; padding:1px 0px; width:50px;"><span style="color:#333333;">'.$data['Phone:'].'</span></td> <td style="padding:1px 0px;">'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getPhone(),$store_id).'</td></tr>';
														if (Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getMobile(),$store_id)!='') $html .= '<tr><td style="color:#333333; padding:1px 0px; width:50px;"><span style="color:#333333;">'.$data['Mobile:'].'</span></td> <td style="padding:1px 0px;">'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getMobile(),$store_id).'</td></tr>';
														if (Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getEmail(),$store_id)!='') $html .= '<tr><td style="color:#333333; padding:1px 0px; width:50px;"><span style="color:#333333;">'.$data['E-Mail:'].'</span></td> <td style="padding:1px 0px;">'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getEmail(),$store_id).'</td></tr>';
														if (Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getWebsite(),$store_id)!='') $html .= '<tr><td style="color:#333333; padding:1px 0px; width:50px;"><span style="color:#333333;">'.$data['Website:'].'</span></td> <td style="padding:1px 0px;">'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getWebsite(),$store_id).'</td></tr>';
														$html .= '</table>';
													}
													$html .= '</div>';
												}
											$html .= '</div>';
											if ($groupdeals->getCouponFinePrint()==1 || $groupdeals->getCouponHighlights()==1) {
												$html .= '<div style="float:left; width:100%; border-top:1px dashed #DDDDDD; margin:8px 0 0; padding:10px 0 0;">';
													if ($groupdeals->getCouponFinePrint()==1) {
													$html .= '<div style="float:left; width:48%">
														<h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:0; color:#333333;">'.$data['The Fine Print'].'</h3>
														'.str_replace('<ul>','<ul style="padding-left:18px; margin-left:0px; list-style-type:disc;">',str_replace('<ol>','<ol style="padding-left:18px; margin-left:0px; list-style-type:decimal;">',$product->getGroupdealFineprint())).'
													</div>';
													}
													if ($groupdeals->getCouponHighlights()==1) {
													$html .= '<div style="float:right; width:50%">
														<h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:0; color:#333333;">'.$data['Highlights'].'</h3>
														'.str_replace('<ul>','<ul style="padding-left:18px; margin-left:0px; list-style-type:disc;">',str_replace('<ol>','<ol style="padding-left:18px; margin-left:0px; list-style-type:decimal;">',$product->getGroupdealHighlights())).' 
													</div>';
													}
												$html .= '</div>';
											}
											if (($groupdeals->getCouponMerchantDescription()==1 || $groupdeals->getCouponBusinessHours()==1) && $merchant->getId() && $merchant->getStatus()==1) {
											$html .= '<div style="float:left; width:100%">';
												if ($groupdeals->getCouponMerchantDescription()==1) {
												$html .= '<h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:10px 0 0; color:#333333;">'.$data['Merchant Description'].'</h3>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getDescription(),$store_id);
												}
												if ($groupdeals->getCouponBusinessHours()==1) {
												$html .= '<span style="display:block; padding:0px; font-size:11px; font-weight:bold; margin:10px 0px 0; color:#666;">'.$data['BUSINESS HOURS'].'</span>
												<div style="float:left;width:75%;">'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getBusinessHours(),$store_id).'</div>';
												}
											$html .= '</div>';	
											}		
											if ($groupdeals->getCouponAdditionalInfo()!='') {
												$html .= '<div style="float:left; width:100%">
													<h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:10px 0 0; color:#333333;">'.$data['Additional Info'].'</h3>
													'.$groupdeals->getCouponAdditionalInfo().' 
												</div>';
											}
										$html .= '</td> 
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			</body>
			</html>';
			
		return $html;
	}

	public function getCouponPdfHtml($groupdeals, $coupon = null, $order_item = null, $customer_name = 'JOHN DOE', $store_id)
	{
		$merchant = Mage::getModel('groupdeals/merchants')->getCollection()->addFieldToFilter('merchants_id', array('eq' => $groupdeals->getMerchantId()))->getFirstItem();
		$addresses = '';
		
		if ($merchant->getAddress()!='') {
			$addressCollection = explode('_;_',$merchant->getAddress());
		
			$j = 1;
			foreach ($addressCollection as $address){
				$addresses .= $j.'. '.$address.'<br/>';
				$j++;
			}
		} else {
			$addresses = '';
		}
		
		$this->_locale = new Zend_Locale(Mage::getStoreConfig('general/locale/code', $store_id));
		$localeCode = $this->_locale->toString();
		$file = 'app/locale/'.$localeCode.'/Groupdeals.csv';
		
		if (!file_exists($file)) {
			$file = 'app/locale/en_US/Groupdeals.csv';
		}
		
		$data = array();
		if (file_exists($file)) {
			$parser = new Varien_File_Csv();
			$parser->setDelimiter(self::CSV_SEPARATOR);
			$data = $parser->getDataPairs($file);
		}

		if ($merchant->getId() && $merchant->getStatus()==1) {
			$data['Redeamable in %s.'] = '';
			$temp_redeem_array = explode('%s', $data['Redeamable at %s in %s.']);
			if ($groupdeals->getCity()=='Universal') {
				$data['Redeamable at %s in %s.'] = $temp_redeem_array[0].Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getName(),$store_id);
			} else {
				$data['Redeamable at %s in %s.'] = $temp_redeem_array[0].Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getName(),$store_id).$temp_redeem_array[1].$groupdeals->getCity().$temp_redeem_array[2];
			} 
		} else {
			$data['Redeamable at %s in %s.'] = '';
			$temp_redeem_array = explode('%s', $data['Redeamable in %s.']);
			if ($groupdeals->getCity()=='Universal') {
				$data['Redeamable in %s.'] = '';
			} else {
				$data['Redeamable in %s.'] = $temp_redeem_array[0].$groupdeals->getCity();
			} 			
		}	
		
		$product = Mage::getModel('catalog/product')->setStoreId($store_id)->load($groupdeals->getProductId());
		$product_name = $product->getName();
		if ($product->getImage() != 'no_selection' && $product->getImage()){
			$product_image = Mage::helper('catalog/image')->init($product, 'image');
		} else {
			$product_image =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/images/catalog/product/placeholder/image.jpg';
		}

		$gift = '';
		if(is_null($coupon) || is_null($order_item)){
			$coupon_code = 'LXEANN9T9T0';
			$value = Mage::getModel('groupdeals/groupdeals')->getFormatedPrice($product, $product->getSpecialPrice());
			$custom_options = 'The selected custom options will appear here.';
		} else {
			$coupon_code = $coupon->getCouponCode();
			$value = Mage::getModel('groupdeals/groupdeals')->getFormatedPrice($product, $order_item->getPrice());
			$custom_options = '';
			$options = $this->getItemOptions($groupdeals->getProductId(),$order_item);
			if ($options) {
				foreach ($options as $option) {
					$_formatedOptionValue = $this->getFormatedOptionValue($option);
					$custom_options = '<span style="color:#333333;">'.$option['label'].':</span> '.$_formatedOptionValue['value'].'<br/>';
				}
			}
			
			$order = Mage::getModel('sales/order')->load($order_item->getOrderId());
			if($order->getGroupdealsCouponTo()!=''){
				$customer_name = $order->getGroupdealsCouponTo();
				$temp_gift_array = explode('%s', $data['This is a Gift Coupon sent to you by %s.']);
				$data['This is a Gift Coupon sent to you by %s.'] = $temp_gift_array[0].'<strong>'.$order->getGroupdealsCouponFrom().'</strong>'.$temp_gift_array[1];
				$gift = '<br/><br/><span style="width:413px; font-size:13px; color:#333333; ">'.$data['This is a Gift Coupon sent to you by %s.'].'</span><br/><br/><span style="font-size:13px; color:#333333; "><strong>'.$data['Gift Message:'].'</strong> '.$order->getGroupdealsCouponMessage().'</span>';
			}
		}
		
		$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body style="font-size:10px; font-family:Arial, Helvetica, sans-serif;;">
			<table width="580" bgcolor="#f0f1f3" style="padding:5px;">
				<tbody>
					<tr>
						<td>
							<table width="100%" >
								<tbody>
									<tr>
										<td bgcolor="#313131" style="border-bottom:2px solid #f1461e;">
											<table width="100%">
													<tr>
														<td style="text-align:left; padding:10px 12px;">
															<img width="150px" src="'.Mage::getModel('core/design_package')->getSkinUrl(Mage::getStoreConfig('design/header/logo_src')).'" alt="" />
														</td>';
														if ($groupdeals->getCouponMerchantLogo()!='' && $merchant->getId() && $merchant->getStatus()==1 && $merchant->getMerchantLogo()!='') $html .= '
														<td style="text-align:right; padding:10px 12px;">
															<img style="width:100px;" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$merchant->getMerchantLogo().'" alt="" />
														</td>';										
													$html .= '
													</tr>
											</table>
										</td>
									</tr>
									<tr bgcolor="#fff" style="text-align:left;">
										<td style="font-size:11px; padding:10px; color:#808080; ">
											<table width="100%">
												<tbody>
													<tr >										
														<td colspan="2" style="padding:0px;" >
															<h3 style="padding:0px; font-size:22px; font-weight:bold; margin:0px; color:#333333;">'.$product_name.'</h3> 
															<span style="width:413px; font-size:13px; color:#333333; ">'.$data['Redeamable at %s in %s.'].$data['Redeamable in %s.'].'</span>	
															'.$gift.'
														</td>
													</tr>
													<tr>										
														<td width="30%" style="padding:10px 0 0;vertical-align:top;" >
															<img width="100%" style="border:2px solid #D6D7D9;" src="'.$product_image.'" alt="" />
														</td>
														<td  width="70%" >
															<span style="font-size:12px; color:#333333; margin:0px 0px 0px 1px;">'.$data['Customer Name:'].' <span style="font-weight:bold;">'.$customer_name.'</span></span><br/>
															<table width="100%" style="font-size:11px; border-top:1px dashed #DDDDDD; padding-bottom:3px; margin:5px 0;">
																<tr><td style="color:#333333; padding:0; width:70px; padding-top:4px; ">'.$data['Coupon #:'].'</td> <td style="padding:0; padding-top:4px;">'.$coupon_code;
															if ($groupdeals->getCouponExpirationDate()!='0000-00-00') $html .= '<tr><td style="color:#333333; padding:0; ">'.$data['Valid Until:'].'</td> <td style="padding:0;">'.Mage::app()->getLocale()->date($groupdeals->getCouponExpirationDate())->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)).' </td></tr>';
															if ($groupdeals->getCouponPrice()==1) $html .= '
																<tr><td style="color:#333333; ">'.$data['Value:'].'</td> <td style="padding:0;">'.$value.'</td></tr>';
															if ($product->getHasOptions()==1) {
																$html .= '<tr><td colspan="2" style="padding:5px 0 0;"><span style="font-size:11px; color:#333333">'.$data['CUSTOM OPTIONS:'].'</span><br/>'.$custom_options.'</td></tr>';	
															} 
															if ($groupdeals->getCouponBarcode()!='') $html .= '<tr><td colspan="2"><img style="width:110px; margin-top:3px;" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$groupdeals->getCouponBarcode().'" alt="" /></td></tr>';	
															$html .= '</table>';
															if ($merchant->getId() && $merchant->getStatus()==1){
																$html .= '<div style="border-top:1px dashed #DDDDDD; padding:9px 0 0 0;">';
																if ($groupdeals->getCouponMerchantAddress()==1 && $addresses!='') {
																	$html .= '<span style="font-size:11px; color:#333333">'.$data['ADDRESS:'].'</span><br/>'.$addresses.'<br/>';
																} elseif ($groupdeals->getCouponMerchantAddress()==1) {
																	$html .= '<span style="font-size:11px; color:#333333">'.$data['ADDRESS:'].'</span><br/>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getRedeem(),$store_id).'';
																}
																
																if ($groupdeals->getCouponMerchantContact()==1) {
																	$html .= '<table width="100%" style="font-size:11px;margin:0px 0px 0px 0; padding:5px 0px 6px 0; border-top:1px dashed #DDDDDD;" cellspacing="0" cellpadding="0">
																				<tr>
																	<td colspan="2"><span style="font-size:11px; color:#333333">'.$data['CONTACT INFO:'].'</span></td>
																	</tr>';
																	if (Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getPhone(),$store_id)!='') $html .= '<tr><td style="color:#333333; padding:0; width:50px;"><span style="color:#333333;">'.$data['Phone:'].'</span></td> <td>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getPhone(),$store_id).'</td></tr>';
																	if (Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getMobile(),$store_id)!='') $html .= '<tr><td style="color:#333333; padding:0; width:50px;"><span style="color:#333333;">'.$data['Mobile:'].'</span></td> <td>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getMobile(),$store_id).'</td></tr>';
																	if (Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getEmail(),$store_id)!='') $html .= '<tr><td style="color:#333333; padding:0; width:50px;"><span style="color:#333333;">'.$data['E-Mail:'].'</span></td> <td>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getEmail(),$store_id).'</td></tr>';
																	if (Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getWebsite(),$store_id)!='') $html .= '<tr><td style="color:#333333; padding:0; width:50px;"><span style="color:#333333;">'.$data['Website:'].'</span></td> <td>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getWebsite(),$store_id).'</td></tr>';
																	$html .= '</table>';
																}
															$html .= '</div>';
															}
														$html .= '</td>
													</tr>';		
											if ($groupdeals->getCouponFinePrint()==1 || $groupdeals->getCouponHighlights()==1) {
												$html .= '<tr style="float:left; width:100%;  margin:6px 0 0; padding:10px 0 0;">';
													if ($groupdeals->getCouponFinePrint()==1) {
													$html .= '<td width="50%" style="border-top:1px dashed #DDDDDD; padding:7px 0 0 0 ;">
														<h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:0; color:#333333;">'.$data['The Fine Print'].'</h3>
														'.str_replace('<ul>','<ul style="padding-left:18px; list-style-type:disc;">',str_replace('<ol>','<ol style="padding-left:18px; list-style-type:decimal;">',$product->getGroupdealFineprint())).'
													</td>';
													}
													if ($groupdeals->getCouponHighlights()==1) {
													$html .= '<td width="50%" style="border-top:1px dashed #DDDDDD; padding:7px 0 0 0 ;">
														<h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:0; color:#333333;">'.$data['Highlights'].'</h3>
														'.str_replace('<ul>','<ul style="padding-left:18px; list-style-type:disc;">',str_replace('<ol>','<ol style="padding-left:18px; list-style-type:decimal;">',$product->getGroupdealHighlights())).' 
													</td>';
													}
												$html .= '</tr>';
											}
											if (($groupdeals->getCouponMerchantDescription()==1 || $groupdeals->getCouponBusinessHours()==1) && $merchant->getId() && $merchant->getStatus()==1) {
											$html .= '<tr>
															<td colspan="2">';
												if ($groupdeals->getCouponMerchantDescription()==1) {
												$html .= '<h3 style="display:block; font-size:14px; font-weight:bold; padding:5px 0 0; color:#333333;">'.$data['Merchant Description'].'</h3>
												'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getDescription(),$store_id).'</td></tr>';
												}
												if ($groupdeals->getCouponBusinessHours()==1) {
												$html .= '<tr><td colspan="2"><span style="display:block; font-size:11px; font-weight:bold; padding:5px 0px 0; color:#666;">'.$data['BUSINESS HOURS'].'</span>
												<span>'.Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getBusinessHours(),$store_id).'</span></td>';
												}
											$html .= '</tr>';	
											}	

											if ($groupdeals->getCouponAdditionalInfo()!='') {
												$html .= '<tr>
															  <td colspan="2">
													<h3 style="display:block; font-size:14px; font-weight:bold; padding:0; color:#333333;">'.$data['Additional Info'].'</h3>
													'.$groupdeals->getCouponAdditionalInfo().' 
													</td>
												</tr>';
											}
											
												$html .= '</tbody>
											</table>
										</td> 
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			</body>
			</html>';
			
		return  str_replace('</p','</span',str_replace('<p','<span style="display:block;"', $html));
	}	
	
    public function getItemOptions($product_id, $order_item_id)
    {
		$options = $order_item_id->getProductOptions();
        $result = array();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }
	
	public function getFormatedOptionValue($optionValue)
    {
        $optionInfo = array();

        // define input data format
        if (is_array($optionValue)) {
            if (isset($optionValue['option_id'])) {
                $optionInfo = $optionValue;
                if (isset($optionInfo['value'])) {
                    $optionValue = $optionInfo['value'];
                }
            } elseif (isset($optionValue['value'])) {
                $optionValue = $optionValue['value'];
            }
        }

        // render customized option view
        if (isset($optionInfo['custom_view']) && $optionInfo['custom_view']) {
            $_default = array('value' => $optionValue);
            if (isset($optionInfo['option_type'])) {
                try {
                    $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                    return array('value' => $group->getCustomizedView($optionInfo));
                } catch (Exception $e) {
                    return $_default;
                }
            }
            return $_default;
        }

        // truncate standard view
        $result = array();
        if (is_array($optionValue)) {
            $_truncatedValue = implode("\n", $optionValue);
            $_truncatedValue = nl2br($_truncatedValue);
            return array('value' => $_truncatedValue);
        } else {
            $_truncatedValue = Mage::helper('core/string')->truncate($optionValue, 55, '');
            $_truncatedValue = nl2br($_truncatedValue);
        }

        $result = array('value' => $_truncatedValue);

        if (Mage::helper('core/string')->strlen($optionValue) > 55) {
            $result['value'] = $result['value'] . ' <a href="#" class="dots" onclick="return false">...</a>';
            $optionValue = nl2br($optionValue);
            $result = array_merge($result, array('full_view' => $optionValue));
        }

        return $result;
    }
 
	public function addAttachment($mailTemplate, $rFile, $sFilename) {
		$attachment = $mailTemplate->getMail()->createAttachment($rFile);
		$attachment->type = 'application/pdf';
		$attachment->filename = $sFilename;
	}
}