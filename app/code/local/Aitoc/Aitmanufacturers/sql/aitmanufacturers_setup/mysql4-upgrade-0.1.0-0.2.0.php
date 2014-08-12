<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('aitmanufacturers_stores')} DROP FOREIGN KEY `FK_AITMANUFACTURERS_STORES_MANUFACTURER`;

ALTER TABLE {$this->getTable('aitmanufacturers_stores')} CHANGE COLUMN `manufacturers_id` `id` INTEGER(11) UNSIGNED NOT NULL;

ALTER TABLE {$this->getTable('aitmanufacturers_stores')} ADD CONSTRAINT `FK_AITMANUFACTURERS_STORES_MANUFACTURER` FOREIGN KEY (`id`)
	REFERENCES {$this->getTable('aitmanufacturers')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE {$this->getTable('aitmanufacturers_stores')} ADD COLUMN `manufacturer_id` INTEGER(11) UNSIGNED NOT NULL AFTER `id`;

ALTER TABLE {$this->getTable('aitmanufacturers')} ADD COLUMN `small_logo` VARCHAR(255) NOT NULL AFTER `title`;

ALTER TABLE {$this->getTable('aitmanufacturers')} ADD COLUMN `featured` SMALLINT(2) UNSIGNED NOT NULL DEFAULT '0' AFTER `layout_update_xml`;

UPDATE  {$this->getTable('aitmanufacturers_stores')},  {$this->getTable('aitmanufacturers')} SET {$this->getTable('aitmanufacturers_stores')}.`manufacturer_id` = {$this->getTable('aitmanufacturers')}.`manufacturer_id`
    WHERE {$this->getTable('aitmanufacturers_stores')}.id = {$this->getTable('aitmanufacturers')}.`id`;
");

$installer->endSetup();