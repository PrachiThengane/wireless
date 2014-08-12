<?php
/**
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('aitmanufacturers')}
	ADD COLUMN `list_image` VARCHAR(255) NOT NULL AFTER `small_logo`,
	ADD COLUMN `show_brief_image` TINYINT(1) NOT NULL  DEFAULT '1' AFTER `list_image`,
	ADD COLUMN `show_list_image` TINYINT(1) NOT NULL DEFAULT '1' AFTER `show_brief_image`;

");

$installer->endSetup();