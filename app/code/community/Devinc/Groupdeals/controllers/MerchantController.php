<?php
class Devinc_Groupdeals_MerchantController extends Mage_Core_Controller_Front_Action
{	
	public function subscribeAction()
    {  
        if (Mage::getStoreConfig('groupdeals/merchants_subscribe/enabled')) {
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
					'link'=>"javascript:openGroupdealsPopup();")
				)
				->addCrumb('merchant',
				array('label'=>Mage::helper('catalogsearch')->__('Become a Merchant'),
					'title'=>Mage::helper('catalogsearch')->__('Become a Merchant'))
				);
				
			$this->renderLayout(); 
		} else {
			$this->_redirect('no-route');		
		}  
    }
    
    public function postAction()
    {
		$post = $this->getRequest()->getPost();
        if ($post)  {
            try {
            	$post['status'] = 3;  
            	$address = '';
				for ($i = 1; $i<=5; $i++) {
					if ($post['address_'.$i]!='') {
						$address .= $post['address_'.$i].'_;_';
					}
				}
				$post['address'] = substr($address,0,-3);    
				               
                Mage::getModel('groupdeals/merchants')->setData($post)->save();
                
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('groupdeals')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/subscribe');

                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/subscribe');
                return;
            }

        } else {
            $this->_redirect('*/*/subscribe');
        }

    }
	
}