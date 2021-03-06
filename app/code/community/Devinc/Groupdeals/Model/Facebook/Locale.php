<?php
/**
 * Facebook locale model
 *
 * @author     Ivan Weiler <ivan.weiler@gmail.com>
 */
class Devinc_Groupdeals_Model_Facebook_Locale
{
    public function getLocales()
    {
    	$locales = array();
    	$localesFile = Mage::app()->getConfig()->getModuleDir('etc', 'Devinc_Groupdeals').DS.'FacebookLocales.xml';
		
    	$xml = simplexml_load_file($localesFile, null, LIBXML_NOERROR);
		if($xml && is_object($xml->locale)) {
			foreach($xml->locale as $item) {
        		$locales[(string)$item->codes->code->standard->representation] = (string)$item->englishName;
			}
        }   	
    	
        asort($locales);
    	return $locales;
    }
    
	public function getOptionLocales()
    {
    	$locales = array();
    	foreach($this->getLocales() as $value => $label) {
    		$locales[] = array(
				'value' => $value,
				'label' => $label  	
    		);	
    	}
    	return $locales;
    }
    
}