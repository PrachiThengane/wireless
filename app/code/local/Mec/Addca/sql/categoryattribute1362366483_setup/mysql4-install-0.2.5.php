<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "subhead",  array(
    "type"     => "varchar",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Sub Head",
    "input"    => "text",
    "class"    => "subhead",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => true,
    "unique"     => false,
    "note"       => ""

	));
$installer->endSetup();
	 