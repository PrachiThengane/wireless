<?php
$installer = $this;

$installer->startSetup();

$installer->run("
alter table {$installer->getTable('groupdeals')} drop column `date_from`;
alter table {$installer->getTable('groupdeals')} drop column `date_to`;
alter table {$installer->getTable('groupdeals')} drop column `time_from`;
alter table {$installer->getTable('groupdeals')} drop column `time_to`;
alter table {$installer->getTable('groupdeals')} add column `datetime_from` datetime NULL after `city`;
alter table {$installer->getTable('groupdeals')} add column `datetime_to` datetime NULL after `datetime_from`;
");

$installer->endSetup(); 