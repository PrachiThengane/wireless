<?php
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';
class Devinc_Groupdeals_Adminhtml_GroupdealsController extends Mage_Adminhtml_Catalog_ProductController
{
    const CSV_SEPARATOR = ',';

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('groupdeals/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Deals Manager'), Mage::helper('adminhtml')->__('Deals Manager'));	
			
		return $this;
	}   
 
	public function indexAction() {		
		$this->_initAction()
            ->_title($this->__('Manage Deals'))
			->renderLayout();
	}
 
	public function newAction() {
		if (Mage::getSingleton('core/session')->getGroupdealsRefresh()!='') {
			Mage::getModel('groupdeals/groupdeals')->updateAfterSave();
		}
		$id     = $this->getRequest()->getParam('groupdeals_id');
		$model  = Mage::getModel('groupdeals/groupdeals')->load($id);

		if ($model->getId() || $id == 0) {					
			$product = $this->_initProduct();
			 
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);			
			}
			
			Mage::register('groupdeals_data', $model);

			$this->loadLayout();
			$this->_title(false)->_title($this->__('New Deal'));
			$this->_setActiveMenu('groupdeals/add');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal Information'), Mage::helper('adminhtml')->__('Deal Information'));
			
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit'))
				->_addLeft($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tabs'));
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Deal does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function columnEditAction() {
		$id     = $this->getRequest()->getParam('groupdeals_id');
		$model  = Mage::getModel('groupdeals/groupdeals')->load($id);
		$this->_redirect('*/*/edit', array('groupdeals_id' => $id, 'id' => $model->getProductId()));
	}
 
	public function setMainAction() {
		$old_deal = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));		
		$data = $old_deal->getData();
		
		$new_deal = Mage::getModel('groupdeals/groupdeals')->setData($data)->setId()->save();	
		// Update url rewrite		
		$productId = $new_deal->getProductId();
		$groupdealsId = $old_deal->getId();
		$stores = Mage::app()->getStores();		
		foreach ($stores as $_eachStoreId => $val) 
		{
			$store = Mage::app()->getStore($_eachStoreId);			
			if ($store->getRootCategoryId()) {
				$_storeId = $store->getId();
				$product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($productId);
				
				Mage::getSingleton('catalog/url')->refreshProductRewrite($productId, $_storeId);
				$productUrlRewriteId = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$groupdealsId)->getFirstItem()->getId();					
				if ($productUrlRewriteId!='') {
					Mage::getModel('core/url_rewrite')->load($productUrlRewriteId)->setTargetPath('groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$new_deal->getId())->save();
				}		
				
				if (count($product->getCategoryIds())>0) {
					foreach ($product->getCategoryIds() as $categoryId) {
						$categoryUrlRewriteId = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$groupdealsId)->getFirstItem()->getId();					
						if ($categoryUrlRewriteId!='') {
							Mage::getModel('core/url_rewrite')->load($categoryUrlRewriteId)->setTargetPath('groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$new_deal->getId())->save();
						}	
					}										 
				}											 
			}										 
		} 
			
		$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('groupdeals_id', $groupdealsId);
		if (count($couponsCollection)>0) {
			foreach ($couponsCollection as $coupon) {
				$coupon->setGroupdealsId($new_deal->getId())->save();
			}
		}
		
		$notificationsCollection = Mage::getModel('groupdeals/notifications')->getCollection()->addFieldToFilter('groupdeals_id', $groupdealsId);
		if (count($notificationsCollection)>0) {
			foreach ($notificationsCollection as $notification) {
				$notification->setGroupdealsId($new_deal->getId())->save();
			}
		}
	
		$old_deal->delete();
		
		Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();
		
		$this->_redirect('*/*/');
	}
	
	public function editAction() {
		if (Mage::getSingleton('core/session')->getGroupdealsRefresh()!='') {
			Mage::getModel('groupdeals/groupdeals')->updateAfterSave();
		}
		$id     = $this->getRequest()->getParam('groupdeals_id');
		$model  = Mage::getModel('groupdeals/groupdeals')->load($id);

		if ($model->getId() || $id == 0) {					
			$product = $this->_initProduct();
			 
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);			
			}
			
			Mage::register('groupdeals_data', $model);

			$this->loadLayout();
			$this->_title(false)->_title($this->__($product->getName()));
			$this->_setActiveMenu('groupdeals/add');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal Information'), Mage::helper('adminhtml')->__('Deal Information'));
			
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit'))
				->_addLeft($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tabs'));

			if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
				$switchBlock->setDefaultStoreName($this->__('Default Values'))
					->setWebsiteIds($product->getWebsiteIds())
					->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null)));
			}
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Deal does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function regionAction()
    {
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_field_region')->toHtml()
        );		
    }	

    /**
     * Get super config grid
     *
     */
    public function superConfigAction()
    {
        $this->_initProduct();
        $this->loadLayout(false);
        $this->renderLayout();
    }
	
    public function notificationsAction()
    {
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_notifications')->toHtml()
        );		
    }
	
    public function ordersAction()
    {
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_orders')->toHtml()
        );		
    }   

	public function redeemAction()
    {
		$coupon_id = $this->getRequest()->getParam('coupon_id');
		$storeId = $this->getRequest()->getParam('store', 0);
		if ($coupon_id) {
			try {
				$coupon = Mage::getModel('groupdeals/coupons')->load($coupon_id)->setRedeem('used')->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The "%s" Coupon has been redeemed.', $coupon->getCouponCode()));
				$this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store'=>$storeId));
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				
                $this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store'=>$storeId));
                return;
            }
		}
    }
	
	public function saveAction() {
        $storeId = $this->getRequest()->getParam('store', 0);
		if ($data = $this->getRequest()->getPost()) {
			/* $this->getRequest()->setPost('product', array('description' => $data['product']['groupdeal_fineprint'], 'short_description' => $data['product']['groupdeal_highlights'], 'url_key' => false)); */
            $product = $this->_initProductSave();
			$data['coupon_expiration_date'] = $this->getRequest()->getPost('coupon_expiration_date');
			if (!isset($data['coupon_expiration_date']) || $data['coupon_expiration_date']=='') {
				$data['coupon_expiration_date'] = '0000-00-00';
			}
			$data['country_id'] = $this->getRequest()->getPost('country_id');
			if (!isset($data['country_id']) || $data['country_id']=='') {
				$data['country_id'] = '';
				$data['region'] = '';
				$data['city'] = 'Universal';
			}
			$data = $this->_filterPostData($data);  	
			
			try {				
				$product->save();
                $productId = $product->getId();				
								
                // do copying data to stores
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }	
				
				// update deal data
				$data['product_id'] = $productId;			
				
				// Upload/Delete Barcode image
				$data['coupon_barcode'] = $this->getRequest()->getPost('coupon_barcode');
				if (!isset($_FILES['coupon_barcode']['name'])) {
					$_FILES['coupon_barcode']['name'] = '';
				}
				if($_FILES['coupon_barcode']['name'] != '') {
					try {    
						 $uploader = new Varien_File_Uploader('coupon_barcode');
						 $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						 $uploader->setAllowRenameFiles(false);
						 $uploader->setFilesDispersion(false);
					 
						 $path = Mage::getBaseDir('media') . DS . 'groupdeals/';
								
						 $uploader->save($path, $_FILES['coupon_barcode']['name']);
					} catch (Exception $e) {
						  
					}
					$data['coupon_barcode'] = 'groupdeals/'.$_FILES['coupon_barcode']['name'];
				} elseif(!isset($data['coupon_barcode']['delete'])) {			
					$data['coupon_barcode'] = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'))->getCouponBarcode();			
				} else {
					$data['coupon_barcode'] = '';
				}
				
				
				// Save Groupdeals Data
				$model = Mage::getModel('groupdeals/groupdeals');	
				
				$model->setData($data)
					->setId($this->getRequest()->getParam('groupdeals_id'));
				$model->save();				
				
                //Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($productId);		
				Mage::getSingleton('core/session')->setGroupdealsStoreId($storeId);
				Mage::getSingleton('core/session')->setGroupdealsId($model->getId());
				Mage::getSingleton('core/session')->setGroupdealsRefresh($productId);
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully saved'));
				
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('groupdeals_id' => $model->getId(), 'id' => $productId, 'store'=>$storeId));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                Mage::getSingleton('adminhtml/session')->setProductData($data);
				
                $this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store'=>$storeId));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find deal to save'));
        $this->_redirect('*/*/');
	}
		
	public function previewAction() {		
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_preview')->toHtml()
        );		
	}
	
	public function pdfPreviewAction() {
				
		$groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', array('eq' => $this->getRequest()->getParam('id')))->getFirstItem();
        $storeId = $this->getRequest()->getParam('store', 0);
        $this->_locale = new Zend_Locale(Mage::getStoreConfig('general/locale/code', $storeId));
		$localeCode = $this->_locale->toString();
		
		$pdf_html = Mage::getModel('groupdeals/coupons')->getCouponPdfHtml($groupdeals, null, null, 'JOHN DOE', $storeId);
		
		require_once("dompdf/dompdf_config.inc.php");
		spl_autoload_register('DOMPDF_autoload'); 
							
		$dompdf = new DOMPDF();
		$dompdf->set_paper("a4", "portrait"); 
		$dompdf->load_html(utf8_decode($pdf_html));
		$dompdf->render(); 
		
		return $dompdf->stream("coupon_preview_".$localeCode.".pdf");									
				
	}
 
	public function emailAction() {
		if( $this->getRequest()->getParam('groupdeals_id') > 0 ) {
			try {
				$groupdeals = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));	
			
				$start_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($groupdeals->getDatetimeFrom());
				$end_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($groupdeals->getDatetimeTo());
				$items_collection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('created_at', array("from" =>  $start_date_time, "to" =>  $end_date_time, "datetime" => true))->addFieldToFilter('product_id', $groupdeals->getProductId());
			
				if (count($items_collection)>0) {
					foreach ($items_collection as $item) {
						$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId())->addFieldToFilter('status', 'pending');
						if (count($couponsCollection)>0) {		
							foreach ($couponsCollection as $coupon) {
								$coupon->setStatus('sending')->save();
							}
						}	
					}	
					
					Mage::getModel('groupdeals/coupons')->email();
					
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Coupons are being emailed.'));
				} else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('No orders were placed for this deal.'));				
				}

				$this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id')));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					
				$this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find deal for which to send coupons'));
        $this->_redirect('*/*/');
	}
	
	public function emailCouponAction() {
		if( $this->getRequest()->getParam('order_id') > 0 ) {
			try {
				$order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));	
				$groupdeals = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));	
				$items_collection = $order->getItemsCollection();
			
				if (count($items_collection)>0) {
					$coupon_sent = false;
					foreach ($items_collection as $item) {
						$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId())->addFieldToFilter('status', 'complete')->addFieldToFilter('groupdeals_id', $groupdeals->getId());
						if (count($couponsCollection)>0) {		
							foreach ($couponsCollection as $coupon) {
								$coupon->setStatus('sending')->save();
							}
							$coupon_sent = true;
						}	
					}	
					
					Mage::getModel('groupdeals/coupons')->email();
					if ($coupon_sent) {
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Coupon(s) sent.'));
					} else {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Order hasn\'t been invoiced.'));				
					}
				}

				$this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store' => $this->getRequest()->getParam('store', 0)));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					
				$this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store' => $this->getRequest()->getParam('store', 0)));
				return;
			}
		}
		
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find deal for which to send coupons'));
        $this->_redirect('*/*/');
	}	
 
	public function emailMerchantAction() {
		if( $this->getRequest()->getParam('groupdeals_id') > 0 ) {
			try {
				$groupdeals = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));	
				$merchant = Mage::getModel('groupdeals/merchants')->getCollection()->addFieldToFilter('merchants_id', array('eq' => $groupdeals->getMerchantId()))->getFirstItem();
				$merchant_email = Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getEmail(), 0);
				$product_name = Mage::getModel('catalog/product')->setStoreId(0)->load($groupdeals->getProductId())->getName();	
				$sender = Mage::getStoreConfig('groupdeals/notifications/email_sender');
				$reply_to = Mage::getStoreConfig('trans_email/ident_'.$sender.'/email');
			
				$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('groupdeals_id', $groupdeals->getId());
				if (count($couponsCollection)>0) {
					$content    = $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_orders')->getCsv();
					
					$email_data['name'] = $product_name;
				
					$postObject = new Varien_Object();
					$postObject->setData($email_data);	
									
					$mailTemplate = Mage::getModel('core/email_template');
					
						
					$this->addCsvAttachment($mailTemplate, str_replace('</br>',' // ',$content), ''.$product_name.' Coupons.csv');		
						
					$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->setReplyTo($reply_to)
						->sendTransactional(
							'groupdeals_notifications_email_merchant_template',
							$sender , 
							$merchant_email,
							null,
							array('data' => $postObject)
						);						
					
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Coupons CSV Emailed.'));
				} else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('No coupons were sent for this deal.'));				
				}

				$this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id')));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					
				$this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find deal for which to send coupons'));
        $this->_redirect('*/*/');
	}		
 
	public function addCsvAttachment($mailTemplate, $rFile, $sFilename) {
		$attachment = $mailTemplate->getMail()->createAttachment($rFile);
		$attachment->type = 'application/csv';
		$attachment->filename = $sFilename;
	}
	
	public function deleteAction() {
		if( $this->getRequest()->getParam('groupdeals_id') > 0 ) {
			try {
				$groupdeals = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));
				$productId  = (int) $groupdeals->getProductId();	
				$product = Mage::getModel('catalog/product')->load($productId);
				$product->delete();
				
				$model = Mage::getModel('groupdeals/groupdeals');
				 
				$model->setId($this->getRequest()->getParam('groupdeals_id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id')));
			}
		}
		Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();
		
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
		$productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $groupdeals = Mage::getSingleton('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', $productId)->getFirstItem();
						$product = Mage::getSingleton('catalog/product')->load($productId);
                        Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $groupdeals->delete();
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
		Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();
		
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
		$productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($productIds, $status);
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, array('status' => $status), $storeId);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the deal(s) status.'));
        }
		Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();
		
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'groupdeals.csv';
        $content    = $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, strip_tags($content));
    }
  
    public function exportOrdersCsvAction()
    {
        $fileName   = $this->getRequest()->getParam('csv_excel_name').' Coupons.csv';
        $content    = $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_orders')
            ->getCsv();

        $this->_sendUploadResponse($fileName, strip_tags('</br>',' // ',$content));
        //$this->_sendUploadResponse($fileName, strip_tags(str_replace('Email Coupon(s)','',str_replace('Action','',str_replace('View || Redeem','',str_replace('View Order','',str_replace('</br>',' // ',$content)))))));
    }
  
    /* public function exportOrdersExcelAction()
    {
        $fileName   = $this->getRequest()->getParam('csv_excel_name').' Coupons.xml';
        $content    = $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_orders')
            ->getExcelFile($fileName);

        $this->_sendUploadResponse($fileName, $content);
    } */

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }	
	
	protected function _filterPostData($data)
    {
		if ($data['coupon_expiration_date']!='') {
			$data = $this->_filterDatesCustom($data, array('datetime_from', 'datetime_to', 'coupon_expiration_date'));
		} else {
			$data = $this->_filterDatesCustom($data, array('datetime_from', 'datetime_to'));
		}
        return $data;
    }
	
	protected function _filterDatesCustom($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
		
        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $this->LocalizedToNormalized($array[$dateField]);
                $array[$dateField] = $this->NormalizedToLocalized($array[$dateField]);
            }
        }
        return $array;
    }
	
	public function LocalizedToNormalized($value)
    {
		if (substr(Mage::app()->getLocale()->getLocaleCode(),0,2)!='en') {
		    $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
		  		Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
		    );
	    } else {		
		    $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
		  		Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
		    );
	    }
	
		$_options = array(
			'locale'      => Mage::app()->getLocale()->getLocaleCode(),
			'date_format' => $dateFormatIso,
			'precision'   => null
		);
        return Zend_Locale_Format::getDate($value, $_options);        
    }
	
	public function NormalizedToLocalized($value)
    {
        #require_once 'Zend/Date.php';
        $date = new Zend_Date($value, Mage::app()->getLocale()->getLocaleCode());
        return $date->toString('yyyy-MM-dd HH:mm:ss');       
    }
	
	public function uploadAction()
    {
        $result = array();
        try {
            $uploader = new Varien_File_Uploader('image');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save(
                Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath()
            );

            $result['url'] = Mage::getSingleton('catalog/product_media_config')->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';
            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain()
            );
        } catch (Exception $e) {
            //$result = array('error'=>$e->getMessage(), 'errorcode'=>$e->getCode());
        }

       // $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products');
    }
}