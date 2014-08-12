<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getUrl($image)
    {
        return Mage::getBaseUrl('media').'aitmanufacturers' . DS . $image;
    }
    
	public function getIconUrl($image)
    {
        return Mage::getBaseUrl('media').'aitmanufacturers' . DS . 'list' . DS . $image;
    }
    
    public function init($image)
    {
        $this->image = $image;
        $this->path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS;
        $this->_processor = new Varien_Image($this->path.$this->image);
         $this->_processor->keepAspectRatio(true);
        /*$this->_processor->keepFrame(true);
        $this->_processor->keepTransparency($this->_keepTransparency);
        $this->_processor->constrainOnly($this->_constrainOnly);
        $this->_processor->backgroundColor($this->_backgroundColor);*/
         return $this;
    }
    
    public function resize($width, $height = null)
    {
         //$this->_processor->setWidth($width)->setHeight($height);
         $this->_processor->resize($width, $height);
         return $this->_processor;
    }
}
