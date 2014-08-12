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
 * @package    AW_Hometabspro
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
/**
 * Config Image Model
 */
class AW_Hometabspro_Model_System_Config_Backend_Image extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    /**
     * Save uploaded file before saving config value
     * @return Mage_Adminhtml_Model_System_Config_Backend_Image
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (isset($value['delete']) && isset($value['value']) && ($value = $value['value']) && $value['delete']){

            $path = Mage::getBaseDir('media').'/hometabspro/'.$value;
            try {
                if (file_exists($path)){
                    unlink($path);
                }
            } catch (Exception $e) {
                
            }
        }
        parent::_beforeSave();
        return $this;
    }
}