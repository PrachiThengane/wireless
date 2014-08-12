<?php
class HM_QuickLogin_Model_Source_Fields extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'accountdashboard', 'label' => 'My Account'),
            array('value' => 'accountinformation', 'label' => 'Account Information'),
			array('value' => 'addressbook', 'label' => 'Address Book'),
			array('value' => 'myorder', 'label' => 'My Order'),
			array('value' => 'myproductreview', 'label' => 'My Product reviews'),
			array('value' => 'mytag', 'label' => 'My Tags'),
			array('value' => 'newletters', 'label' => 'Newsletters Subscriptions'),
			array('value' => 'mywishlist', 'label' => 'My Wishlist'),
			array('value' => 'mycompare', 'label' => 'My Compare'),
			array('value' => 'mydownload', 'label' => 'My Downloadable Products')
			
        );
    }
}
