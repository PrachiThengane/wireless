<?php

class Mec_Navigation_Block_Product_List_More extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
        if ($this->showMoreButton()){
        	$this->setTemplate('mec/navigation/catalog/product/list/more.phtml');
        }                               
    } 
	 
	public function showMoreButton(){
		return Mage::getStoreConfig('mec_navigation/general/autoscrolling') && Mage::helper('mec_navigation')->isMecNavigationAjax();
	}
	
	public function getMoreUrl(){		
		$url = '';						
		$pager = $this->getPagerBlock();		
		if ($pager){
			if (!$pager->isLastPage()){
				$url = $pager->getNextPageUrl();
			}
		}		
		return $url;
	}
	
  	public function getPagerBlock()
    {
    	$toolbar = $this->getLayout()->getBlock('product_list_toolbar');				
        $pagerBlock = $toolbar->getChild('product_list_toolbar_pager');

        if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($toolbar->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($toolbar->getLimitVarName())
                ->setPageVarName($toolbar->getPageVarName())
                ->setLimit($toolbar->getLimit())
                ->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($toolbar->getCollection());

            return $pagerBlock;
        }

        return false;
    }
                    
}
