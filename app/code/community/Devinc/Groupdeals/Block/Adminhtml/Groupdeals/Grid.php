<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('groupdealsGrid');
      $this->setSaveParametersInSession(true); 
  }

  protected function _prepareCollection()
  {
      $store = $this->_getStore();
      
      $groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection();
		
	  $groupdeals_product_id = array();
		  
	  foreach ($groupdeals_collection as $groupdeals) {  		
		  $groupdeals_product_id[] = $groupdeals->getProductId();    
	  }	  
 
      $collection = Mage::getModel('catalog/product')->getCollection()		
            ->addAttributeToSelect('entity_id')		
            ->addAttributeToSelect('name')	
            ->addAttributeToSelect('special_price')			
            ->addAttributeToSelect('groupdeal_type')		
            ->addAttributeToSelect('groupdeal_status')				
			->addStoreFilter($store)
			//->joinField('groupdeals_id','groupdeals/groupdeals','groupdeals_id','product_id=entity_id',null,'left')
			->joinTable('groupdeals/groupdeals','product_id=entity_id', array('groupdeals_id', 'sold_qty', 'minimum_qty', 'datetime_from', 'datetime_to'))
			/* ->joinField('city','groupdeals/groupdeals','city','product_id=entity_id',null,'left') */
			->joinField('city','groupdeals/groupdeals','city','product_id=entity_id',null,'left')
			->joinField('merchant_id','groupdeals/groupdeals','merchant_id','product_id=entity_id',null,'left')
			->addAttributeToFilter('entity_id', array('in' => $groupdeals_product_id))
			;
			        
	  if ($merchant_id = Mage::getModel('groupdeals/merchants')->isMerchant()) {
		   $collection->addFieldToFilter('merchant_id', $merchant_id);
	  }
	
 	  /*
$defaultSort = Mage::getSingleton('adminhtml/session')->getData('groupdealsGridsort');
 	  $sort = $this->getRequest()->getParam('sort', '');
	  if ($sort=='' && $defaultSort=='') {
		  $collection->setOrder('city', 'asc')
			  ->setOrder('groupdeal_status', 'asc')
			  ->setOrder('groupdeal_type', 'asc');
	  }  
*/
	  $collection->setOrder('city', 'asc');
	  
	
      $this->setCollection($collection);
	  parent::_prepareCollection();
	  $this->getCollection()->addWebsiteNamesToResult();	
	  return $this;
  }

  protected function _addColumnFilterToCollection($column)
  {
	  if ($this->getCollection()) {
		  if ($column->getId() == 'websites') {
			  $this->getCollection()->joinField('websites',
				'catalog/product_website',
				'website_id',
				'product_id=entity_id',
				null,
				'left');
		  } elseif ($column->getId() == 'city') {
		  	  $this->getCollection()->joinField('city',
				'groupdeals/groupdeals',
				'city',
				'product_id=entity_id',
				null,
				'left');		  
		  } elseif ($column->getId() == 'datetime_from') {
		  	  $this->getCollection()->joinField('datetime_from',
				'groupdeals/groupdeals',
				'datetime_from',
				'product_id=entity_id',
				null,
				'left');		  
		  } elseif ($column->getId() == 'datetime_to') {
		  	  $this->getCollection()->joinField('datetime_to',
				'groupdeals/groupdeals',
				'datetime_to',
				'product_id=entity_id',
				null,
				'left');		  
		  } 
	  }
	  return parent::_addColumnFilterToCollection($column);
  }
  
  protected function _setCollectionOrder($column)
  {
	  if ($this->getCollection()) {
		  if ($column->getId() == 'websites') {
			  $this->getCollection()->joinField('websites',
				'catalog/product_website',
				'website_id',
				'product_id=entity_id',
				null,
				'left');
		  } elseif ($column->getId() == 'city') {
		  	  $this->getCollection()->joinField('city',
				'groupdeals/groupdeals',
				'city',
				'product_id=entity_id',
				null,
				'left');		  
		  } elseif ($column->getId() == 'datetime_from') {
		  	  $this->getCollection()->joinField('datetime_from',
				'groupdeals/groupdeals',
				'datetime_from',
				'product_id=entity_id',
				null,
				'left');		  
		  } elseif ($column->getId() == 'datetime_to') {
		  	  $this->getCollection()->joinField('datetime_to',
				'groupdeals/groupdeals',
				'datetime_to',
				'product_id=entity_id',
				null,
				'left');		  
		  } 
		  elseif ($column->getId() == 'sold_qty') {
		  	  $this->getCollection()->joinField('sold_qty',
				'groupdeals/groupdeals',
				'sold_qty',
				'product_id=entity_id',
				null,
				'left');		  
		  } 
	  }
	  return parent::_setCollectionOrder($column);
  }
  
  protected function _prepareColumns()
  {
	 
     /*  $this->addColumn('groupdeals_id', array(
          'header'    => Mage::helper('groupdeals')->__('ID'),
          'align'     => 'right',
          'width' => '50px',
          'index'     => 'groupdeals_id',
      ));  */
		
	  $this->addColumn('name', array( 
          'header'    => Mage::helper('groupdeals')->__('Name'),
          'align'     => 'left',
          'index'     => 'name',
          'type'      => 'text',
      ));       

	  $this->addColumn('groupdeal_type', array(
          'header'    => Mage::helper('groupdeals')->__('Type'),
          'align'     => 'left',
          'index'     => 'groupdeal_type',
          'width'     => '70px',
          'type'      => 'options',
          'options'   => array(
              0 => 'Future Deal',
              1 => 'Main Deal',
              2 => 'Side Deal',
              3 => 'Recent Deal',
          ),
          'renderer'  => 'groupdeals/adminhtml_groupdeals_grid_renderer_type',
      ));  
	  
      $store = $this->_getStore();
		
	  /* $this->addColumn('product_price', array( 
          'header'    => Mage::helper('groupdeals')->__('Price'),
          'align'     => 'left',
          'index'     => 'product_price',
          'currency_code' => $store->getBaseCurrency()->getCode(),
          'type'      => 'price',
      ));     */
	  
	  $this->addColumn('special_price', array( 
          'header'    => Mage::helper('groupdeals')->__('Special Price'),
          'align'     => 'left',
          'index'     => 'special_price',
          'width'     => '50px',
          'currency_code' => $store->getBaseCurrency()->getCode(),
          'type'      => 'price',
      ));     
		
	  $this->addColumn('sold_qty', array( 
          'header'    => Mage::helper('groupdeals')->__('Purchased/Target'),
          'align'     => 'left',
          'index'     => 'sold_qty',
          'width'     => '50px',
          'type'      => 'text',
          'filter'    => false,
          'renderer'  => 'groupdeals/adminhtml_groupdeals_grid_renderer_target',
      ));  
	  
	  $this->addColumn('datetime_from', array(
          'header'    => Mage::helper('groupdeals')->__('Date/Time From'),
          'align'     => 'left',
          'width'     => '135px',
          'type'      => 'date',
          'format'    => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
          'gmtoffset' => false,
          'default'   => '',
          'index'     => 'datetime_from',
      ));	
	  
	  $this->addColumn('datetime_to', array(
          'header'    => Mage::helper('groupdeals')->__('Date/Time To'),
          'align'     => 'left',
          'width'     => '135px',
          'type'      => 'date',
          'format'    => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
          'gmtoffset' => false,
          'default'   => '',
          'index'     => 'datetime_to',
      ));	  
	 	  
	  /*
	  $groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection(); 
	  
	  $countries = Array();
	  $regions = Array();
	  $cities = Array();
	  
	  foreach ($groupdeals_collection as $groupdeals) {  
		if ($groupdeals->country_id!='') {                 
          $countries[$groupdeals->getId()] = Mage::app()->getLocale()->getCountryTranslation($groupdeals->country_id);   
		}
		if ($groupdeals->region!='') {
          $regions[$groupdeals->getId()] = $groupdeals->region;          
		}
		if ($groupdeals->city!='') {
          $cities[$groupdeals->getId()] = $groupdeals->city; 
		}
      }
	  
	  if (count($countries)!=0) {
		  $this->addColumn('country', array( 
			  'header'    => Mage::helper('groupdeals')->__('Country'),
			  'align'     => 'left',
              'width'     => '100px',
			  'index'     => 'groupdeals_id',
			  'type'      => 'options',
			  'options'   => $countries
		  )); 
	  }
	  
	  if (count($regions)!=0) {
		  $this->addColumn('region', array( 
			  'header'    => Mage::helper('groupdeals')->__('State/Province'),
			  'align'     => 'left',
              'width'     => '100px',
			  'index'     => 'groupdeals_id',
			  'type'      => 'options',
			  'options'   => $regions
		  )); 
	  } */
	  
	  $this->addColumn('city', array( 
		  'header'    => Mage::helper('groupdeals')->__('City'),
		  'align'     => 'left',
		  'width'     => '100px',
		  'index'     => 'city',
		  'type'      => 'text',
	  ));    

	  if (!Mage::app()->isSingleStoreMode()) {
		$this->addColumn('websites',
			array(
				'header'=> Mage::helper('groupdeals')->__('Websites'),
				'width' => '100px',
				'sortable'  => false,
				'index'     => 'websites',
				'type'      => 'options',
				'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
		));
	  }
	  
      $this->addColumn('groupdeal_status', array(
          'header'    => Mage::helper('groupdeals')->__('Status'),
          'align'     => 'left',
          'index'     => 'groupdeal_status',
          'width'     => '100px',
          'type'      => 'options',
          'options'   => array(
              0 => 'Queued',
              1 => 'Running',
              4 => 'Ended',
              2 => 'Disabled',
              5 => 'Pending Approval',
          ),
          'renderer'  => 'groupdeals/adminhtml_groupdeals_grid_renderer_status',
      )); 
	  
	  $action_array = array();
	  
	  if ((Mage::getModel('groupdeals/merchants')->getPermission('add_edit')==1 || Mage::getModel('groupdeals/merchants')->getPermission('add_edit')==null) || (Mage::getModel('groupdeals/merchants')->getPermission('sales')==1 || Mage::getModel('groupdeals/merchants')->getPermission('sales')==null)) {
		  $action_array[] = array(
				'caption'   => Mage::helper('groupdeals')->__('Edit'),
				'url'       => array('base'=> '*/*/columnEdit'),
				'field'     => 'groupdeals_id'
		  );	
		  $action_array[] = array(
                'caption'   => Mage::helper('groupdeals')->__('Set as Main Deal'),
                'url'       => array('base'=> '*/*/setMain'),
                'field'     => 'groupdeals_id',
			    'condition' => array('data' => 'groupdeal_type', 'operator' => 'eq', 'value' => '2')
          );
	  }
	  if (Mage::getModel('groupdeals/merchants')->getPermission('delete')==1 || Mage::getModel('groupdeals/merchants')->getPermission('delete')==null) {
		  $action_array[] = array(
				'caption'   => Mage::helper('groupdeals')->__('Delete'),
				'url'       => array('base'=> '*/*/delete'),
				'field'     => 'groupdeals_id',
				'confirm'  => Mage::helper('groupdeals')->__('Are you sure you want to delete this deal?')
		  );
	  }
	  
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('groupdeals')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getGroupdealsId',
			    'width'     => '70px',
                'actions'   => $action_array,
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'width'     => '100px',
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('groupdeals')->__('CSV'));
	  
      return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

		if (Mage::getModel('groupdeals/merchants')->getPermission('delete')==1 || Mage::getModel('groupdeals/merchants')->getPermission('delete')==null) {
			$this->getMassactionBlock()->addItem('delete', array(
				 'label'    => Mage::helper('groupdeals')->__('Delete'),
				 'url'      => $this->getUrl('*/*/massDelete'),
				 'confirm'  => Mage::helper('groupdeals')->__('Are you sure you want to delete these deals?')
			));
		}

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
		if ((Mage::getModel('groupdeals/merchants')->getPermission('add_edit')==1 || Mage::getModel('groupdeals/merchants')->getPermission('add_edit')==null) && (Mage::getModel('groupdeals/merchants')->getPermission('approve')==0 || Mage::getModel('groupdeals/merchants')->getPermission('approve')==null)) {		
			$this->getMassactionBlock()->addItem('status', array(
				 'label'=> Mage::helper('groupdeals')->__('Change status'),
				 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
				 'additional' => array(
						'visibility' => array(
							 'name' => 'status',
							 'type' => 'select',
							 'class' => 'required-entry',
							 'label' => Mage::helper('groupdeals')->__('Status'),
							 'values' => $statuses
						 )
				 )
			));
		}
		
        return $this;
  }

  protected function _getStore()
  {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
  }
	
  public function getRowUrl($row)
  {
      if (Mage::getModel('groupdeals/merchants')->isMerchant() && Mage::getModel('groupdeals/merchants')->getPermission('add_edit')==0 && Mage::getModel('groupdeals/merchants')->getPermission('sales')==0) {
		    return false;
	  } else {
			return $this->getUrl('*/*/edit', array('groupdeals_id' => $row->getGroupdealsId(), 'id' => $row->getId()));
	  }
  }

}