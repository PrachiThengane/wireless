<?php

class Mec_Catalogsearch_Block_Term extends Mage_CatalogSearch_Block_Term
{   
  protected function _loadTerms()  
  {  
      if (empty($this->_terms)) {  
          $this->_terms = array();  
          $terms = Mage::getResourceModel('catalogsearch/query_collection')  
              ->setPopularQueryFilter(Mage::app()->getStore()->getId())  
              ->setOrder('popularity', 'DESC')  
              ->setPageSize(100)  
              ->load()  
              ->getItems();  
          if( count($terms) == 0 ) {  
              return $this;  
          }  
  
          $this->_maxPopularity = reset($terms)->getPopularity();  
          $this->_minPopularity = end($terms)->getPopularity();  
          $range = $this->_maxPopularity - $this->_minPopularity;  
          $range = ( $range == 0 ) ? 1 : $range;  
          foreach ($terms as $term) {  
              if( !$term->getPopularity() ) {  
                  continue;  
              }  
              $term->setRatio(($term->getPopularity()-$this->_minPopularity)/$range);  
              $temp[$term->getName()] = $term;  
              $termKeys[] = $term->getName();  
          }
          $_config = Mage::getStoreConfig('mec_catalogsearch/advancedcatalogsearch/term');
		  //Mage::log($_config);
          if($_config == 1){
               natcasesort($termKeys);
		  }  
          foreach ($termKeys as $termKey) {  
              $this->_terms[$termKey] = $temp[$termKey];  
          }  
      }  
      return $this;  
  }
}
?>
