<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_Orders extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ordersGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('real_order_id');
        $this->setDefaultDir('DESC');
        $this->setVarNameFilter('orders_filter');
		$this->setTemplate('groupdeals/orders/grid.phtml');
    }

	protected function _prepareCollection()
    {	
		//get deal start/end datetime
		$groupdeals = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));
		if ($groupdeals->getId()!='') {
			$start_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($groupdeals->getDatetimeFrom());
			$end_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($groupdeals->getDatetimeTo());
		} else {
			$start_date_time = '1970-01-01 00:00:00';
			$end_date_time = '1970-01-01 00:00:00';
		}
		
		if ((int)str_replace(".", "", Mage::getVersion())<'1410') {					
			$collection = Mage::getResourceModel('sales/order_collection')
				->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id', null, 'left')
				->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id', null, 'left')
				->addExpressionAttributeToSelect('billing_name','CONCAT({{billing_firstname}}, " ", {{billing_lastname}})',array('billing_firstname', 'billing_lastname'))
				->joinTable('sales/order_item','order_id=entity_id',array('product_id' => 'product_id'))
				->addAttributeToFilter('product_id', $this->getRequest()->getParam('id'))
				->addAttributeToFilter('created_at', array("from" =>  $start_date_time, "to" =>  $end_date_time, "datetime" => true));
		} else {	
			$sales_ids = array();
			$sales_item_collection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('created_at', array("from" =>	$start_date_time, "to" =>  $end_date_time, "datetime" => true))->addFieldToFilter('product_id', $this->getRequest()->getParam('id'));	
			if (count($sales_item_collection)>0) {
				foreach($sales_item_collection as $item) {							
					$sales_ids[] = $item->getOrderId();
				}
			}	
			$collection = Mage::getResourceModel('sales/order_grid_collection')
				->addAttributeToFilter('entity_id', array('in' => $sales_ids))
				->addAttributeToFilter('main_table.created_at', array("from" =>  $start_date_time, "to" =>  $end_date_time, "datetime" => true));
		}
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        /* $this->addColumn('customer_email', array(
            'header' => Mage::helper('sales')->__('Email'),
            'index' => 'customer_email',
        )); */

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Order Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
	    
		$product = Mage::registry('current_product');
		if ($product && $product->getTypeId()=='virtual') {
		  $this->addColumn('coupon_sent', array(
			  'header'    => Mage::helper('groupdeals')->__('Coupon Code'),
			  'align'     => 'left',
			  'index'     => 'entity_id',
			  'filter' => false,
			  'width'     => '100px',
			  'type'      => 'text',
			  'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_couponsent',
		  ));
		} elseif (!$product) {
		  $this->addColumn('coupon_sent', array(
			  'header'    => Mage::helper('groupdeals')->__('Coupon Code'),
			  'align'     => 'left',
			  'index'     => 'entity_id',
			  'filter' => false,
			  'width'     => '100px',
			  'type'      => 'text',
			  'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_couponsent',
		  ));
		}

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
           /*  $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View Order'),
                            'url'     => array('base'=>'adminhtml/sales_order/view'),
                            'field'   => 'order_id',
                            'target'   => 'blank'
                        ),
                        array(
                            'caption' => Mage::helper('sales')->__('Email Coupon(s)'),
                            'url'     => array('base'=>'groupdeals/adminhtml_groupdeals/emailCoupon'),
                            'field'   => 'order_id',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            )); */

			$this->addColumn('action', array(
			  'header'    => Mage::helper('groupdeals')->__('Action'),
			  'align'     => 'left',
			  'index'     => 'entity_id',
			  'filter' => false,
			  'width'     => '100px',
			  'type'      => 'action',
			  'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_action',
			));
			
        }
		
		$csv_excel_name = 'Deal';
		$product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
		if ($product && $product->getId()!='') {
			$csv_excel_name = $product->getName();
		}

        $this->addExportType('*/*/exportOrdersCsv/csv_excel_name/'.$csv_excel_name, Mage::helper('sales')->__('CSV'));
        //$this->addExportType('*/*/exportOrdersExcel/csv_excel_name/'.$csv_excel_name, Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }
	
    public function getCity()
    {
        $city = '';
	    if (Mage::registry('groupdeals_data')) {
	  	    $city = Mage::registry('groupdeals_data')->getCity();
	    } elseif ($this->getRequest()->getParam('city') && $this->getRequest()->getParam('city')!='') {
		    $city = $this->getRequest()->getParam('city');
	    }
        return $city;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
			return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }
	
    public function getGridUrl()
    {
        return $this->getUrl('*/*/orders', array('_current'=>true, 'city'=>$this->getCity(), 'groupdeals_id'=>$this->getRequest()->getParam('groupdeals_id')));
    }

}