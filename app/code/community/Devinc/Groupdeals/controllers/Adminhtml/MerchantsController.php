<?php

class Devinc_Groupdeals_Adminhtml_MerchantsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('groupdeals/merchants')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Merchants Manager'), Mage::helper('adminhtml')->__('Merchants Manager'));	
			
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}	
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('groupdeals/merchants')->load($id);

		if ($model->getId() || $id == 0) {								
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);			}
			
			Mage::register('groupdeals_data', $model);

			$this->loadLayout();
			$headBlock = Mage::getSingleton('core/layout')->getBlock('head');  
			$headBlock->addCss('PATH/TO/CSS/FILE.CSS'); 
			$this->_setActiveMenu('groupdeals');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Merchant Information'), Mage::helper('adminhtml')->__('Merchant Information'));
			
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit'))
				->_addLeft($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_tabs'));

				
			if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
				$switchBlock->setDefaultStoreName($this->__('Default Values'))
					->setWebsiteIds(array(1))
					->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null)));
			}
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Merchant does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('groupdeals/merchants')->load($id);

		if ($model->getId() || $id == 0) {								
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);			}
			
			Mage::register('groupdeals_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('groupdeals');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Merchant Information'), Mage::helper('adminhtml')->__('Merchant Information'));
			
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit'))
				->_addLeft($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_tabs'));
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Merchant does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function accountAction() {
		if($merchant_id = Mage::getModel('groupdeals/merchants')->isMerchant()){
	    	$this->_forward('edit', null, null, array('id' => $merchant_id));
	    } else {
	    	$this->_redirect('groupdeals/adminhtml_groupdeals/');
	    }
	}
	
	public function saveAction() {
        $storeId = $this->getRequest()->getParam('store', 0);
		if ($data = $this->getRequest()->getPost()) {		
	  			
			$model = Mage::getModel('groupdeals/merchants');
			$address = '';
			for ($i = 1; $i<=5; $i++) {
				if ($data['address_'.$i]!='') {
					$address .= $data['address_'.$i].'_;_';
				}
			}
			$data['address'] = substr($address,0,-3);
			
			if (!isset($data['name_default'])) $data['name_default'] = 0;
			if (!isset($data['description_default'])) $data['description_default'] = 0;
			if (!isset($data['website_default'])) $data['website_default'] = 0;
			if (!isset($data['email_default'])) $data['email_default'] = 0;
			if (!isset($data['facebook_default'])) $data['facebook_default'] = 0;
			if (!isset($data['twitter_default'])) $data['twitter_default'] = 0;
			if (!isset($data['phone_default'])) $data['phone_default'] = 0;
			if (!isset($data['mobile_default'])) $data['mobile_default'] = 0;
			if (!isset($data['business_hours_default'])) $data['business_hours_default'] = 0;
			if (!isset($data['redeem_default'])) $data['redeem_default'] = 0;			
			
			if ($this->getRequest()->getParam('id')!='' && $this->getRequest()->getParam('id')!=0) {
				$name = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getName();	
				$description = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getDescription();	
				$website = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getWebsite();	
				$email = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getEmail();	
				$facebook = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getFacebook();	
				$twitter = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getTwitter();	
				$phone = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getPhone();	
				$mobile = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getMobile();	
				$business_hours = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getBusinessHours();	
				$redeem = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getRedeem();	
			} else {
				$name = '';
				$description = '';
				$website = '';
				$email = '';
				$facebook = '';
				$twitter = '';
				$phone = '';
				$mobile = '';
				$business_hours = '';
				$redeem = '';
			}
			
			if ($data['name']!='' || $data['name_default']==1) {
				$name_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($name);	
				if ($data['name_default']!=1) {
					$name_array[$storeId] = $data['name'];
				} else {
					unset($name_array[$storeId]);
				}
				$data['name'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($name_array);
			}
			
			if ($data['description']!='' || $data['description_default']==1) {
				$description_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($description);	
				if ($data['description_default']!=1) {
					$description_array[$storeId] = $data['description'];
				} else {
					unset($description_array[$storeId]);
				}
				$data['description'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($description_array);	
			}
			
			if ($data['website']!='' || $data['website_default']==1) {
				$website_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($website);	
				if ($data['website_default']!=1) {
					$website_array[$storeId] = $data['website'];
				} else {
					unset($website_array[$storeId]);
				}
				$data['website'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($website_array);	
			}
			
			if ($data['email']!='' || $data['email_default']==1) {
				$email_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($email);	
				if ($data['email_default']!=1) {
					$email_array[$storeId] = $data['email'];
				} else {
					unset($email_array[$storeId]);
				}
				$data['email'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($email_array);	
			}
				
			if ($data['facebook']!='' || $data['facebook_default']==1) {
				$facebook_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($facebook);	
				if ($data['facebook_default']!=1) {
					$facebook_array[$storeId] = $data['facebook'];
				} else {
					unset($facebook_array[$storeId]);
				}
				$data['facebook'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($facebook_array);	
			}
			
			if ($data['twitter']!='' || $data['twitter_default']==1) {
				$twitter_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($twitter);	
				if ($data['twitter_default']!=1) {
					$twitter_array[$storeId] = $data['twitter'];
				} else {
					unset($twitter_array[$storeId]);
				}
				$data['twitter'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($twitter_array);	
			}
			
			if ($data['phone']!='' || $data['phone_default']==1) {
				$phone_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($phone);	
				if ($data['phone_default']!=1) {
					$phone_array[$storeId] = $data['phone'];
				} else {
					unset($phone_array[$storeId]);
				}
				$data['phone'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($phone_array);	
			}
			
			if ($data['mobile']!='' || $data['mobile_default']==1) {
				$mobile_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($mobile);	
				if ($data['mobile_default']!=1) {
					$mobile_array[$storeId] = $data['mobile'];
				} else {
					unset($mobile_array[$storeId]);
				}
				$data['mobile'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($mobile_array);
			}
			
			if ($data['business_hours']!='' || $data['business_hours_default']==1) {
				$business_hours_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($business_hours);	
				if ($data['business_hours_default']!=1) {
					$business_hours_array[$storeId] = $data['business_hours'];
				} else {
					unset($business_hours_array[$storeId]);
				}
				$data['business_hours'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($business_hours_array);
			}
			
			if ($data['redeem']!='' || $data['redeem_default']==1) {
				$redeem_array = Mage::getModel('groupdeals/groupdeals')->getDecodeString($redeem);	
				if ($data['redeem_default']!=1) {
					$redeem_array[$storeId] = $data['redeem'];
				} else {
					unset($redeem_array[$storeId]);
				}
				$data['redeem'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($redeem_array);	
			}
						
			try {
				// Upload/Delete Barcode image
				$data['merchant_logo'] = $this->getRequest()->getPost('merchant_logo');
				if (!isset($_FILES['merchant_logo']['name'])) {
					$_FILES['merchant_logo']['name'] = '';
				}
				if($_FILES['merchant_logo']['name'] != '') {
					try {    
						 $uploader = new Varien_File_Uploader('merchant_logo');
						 $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						 $uploader->setAllowRenameFiles(false);
						 $uploader->setFilesDispersion(false);
					 
						 $path = Mage::getBaseDir('media') . DS . 'groupdeals/merchants/logo/';
								
						 $uploader->save($path, $_FILES['merchant_logo']['name']);
					} catch (Exception $e) {
						  
					}
					$data['merchant_logo'] = 'groupdeals/merchants/logo/'.$_FILES['merchant_logo']['name'];
				} elseif(!isset($data['merchant_logo']['delete'])) {			
					$data['merchant_logo'] = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getMerchantLogo();			
				} else {
					$data['merchant_logo'] = '';
				}
				
				$permissions = array();
				if (isset($data['merchant_info'])) {
					$permissions['merchant_info'] = 1;
				} else {
					$permissions['merchant_info'] = 0;				
				}
				if (isset($data['add_edit'])) {
					$permissions['add_edit'] = 1;
				} else {
					$permissions['add_edit'] = 0;				
				}
				if (isset($data['approve'])) {
					$permissions['approve'] = 1;
				} else {
					$permissions['approve'] = 0;				
				}
				if (isset($data['delete'])) {
					$permissions['delete'] = 1;
				} else {
					$permissions['delete'] = 0;				
				}
				if (isset($data['sales'])) {
					$permissions['sales'] = 1;
				} else {
					$permissions['sales'] = 0;				
				}
				
				$data['permissions'] = Mage::getModel('groupdeals/groupdeals')->getEncodeString($permissions);
				if ($data['username']!='' && isset($data['password']) && $data['password']=='') {
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					Mage::getSingleton('adminhtml/session')->addError('Please enter a Password.');
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$storeId));			
					return $this;		
				}
				
				if ($data['username']!='') {					
					if ($data['user_id']!='' && $data['user_id']!=0) {
						$userModel = Mage::getModel('admin/user')->load($data['user_id']);
						$userData['user_id'] = $data['user_id'];
					} else {
						$userModel = Mage::getModel('admin/user'); 
						$userData['user_id'] = null;
					}
					$userData['username'] = $data['username'];
					$userData['password'] = $data['password'];
					if (isset($data['new_password'])) {
						$userData['new_password'] = $data['new_password'];
					}
					$userData['email'] = Mage::getModel('groupdeals/groupdeals')->getDecodeString($data['email'],0);
					$userData['firstname'] = 'merchant';
					$userData['lastname'] = 'merchant';
					$userData['is_active'] = $data['is_active'];
					$userModel->setData($userData);

					/*
					 * Unsetting new password and password confirmation if they are blank
					 */
					if ($userModel->hasNewPassword() && $userModel->getNewPassword() === '') {
						$userModel->unsNewPassword();
					}
					if ($userModel->hasPasswordConfirmation() && $userModel->getPasswordConfirmation() === '') {
						$userModel->unsPasswordConfirmation();
					}
					
					$result = $userModel->validate();
					if (is_array($result)) {
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						foreach ($result as $message) {
							Mage::getSingleton('adminhtml/session')->addError($message);
						}
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$storeId));
						return $this;
					}
					$userModel->save();					
					$data['user_id'] = $userModel->getId();
					
					$tempRole = Mage::getModel('admin/role')->getCollection()->addFieldToFilter('user_id', $data['user_id'])->getFirstItem();
					if ($tempRole->getId()) {
						$role = Mage::getModel('admin/roles')->load($tempRole->getParentId());
					} else {
						$role = Mage::getModel('admin/roles');
					}

					$roleName = Mage::getModel('groupdeals/groupdeals')->getDecodeString($data['name'], 0);
					$role->setName($roleName.' Role')
						 ->setPid(false)
						 ->setRoleType('G') 
						 ->save();
					
					$resources[] = false;
					if ($permissions['add_edit']==1 || $permissions['delete']==1 || $permissions['sales']==1) {
						$resources[] = 'admin/catalog/products';
						$resources[] = 'admin/groupdeals';
						$resources[] = 'admin/groupdeals/items';
					}
					
					if ($permissions['add_edit']==1) {
						$resources[] = 'admin/groupdeals/add';
						$resources[] = 'admin/cms/media_gallery';
					}

					if ($permissions['merchant_info']==1) {
						$resources[] = 'admin/groupdeals/merchant_info';
					}
					
					Mage::getModel('admin/rules')
						->setRoleId($role->getId())
						->setResources($resources)
						->saveRel();
						
					$user = Mage::getModel('admin/user')->load($data['user_id']);
					$user->setRoleId($role->getId())->setUserId($data['user_id']);
					if($user->roleUserExists() == false) {
						$user->add();
					}					
				}
				
				$model->setData($data)
					->setId($this->getRequest()->getParam('id'));
					
				$model->save();	 
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Merchant was successfully saved'));

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId(), 'store'=>$storeId));
					return;
				}
				$this->_redirect('*/*/', array('store'=>$storeId));
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store'=>$storeId));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find merchant to save'));
        $this->_redirect('*/*/', array('store'=>$storeId));
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$user_id = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'))->getUserId();
				if ($user_id!=0) {
					$role_id = Mage::getModel('admin/role')->getCollection()->addFieldToFilter('user_id', $user_id)->getFirstItem()->getParentId();
					$roleModel = Mage::getModel('admin/role');
					$roleModel->setId($role_id);
					$roleModel->delete();
					
					$userModel = Mage::getModel('admin/user');
					$userModel->setId($user_id);
					$userModel->delete();
				}
				
				$model = Mage::getModel('groupdeals/merchants');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Merchant was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $groupdealsIds = $this->getRequest()->getParam('groupdeals');
        if(!is_array($groupdealsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select merchant(s)'));
        } else {
            try {
                foreach ($groupdealsIds as $groupdealsId) {
                    $groupdeals = Mage::getModel('groupdeals/merchants')->load($groupdealsId);
                    $groupdeals->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d merchant(s) were successfully deleted', count($groupdealsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $groupdealsIds = $this->getRequest()->getParam('groupdeals');
        if(!is_array($groupdealsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select merchant(s)'));
        } else {
            try {
                foreach ($groupdealsIds as $groupdealsId) {
                    $groupdeals = Mage::getSingleton('groupdeals/merchants')
                        ->load($groupdealsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d merchant(s) were successfully updated', count($groupdealsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'merchants.csv';
        $content    = $this->getLayout()->createBlock('groupdeals/adminhtml_merchants_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, strip_tags($content));
    }  	

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
}