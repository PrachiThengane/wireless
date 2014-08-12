<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Hometabspro
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */?>
<?php
class AW_Hometabspro_Block_Tabs extends Mage_Core_Block_Template
{
	const TEMPLATE_PATH = 'hometabspro/tabs.phtml';
	protected $_items = null;
	
	public function __construct()
	{				
		parent::__construct();
		$this->setTemplate(self::TEMPLATE_PATH);
	}
	
	public function getItemsCollection()
	{		
		if (!$this->_items){
			$this->_items = Mage::helper('hometabspro')->getMenuItems();
		}			
		return $this->_items;
	}	
	
	public function getAjaxUrl()
	{
		return $this->getUrl('hometabspro/ajax/request', array('_secure'=>Mage::app()->getStore(true)->isCurrentlySecure()));
	}
	
	public function getActiveContent()
	{
		if (count($items = $this->getItemsCollection())){
			
			foreach ($items as $item){
				return Mage::helper('hometabspro')->getContentHtml( $item->getIndex() );
			}
		}
	}

        public function getUniqueClassKey() {

              if(!$this->getData('randomClassName')) {
                 $this->setData('randomClassName',rand());
              }

             return $this->getData('randomClassName');
        }



} 