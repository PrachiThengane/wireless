<?php
/**
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aitmanufacturersGrid');
        $this->setDefaultSort('manufacturer');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
        $collection->addStoreFilter($this->getStoreId(), true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
  
    protected function getStoreId()
    {
        return Mage::registry('store_id');
    }

    protected function _prepareColumns()
    {
        /*
        $this->addColumn('id', array(
            'header'    => Mage::helper('aitmanufacturers')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id',
        ));
        */
        
        $this->addColumn('manufacturer', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Brand'),
            'align'     =>'left',
            'width'     => '200px',
            'index'     => 'manufacturer',
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Brand Page Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));
        
        $this->addColumn('url_key', array(
            'header'    => Mage::helper('aitmanufacturers')->__('URL Key'),
            'align'     =>'left',
            'index'     => 'url_key',
        ));
        
        $layouts = array();
        foreach (Mage::getConfig()->getNode('global/aitmanufacturers/layouts')->children() as $layoutName=>$layoutConfig) 
        {
            $layouts[$layoutName] = (string)$layoutConfig->label;
        }
        
        $this->addColumn('root_template', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Layout'),
            'index'     => 'root_template',
            'type'      => 'options',
            'options'   => $layouts,
        ));
        
        /*
        if (!Mage::app()->isSingleStoreMode()) 
        {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('aitmanufacturers')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }
        */
        
        $this->addColumn('featured', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Featured'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'featured',
            'type'      => 'options',
            'options'   => array(
                0 => 'No',
                1 => 'Yes',
            ),
        ));
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));
        
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Sort Order'),
            'align'     =>'left',
            'width'     => '30px',
            'index'     => 'sort_order',
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('aitmanufacturers')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('aitmanufacturers')->__('Edit'),
                        'url'       => array(
                            'base' => '*/*/edit',
                            'params' => array('store' => $this->getStoreId()),
                        ),
                        'field'     => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        
        //$this->addExportType('*/*/exportCsv', Mage::helper('aitmanufacturers')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('aitmanufacturers')->__('XML'));
        return parent::_prepareColumns();
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('aitmanufacturers');
        
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('aitmanufacturers')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete', array('store' => $this->getStoreId())),
             'confirm'  => Mage::helper('aitmanufacturers')->__('Are you sure?')
        ));
        
        $statuses = Mage::getSingleton('aitmanufacturers/status')->getOptionArray();
        
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('aitmanufacturers')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true, 'store' => $this->getStoreId())),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('aitmanufacturers')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        
        return $this;
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $this->getCollection()->addStoreFilter($value);
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $this->getStoreId()));
    }
    
}
