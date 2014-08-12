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

class Cc_Testimonial_Model_System_Config_Source_Selectmode
{

    public function toOptionArray()
    {
        return array(
			array('value'=>'', 'label'=>''),
            array('value'=>'fade', 'label'=>'fade'),
			array('value'=>'fadeZoom', 'label'=>'fadeZoom'),
			array('value'=>'cover', 'label'=>'cover'),
			array('value'=>'uncover', 'label'=>'uncover'),
            array('value'=>'shuffle', 'label'=>'shuffle'),
			array('value'=>'zoom', 'label'=>'zoom'),
			array('value'=>'wipe', 'label'=>'wipe'),
			array('value'=>'toss', 'label'=>'toss'),
			array('value'=>'turnDown', 'label'=>'turnDown'),
			array('value'=>'turnUp', 'label'=>'turnUp'),
			array('value'=>'scrollUp', 'label'=>'scrollUp'),
			array('value'=>'scrollDown', 'label'=>'scrollDown'),
			
        );
    }

}
