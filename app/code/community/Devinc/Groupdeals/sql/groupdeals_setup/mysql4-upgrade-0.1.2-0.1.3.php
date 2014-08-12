<?php
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'target_met_email', 'int(11) after `maximum_qty`');
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'coupon_number');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'coupon_price', 'int(11)');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'coupon_fine_print', 'int(11)');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'coupon_highlights', 'int(11)');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'coupon_merchant_description', 'int(11)');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'coupon_business_hours', 'int(11)');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'coupon_merchant_logo', 'int(11)');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'coupon_additional_info', 'text');

$installer->getConnection()->addColumn($installer->getTable('groupdeals_merchants'), 'merchant_logo', 'text after `name`');

$installer->getConnection()->dropColumn($installer->getTable('groupdeals_coupons'), 'order_id');
$installer->getConnection()->addColumn($installer->getTable('groupdeals_coupons'), 'order_item_id', 'int(11) after `groupdeals_id`');
$installer->getConnection()->addColumn($installer->getTable('groupdeals_coupons'), 'redeem', 'varchar(255) after `coupon_code`');

$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'groupdeals_coupon_from', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'groupdeals_coupon_to', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'groupdeals_coupon_to_email', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'groupdeals_coupon_message', 'varchar(255)');

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'groupdeals_coupon_from', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'groupdeals_coupon_to', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'groupdeals_coupon_to_email', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'groupdeals_coupon_message', 'varchar(255)');

$installer->setConfigData('groupdeals/configuration/subscribe_popup',			1);
$installer->setConfigData('groupdeals/configuration/display_upcoming',			1);
$installer->setConfigData('groupdeals/configuration/slideshow_effect',			1);
$installer->setConfigData('groupdeals/configuration/enable_reviews',			1);
$installer->setConfigData('groupdeals/configuration/enable_facebook_comments',	1);
$installer->setConfigData('groupdeals/merchants_subscribe/enabled',				1);
$installer->setConfigData('groupdeals/merchants_subscribe/facebook_link',	    1);
$installer->setConfigData('groupdeals/merchants_subscribe/twitter_link',		1);
$installer->setConfigData('groupdeals/merchants_subscribe/bussiness_hours',		1);
$installer->setConfigData('groupdeals/merchants_subscribe/address',				2);
$installer->setConfigData('groupdeals/merchants_subscribe/redeem',				1);
$installer->setConfigData('groupdeals/facebook_connect/enabled',				0);
$installer->setConfigData('groupdeals/facebook_connect/locale',				    'en_US');

if (!$installer->getAttribute('customer', 'facebook_uid', 'attribute_id')) {
	$installer->addAttribute('customer', 'facebook_uid', array(
	        'type'	 => 'varchar',
	        'label'		=> 'Facebook Uid',
	        'visible'   => false,
			'required'	=> false
	));
}

$installer->endSetup(); 