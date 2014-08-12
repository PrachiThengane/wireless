<?php

class Devinc_Groupdeals_Model_Notifications extends Mage_Core_Model_Abstract
{	
	const ITERATIONS = 10;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/notifications');
    }
	
    public function notify()
    {						
		//New Deal Notification
		if (Mage::getStoreConfig('groupdeals/notifications/email_new_deal')) {
			$groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection();
	
			if (count($groupdealsCollection)>0) {
				foreach ($groupdealsCollection as $groupdeals) {
					$website_collection = Mage::getModel('core/website')->getCollection();
					foreach ($website_collection as $website) 
					{	
						$website_id = $website->getId();
						$_storeId = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website_id)->getFirstItem()->getId();
						$_product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($groupdeals->getProductId());
						$product_website_ids = $_product->getWebsiteIds();
						$groupdeals_status = $_product->getGroupdealStatus();
						$notification_exist = Mage::getModel('groupdeals/notifications')->getCollection()->setOrder('notification_id', 'DESC')->addFieldToFilter('type', 'new_deal')->addFieldToFilter('groupdeals_id', $groupdeals->getId())->addFieldToFilter('website_id', $website_id)->getFirstItem()->getId();
						if ($groupdeals_status==1 && $notification_exist=='' && in_array($website_id, $product_website_ids)) {			
							// get all website stores
							$stores_collection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website_id);
							$store_ids = array();
							foreach ($stores_collection as $store) {
								$store_ids[] = $store->getId();
							}
							$subscribersCollection = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('city', $groupdeals->getCity())->addFieldToFilter('store_id', array('in', $store_ids));
							$subscriber_ids = '';						
							if (count($subscribersCollection)>0) {
								foreach ($subscribersCollection as $subscriber) {
									$subscriber_ids .= $subscriber->getId().',';
								}
								$subscriber_ids = substr($subscriber_ids,0,-1);
							}
							
							$notification = Mage::getModel('groupdeals/notifications')
												->setGroupdealsId($groupdeals->getId())
												->setWebsiteId($website_id)
												->setType('new_deal')
												->setUnnotifiedSubscriberIds($subscriber_ids)
												->setStatus('pending')
												->save();						
						}
					}
				}
			}
		}
		
		//Limit Met Notification
		if (Mage::getStoreConfig('groupdeals/notifications/email_limit_met')) {
			$groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection();
	
			if (count($groupdealsCollection)>0) {
				foreach ($groupdealsCollection as $groupdeals) {
					$website_collection = Mage::getModel('core/website')->getCollection();
					foreach ($website_collection as $website) 
					{	
						$website_id = $website->getId();
						$_storeId = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website_id)->getFirstItem()->getId();
						$_product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($groupdeals->getProductId());
						$product_website_ids = $_product->getWebsiteIds();
						$groupdeals_status = $_product->getGroupdealStatus();
						$notification_exist = Mage::getModel('groupdeals/notifications')->getCollection()->setOrder('notification_id', 'DESC')->addFieldToFilter('type', 'limit_met')->addFieldToFilter('groupdeals_id', $groupdeals->getId())->addFieldToFilter('website_id', $website_id)->getFirstItem()->getId();
						if ($groupdeals_status==1 && $notification_exist=='' && $groupdeals->getMinimumQty()<=$groupdeals->getSoldQty() && in_array($website_id, $product_website_ids)) {
							// get all website stores
							$stores_collection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website_id);
							$store_ids = array();
							foreach ($stores_collection as $store) {
								$store_ids[] = $store->getId();
							}
							$subscribersCollection = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('city', $groupdeals->getCity())->addFieldToFilter('store_id', array('in', $store_ids));
							$subscriber_ids = '';						
							if (count($subscribersCollection)>0) {
								foreach ($subscribersCollection as $subscriber) {
									$subscriber_ids .= $subscriber->getId().',';
								}
								$subscriber_ids = substr($subscriber_ids,0,-1);
							}
							
							$notification = Mage::getModel('groupdeals/notifications')
												->setGroupdealsId($groupdeals->getId())
												->setWebsiteId($website_id)
												->setType('limit_met')
												->setUnnotifiedSubscriberIds($subscriber_ids)
												->setStatus('pending')
												->save();						
						}
					}
				}
			}
		}	
		
		//Deal Over Notification
		if (Mage::getStoreConfig('groupdeals/notifications/email_deal_over')) {
			$groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection();
	
			if (count($groupdealsCollection)>0){
				foreach ($groupdealsCollection as $groupdeals) {
					$website_collection = Mage::getModel('core/website')->getCollection();
					foreach ($website_collection as $website) 
					{	
						$website_id = $website->getId();
						$_storeId = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website_id)->getFirstItem()->getId();
						$_product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($groupdeals->getProductId());
						$product_website_ids = $_product->getWebsiteIds();
						$groupdeals_status = $_product->getGroupdealStatus();
						$notification_exist = Mage::getModel('groupdeals/notifications')->getCollection()->setOrder('notification_id', 'DESC')->addFieldToFilter('type', 'deal_over')->addFieldToFilter('groupdeals_id', $groupdeals->getId())->addFieldToFilter('website_id', $website_id)->getFirstItem()->getId();
						if ($groupdeals_status==4 && $notification_exist=='' && in_array($website_id, $product_website_ids)) {
							// get all website stores
							$stores_collection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website_id);
							$store_ids = array();
							foreach ($stores_collection as $store) {
								$store_ids[] = $store->getId();
							}
							$subscribersCollection = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('city', $groupdeals->getCity())->addFieldToFilter('store_id', array('in', $store_ids));
							$subscriber_ids = '';						
							if (count($subscribersCollection)>0) {
								foreach ($subscribersCollection as $subscriber) {
									$subscriber_ids .= $subscriber->getId().',';
								}
								$subscriber_ids = substr($subscriber_ids,0,-1);
							}
							
							$notification = Mage::getModel('groupdeals/notifications')
												->setGroupdealsId($groupdeals->getId())
												->setWebsiteId($website_id)
												->setType('deal_over')
												->setUnnotifiedSubscriberIds($subscriber_ids)
												->setStatus('pending')
												->save();	
						}
					}
				}
			}
		}
		
		$notification = Mage::getModel('groupdeals/notifications')->getCollection()->setOrder('notification_id', 'DESC')->addFieldToFilter('status', 'pending')->getFirstItem();
		
		if ($notification->getId()!='') {	
			$sender = Mage::getStoreConfig('groupdeals/notifications/email_sender');
			
			$groupdeals = Mage::getModel('groupdeals/groupdeals')->load($notification->getGroupdealsId());	
			$reply_to = Mage::getStoreConfig('trans_email/ident_'.$sender.'/email');
			
			$unnotified_subscriber_ids = explode(',', $notification->getUnnotifiedSubscriberIds());
			$unnotified_subscriber_ids_temp = $unnotified_subscriber_ids;
			$unnotified_subscriber_ids_string = '';
			$notified_subscriber_ids = array();
			if ($notification->getNotifiedSubscriberIds()!='') {
				$notified_subscriber_ids = explode(',', $notification->getNotifiedSubscriberIds());
			}			
			$i = 0;
			
			foreach ($unnotified_subscriber_ids as $subscriber_id) {						
				$i++;
				
				$subscriber = Mage::getModel('groupdeals/subscribers')->load($subscriber_id);	
				$storeId = $subscriber->getStoreId();
				$_product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($groupdeals->getProductId());						
				$_merchant = Mage::getModel('groupdeals/merchants')->load($groupdeals->getMerchantId());						
								
				$data['name'] = $_product->getName();
				$data['city'] = $groupdeals->getCity();
				//$data['image_url'] = $_product->getMediaConfig()->getMediaUrl($_product->getData('image'));
				$data['image_url'] = Mage::helper('catalog/image')->init($_product, 'image');
								
				$notified_subscriber_ids[] = $subscriber_id;
				$storeCode = Mage::app()->getStore($storeId)->getCode();
				
				$urlRequestPath = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $storeId)->addFieldToFilter('target_path', 'groupdeals/product/view/id/'.$groupdeals->getProductId().'/groupdeals_id/'.$groupdeals->getId())->getFirstItem()->getRequestPath();
				$data['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$urlRequestPath.'?___store='.$storeCode.'&city='.urlencode($groupdeals->getCity()); 		
				
				$data['unsubscribe_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'groupdeals/unsubscribe/customer/email/'.urlencode($subscriber->getEmail()).'/city/'.urlencode($groupdeals->getCity()).'/store/'.$storeId;  
				$data['website_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);  
				$data['website'] = str_replace('http://','',Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));  
				$data['email'] = Mage::getStoreConfig('contacts/email/recipient_email');  
				
				$storeDatetime = new DateTime();
				$storeDatetime->setTimezone(new DateTimeZone(Mage::getStoreConfig('general/locale/timezone', $storeId)));				
				$current_date_time = $storeDatetime->format('l, F d Y');				
				$data['date'] = $current_date_time;  
				
				// Get Prices with taxes
				$data['special_price'] = Mage::getModel('groupdeals/groupdeals')->getFormatedPrice($_product, $_product->getSpecialPrice());  
				$data['price'] = Mage::getModel('groupdeals/groupdeals')->getFormatedPrice($_product, $_product->getPrice());  
				$discount = ($_product->getPrice()-$_product->getSpecialPrice())*100/$_product->getPrice();
				$data['discount'] = number_format($discount,0).'%';
				$you_save_price = $_product->getPrice()-$_product->getSpecialPrice();
				$data['you_save'] = Mage::getModel('groupdeals/groupdeals')->getFormatedPrice($_product, $you_save_price);  
				
				$data['merchant_name'] = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getName(),$storeId);  
				$data['merchant_phone'] = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getPhone(),$storeId);  
				$data['merchant_mobile'] = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getMobile(),$storeId);  
				$data['merchant_email'] = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getEmail(),$storeId);  
				$data['merchant_website'] = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getWebsite(),$storeId);  
				if($data['merchant_website']!=''){
					$data['has_merchant_website'] = true;
				} else {
					$data['has_merchant_website'] = false;
				}

				$address_string = $_merchant->getAddress();
				if (isset($address_string) && $address_string!='') {
					$address = explode('_;_',$address_string);
					for ($j = 0; $j<count($address); $j++) {
						if ($address[$j]!='') {
							$data['merchant_address'.($j+1)] = $address[$j];
						}
					}
				} else {
					$data['merchant_address1'] = $_merchant->getRedeem();
				}	

				$data['merchant_description'] = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getDescription(),$storeId);
				
				$groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', $data['city'])->addFieldToFilter('product_id', array('neq'=>$_product->getId()))->setOrder('groupdeals_id', 'DESC');
				
				$data['side_deals'] = '';
				foreach ($groupdeals_collection as $groupdeal) { 
					$side_deal = Mage::getModel('catalog/product')->setStoreId($storeId)->load($groupdeal->getProductId());
					if ($side_deal->getGroupdealStatus()==1) {   
						$data['side_deals'] .='<tr style="border-bottom:1px solid #F0F1F3;">
								<td width="35%" align="center" style="font-weight:bold;padding:5px 5px 5px 10px;" >
									<img width="100%" alt="" src="'.Mage::helper('catalog/image')->init($side_deal, 'small_image').'">
								</td>
								<td width="65%" align="left" style="font-weight:bold;padding:5px 10px 5px 5px; font-size:11px;" >
									<a style="color:#0981BE;" href="'.$side_deal->getProductUrl().'?___store='.$storeCode.'&city='.urlencode($data['city']).'">'.$side_deal->getName().'</a>
								</td>
							</tr>';          
					}
				}				
				if($data['side_deals']!=''){
					$data['merchant_description_width'] = '65%';
					$data['has_side_deals'] = true;
				} else {
					$data['merchant_description_width'] = '100%';
					$data['has_side_deals'] = false;
				}
				
				$data['all_deals_url'] = $data['website_url'].'groupdeals/product/list/city/'.urlencode($data['city']);
				$data['recent_deals_url'] = $data['website_url'].'groupdeals/product/recent/city/'.urlencode($data['city']);
				$data['contact_us_url'] = $data['website_url'].'contacts/';
				
				$postObject = new Varien_Object();
				$postObject->setData($data);	 
				
				$template = 'groupdeals_notifications_email_new_deal_template';
				if ($notification->getType()=='new_deal') {	
					$template = Mage::getStoreConfig('groupdeals/notifications/email_new_deal_template', $storeId);
				} elseif ($notification->getType()=='limit_met') {
					$template = Mage::getStoreConfig('groupdeals/notifications/email_limit_met_template', $storeId);
				} elseif ($notification->getType()=='deal_over') {
					$template = Mage::getStoreConfig('groupdeals/notifications/email_deal_over_template', $storeId);
				}
				$mailTemplate = Mage::getModel('core/email_template');
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($reply_to)
					->sendTransactional(
						$template,
						$sender , 
						$subscriber->getEmail(),
						null,
						array('data' => $postObject),
						$storeId
					);
					
				$unnotified_subscriber_ids_temp = array_diff($unnotified_subscriber_ids_temp, array($subscriber_id));				
										
				if ($i>=self::ITERATIONS || $i==count($unnotified_subscriber_ids)) {	
					$unnotified_subscriber_ids_string = implode(",", $unnotified_subscriber_ids_temp);							
					$notified_subscriber_ids_string = implode(',', $notified_subscriber_ids);							
					break;
				}
			}				
			
			$notification = Mage::getModel('groupdeals/notifications')->load($notification->getId())
								->setUnnotifiedSubscriberIds($unnotified_subscriber_ids_string)
								->setNotifiedSubscriberIds($notified_subscriber_ids_string);
			if ($unnotified_subscriber_ids_string=='') {
				$notification->setStatus('complete');
			}
							
			$notification->save();	
		}
    }	
	
}