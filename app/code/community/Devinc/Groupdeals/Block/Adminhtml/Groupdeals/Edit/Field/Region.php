<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Field_Region extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
      parent::__construct();
      $this->setTemplate('groupdeals/region.phtml');
  }
}