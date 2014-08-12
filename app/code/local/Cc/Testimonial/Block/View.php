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
class Cc_Testimonial_Block_View extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        //$route = Mage::helper('news')->getRoute(); 
        $isNewsPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'testimonial';
        
        // show breadcrumbs
        if ($isNewsPage && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
                $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('blog')->__('Home'), 'title'=>Mage::helper('blog')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));;
                $breadcrumbs->addCrumb('testimonial', array('label'=>'Testimonial', 'title'=>Mage::helper('testimonial')->__('Return to testimonial'), 'link'=>Mage::getUrl('testimonial')));
        }
                        
        return parent::_prepareLayout();   
    }
    
    public function getTestimonial()
       {
        
        $_testimonial  =   Mage::getModel('testimonial/testimonial')
                ->  setStoreId(Mage::app()->getStore()->getId())
                ->  load($this->getRequest()->getParam('id'), 'testimonial_id');
 
        /*$_news  ->  setTitle($_news->getTitle())
                ->  setContent($_news->getDescripton())
                ->  setCreatedTime($this->formatTime($_news->getCreatedTime(),'DDMMYYYY', true))
                ->  setUpdateTime($this->formatTime($_news->getUpdateTime(),'DDMMYYYY', true));*/
        
        //$this->setData('news', $_news);       
        return $_testimonial;
    }
}
