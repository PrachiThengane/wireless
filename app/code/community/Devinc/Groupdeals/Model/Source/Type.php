<?php

class Devinc_Groupdeals_Model_Source_Type extends Varien_Object
{
    const TYPE_FUTURE  	= 4;
    const TYPE_MAIN		= 1;
    const TYPE_SIDE		= 2;
    const TYPE_RECENT	= 3;

    static public function getOptionArray()
    {
        return array(
            self::TYPE_MAIN     => Mage::helper('groupdeals')->__('Main Deal'),
            self::TYPE_SIDE    => Mage::helper('groupdeals')->__('Side Deal'),
            self::TYPE_FUTURE      => Mage::helper('groupdeals')->__('Future Deal'),
            self::TYPE_RECENT   => Mage::helper('groupdeals')->__('Recent Deal'),
        );
    }
    
    static public function getAllOptions()
    {
        $res = array(
            array(
                'value' => '',
                'label' => Mage::helper('catalog')->__('-- Please Select --')
            )
        );
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    } 
    
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $this->getAttribute()->getAttributeCode() . '_t';
            $collection->getSelect()
                ->joinLeft(
                    array($tableName => $this->getAttribute()->getBackend()->getTable()),
                    "`e`.`entity_id`=`{$tableName}`.`entity_id`"
                        . " AND `{$tableName}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                        . " AND `{$tableName}`.`store_id`='0'",
                    array());
            $valueExpr = $tableName . '.value';
        }
        else {
            $valueTable1    = $this->getAttribute()->getAttributeCode() . '_t1';
            $valueTable2    = $this->getAttribute()->getAttributeCode() . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    array($valueTable1 => $this->getAttribute()->getBackend()->getTable()),
                    "`e`.`entity_id`=`{$valueTable1}`.`entity_id`"
                        . " AND `{$valueTable1}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                        . " AND `{$valueTable1}`.`store_id`='0'",
                    array())
                ->joinLeft(
                    array($valueTable2 => $this->getAttribute()->getBackend()->getTable()),
                    "`e`.`entity_id`=`{$valueTable2}`.`entity_id`"
                        . " AND `{$valueTable2}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                        . " AND `{$valueTable2}`.`store_id`='{$collection->getStoreId()}'",
                    array()
                );
            $valueExpr = new Zend_Db_Expr("IF(`{$valueTable2}`.`value_id`>0, `{$valueTable2}`.`value`, `{$valueTable1}`.`value`)");
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }


}