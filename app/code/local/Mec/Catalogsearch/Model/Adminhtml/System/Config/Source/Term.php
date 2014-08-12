<?php
class Mec_Catalogsearch_Model_Adminhtml_System_Config_Source_Term
{
    public function toOptionArray()
    {
        $result = array(
            array('value'=>1, 'label'=>Mage::helper('mec_catalogsearch')->__('Use default.')),
            array('value'=>2, 'label'=>Mage::helper('mec_catalogsearch')->__('Sort by number of uses.')),            
        );
		return $result;
    }

}
