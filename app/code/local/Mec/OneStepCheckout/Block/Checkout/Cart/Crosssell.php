<?php
class Mec_OneStepCheckout_Block_Checkout_Cart_Crosssell extends Mage_Checkout_Block_Cart_Crosssell{
	
	public function getCartUpSellProducts()
	{
		// Mage::log('getCartUpSellProducts');
		$last_add = Mage::getSingleton('core/session')->getLastAddProduct();

		if($last_add){
			$product = Mage::getModel('catalog/product')->load($last_add);
           
			/*$collection = $product->getRelatedProductCollection()
				->addAttributeToSort('position', 'asc')
				->addStoreFilter();*/
                
            $collection = $product->getRelatedProductCollection()            
            ->addAttributeToSelect('*')                        
            ->setOrder('position','asc')
            ->addStoreFilter();
            
            
        
			$this->_addProductAttributesAndPrices($collection);
			$collection->load();
            $items=$collection->getItems();
            
            $limit = Mage::getStoreConfig('recommender/relatedproducts/numberofrelatedproducts');
        
             if (sizeof($items) < $limit){
            //if (sizeof($items) == 0){
                
                $extratems = $this->getItems(true, $limit - sizeof($items));

                //var_dump (sizeof($extratems));
                //echo ('extra');
                $items = array_merge($items, $extratems);
            }
            
            
            //
			//return $collection;
            return $items;
		}else{
			return '';
		}
		
	}
	
	public function getAddMuiltAction(){
		
		return $this->getUrl('checkout/cart').'addCheckoutProductList';
	
	}
	
   protected $_linkSource = array('useLinkSourceManual', 'useLinkSourceCommerceStack'); // from most to least authoritative

    protected function _getCollection($linkSource = null)
    {
        if(is_null($linkSource)) $linkSource = $this->_linkSource[0];

        
        $collection = Mage::getModel('catalog/product_link')->useCrossSellLinks()
            ->{$linkSource}()
            ->getProductCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addStoreFilter();
        $this->_addProductAttributesAndPrices($collection);

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        return $collection;
    }

    // related, limit, collection : used for related products,

    public function getItems($related = false, $limit)
    {
        $items = $this->getData('items');
       
        if (is_null($items) || sizeof($items)==0) {
            $items = array();
            $ninProductIds = $this->_getCartProductIds();
            if ($ninProductIds) {
                
                $last_add = Mage::getSingleton('core/session')->getLastAddProduct();
                         //       $lastAdded = (int) $this->_getLastAddedProductId();
                    
                if ($related == true) { //related products
                   /* $collection = $this->_getCollection()
                        ->addProductFilter($last_add);
                        var_dump($last_add);
                        
                    if (!empty($ninProductIds)) {
                        $collection->addExcludeProductFilter($ninProductIds);
                    }
                    $collection->setPositionOrder()->load();

                    foreach ($collection as $item) {
                        $ninProductIds[] = $item->getId();
                        $items[] = $item;
                    }                                                                                                      
                    $limit = Mage::getStoreConfig('recommender/relatedproducts/numberofrelatedproducts ') - count($items);    */
                    
                     // A bit of a hack, but return an empty collection if user selected 0 recommendations to show in config
                     
                    $product = Mage::getModel('catalog/product')->load($last_add);  
                    //$unionLinkedItemCollection = null; 
                    //$limit = Mage::getStoreConfig('recommender/relatedproducts/numberofrelatedproducts');

                    $unionLinkedItemCollection = null; 
                    if($limit < 1)
                    {
                        $unionLinkedItemCollection  = $product->getRelatedProductCollection();
                        $unionLinkedItemCollection ->load();
                        $unionLinkedItemCollection ->clear();
                        return $unionLinkedItemCollection -> getItems();
                    }

                    // Get manual links
                    // Set link source to automated CommerceStack recommendations
                    $linkModel = $product->getLinkInstance();
                    $linkModel->useLinkSourceManual();
                    
                    $numRecsToGet = $limit;
                    if(!is_null($unionLinkedItemCollection))
                    {
                        $numRecsToGet = $limit - count($unionLinkedItemCollection);
                    }

                    if($numRecsToGet > 0)
                    {
                        // Figure out if we should use a category filter
                        $constrainCategory = Mage::getStoreConfig('recommender/relatedproducts/constraincategory');
                        $currentCategory = Mage::registry('current_category');
                        $productCategory = $currentCategory;
                        if(is_null($currentCategory))
                        {
                            // This could be a recently viewed or a search page. Try to get category collection and arbitrarily use first
                            /* @var $currentProduct Mage_Catalog_Model_Product */
                            $currentProduct = Mage::registry('current_product');
                            if (is_object($currentProduct))
                            {
                                $currentCategory = $currentProduct->getCategoryCollection();
                                $productCategory = $currentCategory->getFirstItem();
                                $currentCategory = $productCategory;
                            }
                        }
                        $useCategoryFilter = !is_null($currentCategory) && $constrainCategory;

                        // Set link source to automated CommerceStack recommendations
                        $linkModel = $product->getLinkInstance();
                        $linkModel->useLinkSourceCommerceStack();
                    }

                    while($numRecsToGet > 0)
                    {
                        $linkedItemCollection = $product->getRelatedProductCollection()
                            ->addAttributeToSelect('required_options')
                            ->setGroupBy()
                            ->setPositionOrder()
                            ->addStoreFilter();

                        $linkedItemCollection->getSelect()->limit($numRecsToGet);

                        if($useCategoryFilter)
                        {
                            $linkedItemCollection->addCategoryFilter($currentCategory);
                        }

                        if(!is_null($unionLinkedItemCollection))
                        {
                            $linkedItemCollection->addExcludeProductFilter($unionLinkedItemCollection->getAllIds());
                        }

                        Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($linkedItemCollection,
                            Mage::getSingleton('checkout/session')->getQuoteId()
                        );
                        $this->_addProductAttributesAndPrices($linkedItemCollection);

                        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($linkedItemCollection);

                        $linkedItemCollection->load();

                        // Add new source linked items to existing union of linked items
                        if(is_null($unionLinkedItemCollection))
                        {
                            $unionLinkedItemCollection = $linkedItemCollection;
                        }
                        else
                        {
                            foreach($linkedItemCollection as $linkedProduct)
                            {
                                $unionLinkedItemCollection->addItem($linkedProduct);
                            }
                        }

                        if(!is_null($unionLinkedItemCollection))
                        {
                            $numRecsToGet = $limit - count($unionLinkedItemCollection);
                        }

                        if(!$useCategoryFilter) break;

                        // Go up a category level for next iteration
                        $currentCategory = $currentCategory->getParentCategory();
                        if(is_null($currentCategory->getId())) break;
                    }

                    // If we still don't have enough recommendations fill out the remaining with randoms.
                    
                    if($numRecsToGet > 0) $currentCategory = $productCategory;
                    while($numRecsToGet > 0)
                    {
                        $randCollection = Mage::getResourceModel('catalog/product_collection');
                        Mage::getModel('catalog/layer')->prepareProductCollection($randCollection);
                        $randCollection->getSelect()->order('rand()');
                        $randCollection->addStoreFilter();
                        $randCollection->setPage(1, $numRecsToGet);
                        $randCollection->addIdFilter(array_merge($unionLinkedItemCollection->getAllIds(), array($product->getId())), true);

                        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($randCollection);

                        if($useCategoryFilter)
                        {
                            $randCollection->addCategoryFilter($currentCategory);
                        }
                        
                        foreach($randCollection as $linkedProduct)
                        {
                            $unionLinkedItemCollection->addItem($linkedProduct);
                        }
                        
                        if(!$useCategoryFilter) break; // We tried everything

                        if(!is_null($unionLinkedItemCollection))
                        {
                            $numRecsToGet = $limit - count($unionLinkedItemCollection);
                        }
                        
                        // Go up a category level for next iteration
                        $currentCategory = $currentCategory->getParentCategory();
                        if(is_null($currentCategory->getId())) break;
                        
                    }

             
                    foreach ($unionLinkedItemCollection as $product) {
                        $product->setDoNotUseCategoryId(true);
                    }

                    return $unionLinkedItemCollection->getItems();
                }
                else{  //crosssell
                  
                    $limit = Mage::getStoreConfig('recommender/relatedproducts/numberofcrosssellproducts') - count($items);    
                }
                                                                                                                       
                                                                               
                // A bit of a hack, but return an empty collection if user selected 0 recommendations to show in config
                if($limit < 1)
                {
                    /*$this->_itemCollection = $this->_getCollection($this->_linkSource[0]);
                    $this->_itemCollection->load();
                    $this->_itemCollection->clear();
                    return $this;*/
                    $this->setData('items', $items);
                    return $items;
                }

                if (count($items) > $limit)
                {
                    $this->setData('items', $items);
                    return $items;
                }

                $unionLinkedItemCollection = null;
                foreach($this->_linkSource as $linkSource)
                {
                    $numRecsToGet = $limit;
                    if(!is_null($unionLinkedItemCollection))
                    {
                        $numRecsToGet = $limit - count($unionLinkedItemCollection);
                    }

                    if($numRecsToGet > 0)
                    {
                        if (count($items) < $numRecsToGet)
                        {
                            if(!is_null($unionLinkedItemCollection))
                            {
                                $ninProductIds = array_merge($ninProductIds, $unionLinkedItemCollection->getAllIds());
                            }

                            $filterProductIds = array_merge($this->_getCartProductIds(), $this->_getCartProductIdsRel());
                            $collection = $this->_getCollection($linkSource)
                                ->addProductFilter($filterProductIds)
                                ->addExcludeProductFilter($ninProductIds)
                                ->setGroupBy()
                                ->setPositionOrder();

                            $collection->getSelect()->limit($numRecsToGet);
                            $collection->load();
                        }

                        if(is_null($unionLinkedItemCollection))
                        {
                            $unionLinkedItemCollection = $collection;
                        }
                        else
                        {
                            // Add new source linked items to existing union of linked items
                            foreach($collection as $linkedProduct)
                            {
                                $unionLinkedItemCollection->addItem($linkedProduct);
                            }
                        }
                    }
                }
            }


            if(@count($unionLinkedItemCollection) < $limit)
            {
                // Get categories for randoms
                $cartProducts = $this->getQuote()->getAllItems();
                $firstProduct = $cartProducts[0];
                $firstProduct = $firstProduct->getProduct();
                $category = $firstProduct->getCategoryCollection();
                $category = $category->getFirstItem(); // Arbitrary. Really we should do all items before moving up the hierarchy

                $constrainCategory = Mage::getStoreConfig('recommender/relatedproducts/constraincategory');
                $useCategoryFilter = !is_null($category) && $constrainCategory;
            }

            while(@count($unionLinkedItemCollection) < $limit)
            {
                // We still don't have enough recommendations. Fill out the remaining with randoms.
                $numRecsToGet = $limit - count($unionLinkedItemCollection);

                $randCollection = Mage::getResourceModel('catalog/product_collection');
                Mage::getModel('catalog/layer')->prepareProductCollection($randCollection);
                $randCollection->getSelect()->order('rand()');
                $randCollection->addStoreFilter();
                $randCollection->setPage(1, $numRecsToGet);
                $randCollection->addIdFilter(array_merge($unionLinkedItemCollection->getAllIds(), $this->_getCartProductIds()), true);

                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($randCollection);

                if($useCategoryFilter)
                {
                    $randCollection->addCategoryFilter($category);
                }

                foreach($randCollection as $linkedProduct)
                {
                    $unionLinkedItemCollection->addItem($linkedProduct);
                }

                if(!$useCategoryFilter) break; // We tried everything

                // Go up a category level for next iteration
                $category = $category->getParentCategory();
                if(is_null($category->getId())) $useCategoryFilter = false;
            }

            foreach(@$unionLinkedItemCollection as $item)
            {
                $items[] = $item;
            }

            $this->setData('items', $items);
        }
        return $items;
    }
     
}