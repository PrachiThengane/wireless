<?php

$installer = $this;

$installer->startSetup();

$attribute_id = $installer->getConnection()->fetchOne("        
    select attribute_id from {$this->getTable('eav_attribute')} 
	where attribute_code = 'navigation_plain_window_width';    
"); 

if ($attribute_id){
    $installer->run("UPDATE {$this->getTable('eav_attribute')}
                SET attribute_code = 'navigation_pw_width'
                WHERE attribute_id = {$attribute_id}");
}

$attribute_id = $installer->getConnection()->fetchOne("        
    select attribute_id from {$this->getTable('eav_attribute')} 
	where attribute_code = 'navigation_plain_window_side_width';    
"); 

if ($attribute_id){
    $installer->run("UPDATE {$this->getTable('eav_attribute')}
                SET attribute_code = 'navigation_pw_side_width'
                WHERE attribute_id = {$attribute_id}");
}

$installer->getConnection()->addColumn($this->getTable('mec_navigation_attribute'), 'inblock_height',
    "int(3) NOT NULL DEFAULT 150");

$installer->getConnection()->addColumn($this->getTable('mec_navigation_attribute'), 'filter_button',
    "TINYINT(1) NOT NULL DEFAULT 1");
    
$installer->endSetup(); 
