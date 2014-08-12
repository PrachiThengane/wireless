<?php
    /**
    * @author Amasty Team
    * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
    * @package Amasty_Feeds
    */
    $installer = $this;
    $installer->startSetup();
    
    $installer->run("
        alter table `{$this->getTable('amfeed/field')}` 
        add column condition_serialized text default null;
        
        alter table `{$this->getTable('amfeed/profile')}` 
        add column condition_serialized text default null;
        

        ALTER TABLE `{$this->getTable('amfeed/template')}` 
        ADD COLUMN `csv_header_static` TEXT NOT NULL AFTER csv_header;

        ALTER TABLE `{$this->getTable('amfeed/template')}` 
        ADD COLUMN `frm_price_dec_point` VARCHAR(3) DEFAULT '.' AFTER frm_price,
        ADD COLUMN `frm_price_thousands_sep` VARCHAR(3) DEFAULT ',' AFTER frm_price_dec_point;

        ALTER TABLE `{$this->getTable('amfeed/template')}` 
        ADD COLUMN `condition_serialized` TEXT;

        DELETE FROM `{$this->getTable('amfeed/template')}` 
        WHERE filename = 'Google.com';

        INSERT INTO `{$this->getTable('amfeed/template')}` 
            (
             `status`,
             `generated_at`,
             `delivery_at`,
             `type`,
             `title`,
             `filename`,
             `mode`,
             `cond_stock`,
             `cond_disabled`,
             `cond_type`,
             `cond_advanced`,
             `xml_header`,
             `xml_body`,
             `xml_footer`,
             `xml_item`,
             `csv`,
             `csv_header`,
             `csv_header_static`,
             `csv_enclosure`,
             `csv_delimiter`,
             `frm_date`,
             `frm_price`,
             `frm_price_dec_point`,
             `frm_price_thousands_sep`,
             `frm_url`,
             `frm_image_url`,
             `frm_dont_use_category_in_url`,
             `frm_use_parent`,
             `default_image`,
             `delivery_type`,
             `delivered`,
             `send_email`,
             `ftp_host`,
             `ftp_user`,
             `ftp_pass`,
             `ftp_folder`,
             `ftp_is_passive`,
             `info_total`,
             `info_cnt`,
             `info_errors`,
             `freq`,
             `on_days`,
             `hour_from`,
             `hour_to`,
             `error_email`,
             `max_images`,
             `condition_serialized`)
VALUES (
        0,
        '0000-00-00 00:00:00',
        '0000-00-00 00:00:00',
        1,
        'Google.com',
        'Google.com',
        0,
        1,
        1,
        'simple,grouped,configurable,virtual,bundle,downloadable',
        'a:0:{}',
        '<?xml version=\"1.0\"?> <rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\"> <channel>',
        '<g:id>{type=\"attribute\" value=\"sku\" format=\"html_escape\" length=\"\" optional=\"no\" parent=\"no\"}</g:id>\r\n<title>{type=\"attribute\" value=\"name\" format=\"html_escape\" length=\"\" optional=\"no\" parent=\"no\"}</title>\r\n<description>{type=\"attribute\" value=\"description\" format=\"html_escape\" length=\"\" optional=\"no\" parent=\"no\"}</description> \r\n<g:product_type>{type=\"attribute\" value=\"category_name\" format=\"html_escape\" length=\"\" optional=\"no\" parent=\"no\"}</g:product_type> \r\n<link>{type=\"attribute\" value=\"url\" format=\"as_is\" length=\"\" optional=\"no\" parent=\"no\"}</link> \r\n<g:image_link>{type=\"attribute\" value=\"image\" format=\"as_is\" length=\"\" optional=\"no\" parent=\"no\"}</g:image_link> \r\n<g:condition>{type=\"text\" value=\"new\" format=\"as_is\" length=\"\" optional=\"no\" parent=\"no\"}</g:condition>\r\n<g:availability>{type=\"attribute\" value=\"is_in_stock\" format=\"as_is\" length=\"\" optional=\"no\" parent=\"no\"}</g:availability> \r\n<g:price>{type=\"attribute\" value=\"price\" format=\"price\" length=\"\" optional=\"no\" parent=\"no\"} USD</g:price>\r\n<g:brand>{type=\"attribute\" value=\"manufacturer\" format=\"html_escape\" length=\"\" optional=\"no\" parent=\"no\"}</g:brand>\r\n<g:google_product_category>{type=\"attribute\" value=\"category_name\" format=\"as_is\" length=\"\" optional=\"no\" parent=\"no\"}</g:google_product_category>\r\n<g:tax>\r\n <g:country>US</g:country>\r\n <g:rate>0</g:rate>\r\n <g:tax_ship>n</g:tax_ship>\r\n</g:tax>\r\n<g:shipping>\r\n <g:country>US</g:country>\r\n <g:price>0 USD</g:price>\r\n</g:shipping>\r\n<g:identifier_exists>FALSE</g:identifier_exists>',
        '</channel> </rss>',
        'item',
        'a:0:{}',
        0,
        '',
        34,
        44,
        'y.m.d',
        2,
        '.',
        ',',
        0,
        0,
        1,
        0,
        0,
        0,
        0,
        NULL,
        'NULL',
        'NULL',
        'NULL',
        'NULL',
        0,
        0,
        0,
        0,
        0,
        '',
        0,
        0,
        NULL,
        0,
        'a:0:{}');
            
            DELETE FROM `{$this->getTable('amfeed/template')}`
            WHERE filename = 'Amazon.com';

            INSERT INTO `{$this->getTable('amfeed/template')}`
                        (
                         `status`,
                         `generated_at`,
                         `delivery_at`,
                         `type`,
                         `title`,
                         `filename`,
                         `mode`,
                         `cond_stock`,
                         `cond_disabled`,
                         `cond_type`,
                         `cond_advanced`,
                         `xml_header`,
                         `xml_body`,
                         `xml_footer`,
                         `xml_item`,
                         `csv`,
                         `csv_header`,
                         `csv_header_static`,
                         `csv_enclosure`,
                         `csv_delimiter`,
                         `frm_date`,
                         `frm_price`,
                         `frm_price_dec_point`,
                         `frm_price_thousands_sep`,
                         `frm_url`,
                         `frm_image_url`,
                         `frm_dont_use_category_in_url`,
                         `frm_use_parent`,
                         `default_image`,
                         `delivery_type`,
                         `delivered`,
                         `send_email`,
                         `ftp_host`,
                         `ftp_user`,
                         `ftp_pass`,
                         `ftp_folder`,
                         `ftp_is_passive`,
                         `info_total`,
                         `info_cnt`,
                         `info_errors`,
                         `freq`,
                         `on_days`,
                         `hour_from`,
                         `hour_to`,
                         `error_email`,
                         `max_images`,
                         `condition_serialized`)
            VALUES (
                    0,
                    '0000-00-00 00:00:00',
                    '0000-00-00 00:00:00',
                    2,
                    'Amazon.com',
                    'Amazon.com',
                    0,
                    1,
                    1,
                    'simple,grouped,configurable,virtual,bundle,downloadable',
                    'a:0:{}',
                    '',
                    '',
                    '',
                    NULL,
                    'a:12:{s:4:\"name\";a:9:{i:0;s:8:\"Category\";i:1;s:5:\"Title\";i:2;s:4:\"Link\";i:3;s:3:\"SKU\";i:4;s:5:\"Price\";i:5;s:5:\"Brand\";i:6;s:5:\"Image\";i:7;s:11:\"Description\";i:8;s:12:\"Manufacturer\";}s:6:\"before\";a:10:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:4;s:0:\"\";i:5;s:0:\"\";i:6;s:0:\"\";i:7;s:0:\"\";i:8;s:0:\"\";i:9;s:0:\"\";}s:4:\"type\";a:9:{i:0;s:9:\"attribute\";i:1;s:9:\"attribute\";i:2;s:9:\"attribute\";i:3;s:9:\"attribute\";i:4;s:9:\"attribute\";i:5;s:9:\"attribute\";i:6;s:9:\"attribute\";i:7;s:9:\"attribute\";i:8;s:9:\"attribute\";}s:4:\"attr\";a:9:{i:0;s:13:\"category_name\";i:1;s:4:\"name\";i:2;s:3:\"url\";i:3;s:3:\"sku\";i:4;s:5:\"price\";i:5;s:12:\"manufacturer\";i:6;s:5:\"image\";i:7;s:11:\"description\";i:8;s:12:\"manufacturer\";}s:3:\"txt\";a:10:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:4;s:0:\"\";i:5;s:0:\"\";i:6;s:0:\"\";i:7;s:0:\"\";i:8;s:0:\"\";i:9;s:0:\"\";}s:9:\"meta_tags\";a:10:{i:0;s:10:\"meta_title\";i:1;s:10:\"meta_title\";i:2;s:10:\"meta_title\";i:3;s:10:\"meta_title\";i:4;s:10:\"meta_title\";i:5;s:10:\"meta_title\";i:6;s:10:\"meta_title\";i:7;s:10:\"meta_title\";i:8;s:10:\"meta_title\";i:9;s:10:\"meta_title\";}s:6:\"images\";a:10:{i:0;s:7:\"image_1\";i:1;s:7:\"image_1\";i:2;s:7:\"image_1\";i:3;s:7:\"image_1\";i:4;s:7:\"image_1\";i:5;s:7:\"image_1\";i:6;s:7:\"image_1\";i:7;s:7:\"image_1\";i:8;s:7:\"image_1\";i:9;s:7:\"image_1\";}s:5:\"after\";a:10:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:4;s:0:\"\";i:5;s:0:\"\";i:6;s:0:\"\";i:7;s:0:\"\";i:8;s:0:\"\";i:9;s:0:\"\";}s:6:\"format\";a:10:{i:0;s:5:\"as_is\";i:1;s:10:\"strip_tags\";i:2;s:5:\"as_is\";i:3;s:5:\"as_is\";i:4;s:5:\"price\";i:5;s:5:\"as_is\";i:6;s:5:\"as_is\";i:7;s:10:\"strip_tags\";i:8;s:5:\"as_is\";i:9;s:5:\"as_is\";}s:12:\"image_format\";a:10:{i:0;s:4:\"base\";i:1;s:4:\"base\";i:2;s:4:\"base\";i:3;s:4:\"base\";i:4;s:4:\"base\";i:5;s:4:\"base\";i:6;s:4:\"base\";i:7;s:4:\"base\";i:8;s:4:\"base\";i:9;s:4:\"base\";}s:6:\"length\";a:10:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:2:\"40\";i:4;s:0:\"\";i:5;s:0:\"\";i:6;s:0:\"\";i:7;s:0:\"\";i:8;s:0:\"\";i:9;s:0:\"\";}s:6:\"parent\";a:10:{i:0;s:2:\"no\";i:1;s:2:\"no\";i:2;s:2:\"no\";i:3;s:2:\"no\";i:4;s:2:\"no\";i:5;s:2:\"no\";i:6;s:2:\"no\";i:7;s:2:\"no\";i:8;s:2:\"no\";i:9;s:2:\"no\";}}',
                    1,
                    '',
                    110,
                    9,
                    'd.m.y',
                    2,
                    '.',
                    NULL,
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    0,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    0,
                    0,
                    0,
                    0,
                    0,
                    '',
                    0,
                    0,
                    NULL,
                    0,
                    'a:1:{i:1;a:1:{s:9:\"condition\";a:3:{s:9:\"attribute\";a:1:{i:0;s:5:\"price\";}s:8:\"operator\";a:1:{i:0;s:2:\"gt\";}s:5:\"value\";a:1:{i:0;s:4:\"0.01\";}}}}');
    ");
    $installer->endSetup();
?>
