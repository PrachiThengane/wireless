<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * This extension is only for developers as a technology exchange
 * Based on EasyTestimonial_v1.5.8 by mage-world.com
 * Fixed the bug that when compilation has been enabled, the testimonial tab in the backend will be blank page.
 *****************************************************
 * @category   Cc
 * @package    Cc_Testimonial
 * @Author     Chimy
 */
?>
<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('testimonial')};
CREATE TABLE {$this->getTable('testimonial')} (
  `testimonial_id` int(11) unsigned NOT NULL auto_increment,
  `client_name` varchar(255) NOT NULL default '',
  `company` varchar(255) NOT NULL default '',
  `website` varchar(255) NULL default '',
  `email` varchar(255) NOT NULL default '',
  `address` varchar(255) NOT NULL default '',
  `description` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '2',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`testimonial_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
