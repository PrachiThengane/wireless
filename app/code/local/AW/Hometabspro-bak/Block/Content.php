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
class AW_Hometabspro_Block_Content extends Mage_Catalog_Block_Product_List
{
    /**
     * Path to template
     */
	const BASE_TEMPLATE = 'hometabspro/content.phtml';
	
    /**
     * Path to count index
     * (for retriving data frome customer session)
     */
    const PATH_COUNT_INDEX = 'AwHtpCountIndex';

    /**
     * Collection is specific for every type of content
     * @var Mage_Core_Model_Mysql4_Collection_Abstract
     */	 
	protected $_collection;

    /**
     * Class constructor
     */
	public function __construct()
	{
            parent::__construct();
            $this->setTemplate(self::BASE_TEMPLATE);
	}


        protected function _prepareLayout() {

            $coreSession = Mage::getSingleton('core/session');			
            $currentLayout = $this->_getHtpPageLayout();

            if($currentLayout instanceof Varien_Object) {
                     $coreSession->setCurrentLayout($currentLayout);
            }
	}

        /**
        *  $this->getPageLayout() function doesn't work correctly starts from 1.4.1.0 version
	*  So, there is no varien object in session
	*  We simply call Magento function hoping that this bug will be fixed in the
	*  next releases and if it fails call use our own method
        *  @return Varien_Object | bool
	*/
        protected function _getHtpPageLayout() {

                $currentLayout = $this->getPageLayout();
                if($currentLayout instanceof Varien_Object) {
                    return $currentLayout;
                }
                
                if(Mage::app()->getLayout()->getBlock('root') &&
                   Mage::app()->getLayout()->getBlock('root')->getTemplate()) {
                   $currentTemplate = Mage::app()->getLayout()->getBlock('root')->getTemplate();               

                    foreach(Mage::getSingleton('page/config')->getPageLayouts() as $layout) {
                         if($layout->getTemplate() == $currentTemplate) {
                                $currentLayout = clone $layout;
                                return $currentLayout;
                         }
                    }
                    
                    return false;
               }

            return false;
	}

 
    /**
     * Set up collection of products
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @return AW_Hometabspro_Block_Content
     */
	public function setCollection($collection)
	{
		$this->_collection = $collection;		
		return $this;
	}

    /**
     * Retrives  mode of content (List or Grid)
     * @return string
     */
	public function getMode()
	{
		return Mage::helper('hometabspro')->confGetCustomParam($this->getIndex(), 'mode');	
	}

    /**
     * Retrives basical collection of products
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection->addAttributeToSelect('*');

                /* There is no more need for filters as those already cached in
                 * See Helper/Cache
                 */

		return $collection;		
	}

    /**
     * Retrives collection of products
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
	public function getCollection()
	{
		if (!$this->_collection){
			$this->_collection = $this->_prepareCollection();
		}
		return $this->_collection;
	}

    /**
     * Retrives loader HTML
     * @return string
     */
	public function getLoaderHtml()
	{
		return $this->getLayout()->createBlock('hometabspro/loader')->toHtml();
	}

    /**
     * Retrives name of table in database
     * @param string $modelEntity Entity name
     * @return string
     */
	public function getTableName($modelEntity)
	{
		try {
			$table = Mage::getSingleton('core/resource')->getTableName($modelEntity);		
		} catch (Exception $e){
			Mage::throwException($e->getMessage());
		}
		return $table;
	}

    /**
     * Check URL for category id and cut it
     * @param string $url
     * @return string
     */
	public function checkUrl($url)
	{		
		preg_match('/.*\/category\/(\d+).*/', $url, $results);
		if (isset($results[1])){
			return str_replace('category/'.$results[1].'/', '', $url);			
		} else {
			return $url;
		}
	}

    /**
     * Retrives Customer Session
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Register column count in current session
     * It's will be new for every reload of page
     * @param string $skey
     * @param int $count
     */
    protected function _registerSessionCount($skey, $count)
    {
        $session = $this->_getCustomerSession();
        $getMethod = 'get'.self::PATH_COUNT_INDEX;
        $setMethod = 'set'.self::PATH_COUNT_INDEX;
        $counts = $session->$getMethod() ? $session->$getMethod() : array();
        $counts[$skey] = $count;
        $session->$setMethod($counts);
    }

    /**
     * Retrives column count from session
     * @param string $skey
     * @return int
     */
    protected function _getRegisteredSessionCount($skey)
    {
        $session = $this->_getCustomerSession();
        $getMethod = 'get'.self::PATH_COUNT_INDEX;
        $counts = $session->$getMethod() ? $session->$getMethod() : array();
        if (isset($counts[$skey])){
            return $counts[$skey];
        } else {
            return 3;
        }
    }


     /* Only for adding price blocks for bundle products and gift cards */

	protected function _toHtml() {

            $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
            $this->addPriceBlockType('giftcard', 'enterprise_giftcard/catalog_product_price', 'giftcard/catalog/product/price.phtml');

            return parent::_toHtml();

	}

    /**
     * Retrives Column Count for Currnet Layout
     * @return int
     */
    public function getColumnCount()
    {
        if ($this->getPageLayout()){
            $count = parent::getColumnCount();
            if ($skey = Mage::registry(AW_Hometabspro_Helper_Data::$SKEY)){
                $this->_registerSessionCount($skey, $count);
            }
            return $count;
        } else {
           if(Mage::getSingleton('core/session')->getCurrentLayout() instanceof Varien_Object) {
		return $this->_columnCountLayoutDepend[Mage::getSingleton('core/session')->getCurrentLayout()->getCode()];
            }
        }
    }
}