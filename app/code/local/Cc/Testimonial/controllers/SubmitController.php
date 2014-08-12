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
/**
 * Testimonial submit controller
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Cc_Testimonial_SubmitController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle('Testimonial submit');                                               
        }           
             
        $this->renderLayout();
    }
    
    public function postAction()
    {

        $data = $this->getRequest()->getPost();
        //var_dump($_FILES);exit;
        if (!empty($data)) {
            $session = Mage::getSingleton('core/session', array('name'=>'frontend'));
            /* @var $session Mage_Core_Model_Session */
        	if(isset($_FILES['media']['name']) && $_FILES['media']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('media');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','avi','flv','swf','mp3','mp4'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media').DS.'testimonial'.DS;
					$result= $uploader->save($path, $_FILES['media']['name'] );
					
					//$data['media'] = 'testimonial/'. $result['file'];
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['media'] = 'testimonial/'.$_FILES['media']['name'];
			}
            
            // Set store view
            if (!Mage::app()->isSingleStoreMode()) {
            	$data['stores'] = array(Mage::app()->getStore()->getId());
            }
            
            $testimonial     = Mage::getModel('testimonial/testimonial')->setData($data);
			
            /* @var $review Mage_Review_Model_Review */
                        
            // set CreatedTime and UpdateTime value:
        	if ($testimonial->getCreatedTime == NULL || $testimonial->getUpdateTime() == NULL) {
				$testimonial->setCreatedTime(now())
					->setUpdateTime(now());
			} else {
				$testimonial->setUpdateTime(now());
			}
			
            $validate = $testimonial->validate();
            if ($validate === true) {
                try {
                    $testimonial->save();
                    $session->addSuccess($this->__('Your testimonial has been accepted for moderation'));
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
                }
            }
            else {
                try{
                $session->setFormData($data);
                }
                catch(Exception $e){
                    Mage::log($e->getMessage());
                }                  
                if (is_array($validate)) {                   
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }                 
                }
                else {
                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
                }
            }
        }

        if ($redirectUrl = Mage::getSingleton('core/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
    }
}
