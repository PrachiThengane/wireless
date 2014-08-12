<?php
$installer = $this;

$installer->startSetup();

$installer->run("
alter table {$installer->getTable('groupdeals_merchants')} add column `user_id` int(11) NOT NULL after `merchants_id`;
alter table {$installer->getTable('groupdeals_merchants')} add column `permissions` varchar(255) NOT NULL default '' after `user_id`;
");

$installer->endSetup(); 