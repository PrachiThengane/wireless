<?php
class Devinc_Groupdeals_ProductController extends Mage_Core_Controller_Front_Action
{
	protected function _initProduct()
    {
        Mage::dispatchEvent('catalog_controller_product_init_before', array('controller_action'=>$this));
        $productId  = (int) $this->getRequest()->getParam('id');
		$groupdealsModel  = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));
		if ($groupdealsModel) {
			Mage::getSingleton('core/session')->setCity($groupdealsModel->getCity());		
		} elseif (isset($_GET['city'])) {
			Mage::getSingleton('core/session')->setCity($_GET['city']);
		}

        if (!$productId) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);

        if (!Mage::helper('catalog/product')->canShow($product)) {
            return false;
        }
        if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            return false;
        }

        Mage::register('current_product', $product);
        Mage::register('product', $product);

        try {
            Mage::dispatchEvent('catalog_controller_product_init', array('product'=>$product));
            Mage::dispatchEvent('catalog_controller_product_init_after', array('product'=>$product, 'controller_action' => $this));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $product;
    } 	
    
    protected function _initProductLayout($product)
    {
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();

        $update->addHandle('PRODUCT_TYPE_'.$product->getTypeId());
        $update->addHandle('PRODUCT_'.$product->getId());

        if ($product->getPageLayout()) {
            $this->getLayout()->helper('page/layout')
                ->applyHandle($product->getPageLayout());
        }

        $this->loadLayoutUpdates();


        $update->addUpdate($product->getCustomLayoutUpdate());

        $this->generateLayoutXml()->generateLayoutBlocks();

        if ($product->getPageLayout()) {
            $this->getLayout()->helper('page/layout')
                ->applyTemplate($product->getPageLayout());
        }

        $currentCategory = Mage::registry('current_category');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('product-'.$product->getUrlKey());
            if ($currentCategory instanceof Mage_Catalog_Model_Category) {
                $root->addBodyClass('categorypath-'.$currentCategory->getUrlPath())
                    ->addBodyClass('category-'.$currentCategory->getUrlKey());
            }
        }
        return $this;
    }
	
	public function redirectAction()
    {      
		$city = Mage::getStoreConfig('groupdeals/configuration/homepage_deals', Mage::app()->getStore()->getId());
		$groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', $city)->setOrder('groupdeals_id', 'desc');
		if (count($groupdeals_collection)>0) {
			foreach ($groupdeals_collection as $groupdeals) {	
				$product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($groupdeals->getProductId());
				$groupdeals_type = $product->getGroupdealType();
				if ($groupdeals_type==1) {		
					$this->_redirect($product->getUrlPath());
					return;
				}
			}
		}
		if ($city!='') {
			$this->_redirect('groupdeals/product/list/city/'.urlencode($city));
		} else {
			$this->_redirect(Mage::getStoreConfig('web/default/cms_home_page', Mage::app()->getStore()->getId()));
		}
		return;
	}
	
	public function viewAction()
    {
		$product = $this->_initProduct();
        if ($product && ($product->getGroupdealStatus()==1 || $product->getGroupdealStatus()==4)) {
		
			$groupdealsModel  = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));
			$storeId = Mage::app()->getStore()->getStoreId();
			Mage::register('groupdeals', $groupdealsModel);

            $this->_initProductLayout($product);
            $this->loadLayout();			
			
			// Meta tags
			$head = $this->getLayout()->getBlock('head');			
			if ($product->getMetaTitle()=='') {
				$head->setTitle($product->getName());
			} else {
				$head->setTitle($product->getMetaTitle());
			}
			
			if ($product->getMetaDescription()=='') {
				$head->setDescription(strip_tags(Mage::getModel('groupdeals/groupdeals')->getDecodeString($groupdealsModel->getHighlights(),$storeId)));
			} else {
				$head->setDescription($product->getMetaDescription());
			}
			if ($product->getMetaKeyword()!='') {
				$head->setKeywords($product->getMetaKeyword());
			}

			$city = Mage::getSingleton('core/session')->getCity();
			$groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', $city);
			if (isset($city) && $city!='') {			
				$this->getLayout()->getBlock('groupdeals_breadcrumbs')
					->addCrumb('home',
					array('label'=>Mage::helper('catalogsearch')->__('Home'),
						'title'=>Mage::helper('catalogsearch')->__('Home'),
						'link'=>Mage::getBaseUrl())
					)
					->addCrumb('groupdeals',
					array('label'=>Mage::helper('catalogsearch')->__('Group Deals'),
						'title'=>Mage::helper('catalogsearch')->__('Group Deals'),
						'link'=>Mage::getBaseUrl().'groupdeals/product/list/city/Universal/')
					);
				if (count($groupdealsCollection)!=0) {
				  $this->getLayout()->getBlock('groupdeals_breadcrumbs')
						->addCrumb('city',
						array('label'=>Mage::helper('catalogsearch')->__($city),
							'title'=>Mage::helper('catalogsearch')->__($city),
							'link'=>Mage::getBaseUrl().'groupdeals/product/list/city/'.urlencode($city))
						);
				}	
				$this->getLayout()->getBlock('groupdeals_breadcrumbs')
					->addCrumb('product',
					array('label'=>Mage::helper('catalogsearch')->__($product->getName()),
						'title'=>Mage::helper('catalogsearch')->__($product->getName()))
					);
			} else {
				$this->getLayout()->getBlock('groupdeals_breadcrumbs')
					->addCrumb('home',
					array('label'=>Mage::helper('catalogsearch')->__('Home'),
						'title'=>Mage::helper('catalogsearch')->__('Home'),
						'link'=>Mage::getBaseUrl())
					)
					->addCrumb('groupdeals',
					array('label'=>Mage::helper('catalogsearch')->__('Group Deals'),
						'title'=>Mage::helper('catalogsearch')->__('Group Deals'),
						'link'=>Mage::getBaseUrl().'groupdeals/product/list/city/Universal/')
					)
					->addCrumb('product',
					array('label'=>Mage::helper('catalogsearch')->__($product->getName()),
						'title'=>Mage::helper('catalogsearch')->__($product->getName()))
					);
			}
			
			$magento_version = (int)str_replace(".", "", Mage::getVersion());
			if ($magento_version>=1410) {	
				$this->initLayoutMessages(array('catalog/session', 'tag/session', 'checkout/session'));
			}
			
			$this->renderLayout();
        } else {
            if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
        }
    }
	
    public function galleryAction()
    {
        $product = $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function listAction()
    {      
		$this->loadLayout();
		
		if ($this->getRequest()->getParam('city')!='') {
			Mage::getSingleton('core/session')->setCity($this->getRequest()->getParam('city'));
		}
		$city = Mage::getSingleton('core/session')->getCity();
		if (isset($city) && $city!='') {			
			$this->getLayout()->getBlock('groupdeals_breadcrumbs')
				->addCrumb('home',
				array('label'=>Mage::helper('catalogsearch')->__('Home'),
					'title'=>Mage::helper('catalogsearch')->__('Home'),
					'link'=>Mage::getBaseUrl())
				)
				->addCrumb('groupdeals',
				array('label'=>Mage::helper('catalogsearch')->__('Group Deals'),
					'title'=>Mage::helper('catalogsearch')->__('Group Deals'),
					'link'=>Mage::getBaseUrl().'groupdeals/product/list/city/Universal/')
				)
				->addCrumb('city',
				array('label'=>Mage::helper('catalogsearch')->__($city),
					'title'=>Mage::helper('catalogsearch')->__($city))
				);
		}
		
		$magento_version = (int)str_replace(".", "", Mage::getVersion());
		if ($magento_version>=1410) {	
		    $this->initLayoutMessages(array('catalog/session', 'checkout/session'));
		}
		
		$this->renderLayout();      
    }
	
	public function recentAction()
    {      
		$this->loadLayout();
		
		if ($this->getRequest()->getParam('city')!='') {
			Mage::getSingleton('core/session')->setCity($this->getRequest()->getParam('city'));
		}
		$city = Mage::getSingleton('core/session')->getCity();
		if (isset($city) && $city!='') {			
			$this->getLayout()->getBlock('groupdeals_breadcrumbs')
				->addCrumb('home',
				array('label'=>Mage::helper('catalogsearch')->__('Home'),
					'title'=>Mage::helper('catalogsearch')->__('Home'),
					'link'=>Mage::getBaseUrl())
				)
				->addCrumb('groupdeals',
				array('label'=>Mage::helper('catalogsearch')->__('Group Deals'),
					'title'=>Mage::helper('catalogsearch')->__('Group Deals'),
					'link'=>Mage::getBaseUrl().'groupdeals/product/list/city/Universal/')
				)
				->addCrumb('city',
				array('label'=>Mage::helper('catalogsearch')->__($city),
					'title'=>Mage::helper('catalogsearch')->__($city))
				);
		}
		
		$magento_version = (int)str_replace(".", "", Mage::getVersion());
		if ($magento_version>=1410) {	
		    $this->initLayoutMessages(array('catalog/session', 'checkout/session'));
		}
		
		$this->renderLayout();      
    }
	
	public function upcomingAction()
    {      
		$this->loadLayout();
		
        if (!Mage::getStoreConfig('groupdeals/configuration/display_upcoming')) {
            $this->_redirect('*/*/list');
            return;
        }
        
		if ($this->getRequest()->getParam('city')!='') {
			Mage::getSingleton('core/session')->setCity($this->getRequest()->getParam('city'));
		}
		$city = Mage::getSingleton('core/session')->getCity();
		if (isset($city) && $city!='') {			
			$this->getLayout()->getBlock('groupdeals_breadcrumbs')
				->addCrumb('home',
				array('label'=>Mage::helper('catalogsearch')->__('Home'),
					'title'=>Mage::helper('catalogsearch')->__('Home'),
					'link'=>Mage::getBaseUrl())
				)
				->addCrumb('groupdeals',
				array('label'=>Mage::helper('catalogsearch')->__('Group Deals'),
					'title'=>Mage::helper('catalogsearch')->__('Group Deals'),
					'link'=>Mage::getBaseUrl().'groupdeals/product/list/city/Universal/')
				)
				->addCrumb('city',
				array('label'=>Mage::helper('catalogsearch')->__($city),
					'title'=>Mage::helper('catalogsearch')->__($city))
				);
		}
		
		$magento_version = (int)str_replace(".", "", Mage::getVersion());
		if ($magento_version>=1410) {	
		    $this->initLayoutMessages(array('catalog/session', 'checkout/session'));
		}
		
		$this->renderLayout();      
    }
	
	public function merchantAction()
    {      
		$this->loadLayout();
	
		$this->getLayout()->getBlock('groupdeals_breadcrumbs')
			->addCrumb('home',
			array('label'=>Mage::helper('catalogsearch')->__('Home'),
				'title'=>Mage::helper('catalogsearch')->__('Home'),
				'link'=>Mage::getBaseUrl())
			)
			->addCrumb('groupdeals',
			array('label'=>Mage::helper('catalogsearch')->__('Group Deals'),
				'title'=>Mage::helper('catalogsearch')->__('Group Deals'),
				'link'=>Mage::getBaseUrl().'groupdeals/product/list/city/Universal/')
			)
			->addCrumb('merchant',
			array('label'=>Mage::helper('catalogsearch')->__('Become a Merchant'),
				'title'=>Mage::helper('catalogsearch')->__('Become a Merchant'))
			);
			
		$this->renderLayout();      
    }
	
    public function nodealsAction()
    {
        Mage::getSingleton('core/session')->addError($this->__('There are no deals setup at the moment'));
		
        $this->_redirect('');
    }
	
    public function subscribeAction()
    {
		$storeId = Mage::app()->getStore()->getStoreId();
			
        if ($data = $this->getRequest()->getPost()) {	
        	$city = Mage::getSingleton('core/session')->getCity();
			if (!isset($data['city']) && isset($city) && $city!='') {
				$data['city'] = $city;
			} elseif (!isset($data['city'])) {
				Mage::getSingleton('core/session')->addError($this->__('Session expired. Please select a city.'));	
				$this->_redirect(''); 
				return;
			}
			$class = new Devinc_Groupdeals_Block_Popup; 
			$redirect_url = $class->getCityUrl($data['city']);
			
			$data['store_id'] = $storeId;
			$subscribersCollection = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('email', $data['email'])->addFieldToFilter('store_id', $data['store_id'])->addFieldToFilter('city', $data['city']);				
			if (count($subscribersCollection)==0 && $data['email']!='' && $data['city']!='' && $data['store_id']!='') {		
				$model = Mage::getModel('groupdeals/subscribers');	
					
				$model->setData($data);			
				$model->save();		
				Mage::getSingleton('catalog/session')->addSuccess($this->__('Thank you for your subscription.'));	
			} else {
				Mage::getSingleton('catalog/session')->addError($this->__('You are already subscribed to this city.'));	
			}
			$this->_redirectUrl($redirect_url);
			return;
        } 
        $this->_redirect('');
    } 
}