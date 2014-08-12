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
class Cc_Testimonial_Block_Page extends Mage_Page_Block_Html_Pager
{
   /**
     * List of available view types
     *
     * @var string
     */
    protected $_availableMode       = array();
        
    protected function _construct()
    {
        parent::_construct();

        $this->_availableMode = array('list' => $this->__('List'));
        $this->setTemplate('page/html/pager.phtml');
    }
    
    public function setCollection($collection)
    {
        parent::setCollection($collection);
        return $this;
    }
    
    public function getModes()
    {
        return $this->_availableMode;
    }

    /**
     * Set available view modes list
     *
     * @param array $modes
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function setModes($modes)
    {
        if(!isset($this->_availableMode)){
            $this->_availableMode = $modes;
        }
        return $this;
    }
}
?>
