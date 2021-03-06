<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Points
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
/* Clean DB transactions and summary for guests (customer_id = 0) */
$resource = Mage::getSingleton('core/resource');
$connWrite = $resource->getConnection('log_write');
$connRead = $resource->getConnection('log_read');

$summaryTable = $resource->getTableName("points/summary");
$select = $connRead->select();
$select->from($summaryTable, array('id'))->where('customer_id = ?', 0);
$problemSummaryId = $connRead->fetchOne($select);

if ($problemSummaryId)
{
    $sql = sprintf("DELETE FROM %s WHERE customer_id = 0", $summaryTable);
    $connWrite->query($sql);

    $transactionsTable = $resource->getTableName("points/transaction");
    $sql = sprintf("DELETE FROM %s WHERE summary_id = %s", $transactionsTable, $problemSummaryId);
    $connWrite->query($sql);
}