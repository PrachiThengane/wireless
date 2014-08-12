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

/**
 * Testimonial submit form block
 *
 * @category   HM
 * @package    HM_Testimonial
 * @author      Hello Magento Module Team <module@asia-connect.com.vn>
 */
class Cc_Testimonial_Block_Submit extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();

    }

    public function getAction()
    {
        return Mage::getUrl('testimonial/submit/post');
    }  

}
