<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout(array('default', 'aitmanufacturers_index_list'));     
        $this->renderLayout();
    }
    
    public function viewAction(){
        $id = $this->getRequest()->getParam('id');
        if (!Mage::helper('aitmanufacturers/manufacturer')->renderPage($this, $id)) {
//            $this->_forward('defaultIndex', 'index', 'cms');
            $this->_forward('index', 'index', 'aitmanufacturers');
        }
    }

}
