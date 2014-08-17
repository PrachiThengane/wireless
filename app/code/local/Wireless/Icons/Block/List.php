<?php
class Wireless_Icons_Block_List extends Mage_Core_Block_Template
{
    private $icon_img_file_path = "app/code/local/Wireless/Icons/etc/icon_imgs.xml";
    
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getListIconData($product) {
        
        $xml=simplexml_load_file($this->icon_img_file_path);
       // var_dump($xml);
        //die();
        
    }
}
