<?php
$installer = $this;

// Create attribute set and deal attributes
Mage::getModel('groupdeals/groupdeals')->createAttributeSet();

// Add tables
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$installer->getTable('groupdeals')};
CREATE TABLE {$installer->getTable('groupdeals')} (
  `groupdeals_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `country_id` varchar(255) NOT NULL default '',
  `region` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `date_from` date NULL,
  `date_to` date NULL,
  `time_from` varchar(255) NOT NULL default '',
  `time_to` varchar(255) NOT NULL default '',
  `minimum_qty` int(11) NOT NULL,
  `sold_qty` int(11) NOT NULL,
  `maximum_qty` int(11) NOT NULL,
  `coupon_barcode` text NOT NULL default '',
  `coupon_merchant_address` int(11) NOT NULL,
  `coupon_merchant_contact` int(11) NOT NULL,
  `coupon_number` int(11) NOT NULL,
  `coupon_expiration_date` date NULL,
  PRIMARY KEY (`groupdeals_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_merchants')};
CREATE TABLE {$installer->getTable('groupdeals_merchants')} (
  `merchants_id` int(11) unsigned NOT NULL auto_increment,
  `name` text NOT NULL default '',
  `description` text NOT NULL default '',
  `website` text NOT NULL default '',
  `email` text NOT NULL default '',
  `facebook` text NOT NULL default '',
  `twitter` text NOT NULL default '',
  `phone` text NOT NULL default '',
  `mobile` text NOT NULL default '',
  `business_hours` text NOT NULL default '',
  `address` text NOT NULL default '',
  `redeem` text NOT NULL default '',
  `paypal_email` varchar(255) NOT NULL default '',
  `authorize_info` text NOT NULL default '',
  `bank_info` text NOT NULL default '',
  `other` text NOT NULL default '',
  `status` int(11) NOT NULL,
  PRIMARY KEY (`merchants_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_subscribers')};
CREATE TABLE {$installer->getTable('groupdeals_subscribers')} (
  `subscriber_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  PRIMARY KEY (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_notifications')};
CREATE TABLE {$installer->getTable('groupdeals_notifications')} (
  `notification_id` int(11) unsigned NOT NULL auto_increment,
  `groupdeals_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL default '',
  `unnotified_subscriber_ids` text NOT NULL default '',
  `notified_subscriber_ids` text NOT NULL default '',
  `status` varchar(255) NOT NULL default '',
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_coupons')};
CREATE TABLE {$installer->getTable('groupdeals_coupons')} (
  `coupon_id` int(11) unsigned NOT NULL auto_increment,
  `groupdeals_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `coupon_code` varchar(255) NOT NULL default '',
  `status` varchar(255) NOT NULL default '',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

//Create configuration
$installer->setConfigData('groupdeals/configuration/enabled',				1);
$installer->setConfigData('groupdeals/configuration/header_links',			1);
$installer->setConfigData('groupdeals/configuration/homepage_deals',	    '');
$installer->setConfigData('groupdeals/configuration/sidedeals_number',	    '3');
$installer->setConfigData('groupdeals/configuration/pdf_attachment',	    1);
$installer->setConfigData('groupdeals/configuration/countdown_type',	    1);

$installer->setConfigData('groupdeals/countdown_configuration/display_days',	    0);
$installer->setConfigData('groupdeals/countdown_configuration/bg_main',	   		'#F6F6F6');
$installer->setConfigData('groupdeals/countdown_configuration/bg_color',	   		'#333333');
$installer->setConfigData('groupdeals/countdown_configuration/alpha',	   			'70');
$installer->setConfigData('groupdeals/countdown_configuration/textcolor',	   		'#FFFFFF');
$installer->setConfigData('groupdeals/countdown_configuration/txt_color',	   		'#333333');
$installer->setConfigData('groupdeals/countdown_configuration/sec_text',	   		'SECONDS');
$installer->setConfigData('groupdeals/countdown_configuration/min_text',	   		'MINUTES');
$installer->setConfigData('groupdeals/countdown_configuration/hour_text',	   		'HOURS');
$installer->setConfigData('groupdeals/countdown_configuration/days_text',	   		'DAYS');

$installer->setConfigData('groupdeals/js_countdown_configuration/bg_main',	  	    '#F6F6F6');
$installer->setConfigData('groupdeals/js_countdown_configuration/textcolor',		'#333333');
$installer->setConfigData('groupdeals/js_countdown_configuration/days_text',		'day(s)');

$installer->setConfigData('groupdeals/notifications/email_sender',	   				'general');
$installer->setConfigData('groupdeals/notifications/email_new_deal',	   			1);
$installer->setConfigData('groupdeals/notifications/email_new_deal_template',	    'groupdeals_notifications_email_new_deal_template');
$installer->setConfigData('groupdeals/notifications/email_limit_met',	   			1);
$installer->setConfigData('groupdeals/notifications/email_limit_met_template',	   	'groupdeals_notifications_email_limit_met_template');
$installer->setConfigData('groupdeals/notifications/email_deal_over',	   			1);
$installer->setConfigData('groupdeals/notifications/email_deal_over_template',	   	'groupdeals_notifications_email_deal_over_template');

$installer->setConfigData('system/cron/schedule_generate_every',	   			1);
$installer->setConfigData('system/cron/schedule_ahead_for',	   					1);
$installer->setConfigData('system/cron/schedule_lifetime',	   					30);
$installer->setConfigData('system/cron/history_cleanup_every',	   				120);
$installer->setConfigData('system/cron/history_success_lifetime',	   			120);
$installer->setConfigData('system/cron/history_failure_lifetime',	   			120);

$installer->endSetup(); 