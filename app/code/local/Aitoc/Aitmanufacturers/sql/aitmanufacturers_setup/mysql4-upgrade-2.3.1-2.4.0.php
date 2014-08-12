<?php
/**
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$this->addAttribute('catalog_product', 'aitmanufacturers_sort', array(
	'type'						=> 'int',
	'label'						=> 'Position',
	'required'					=> 0,
	'visible'					=> 0,
	'default'					=> 9999,
	'global'					=> 0,
	'is_configurable'			=> 0,
	'used_for_price_rules'		=> 0,
));

$installer->endSetup();