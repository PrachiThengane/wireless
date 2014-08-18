<?php
class Wireless_Icons_Block_Images extends Mage_Core_Block_Template
{
    private $icon_img_file_path = "app/code/local/Wireless/Icons/etc/icon_imgs.xml";
    public $base_dir = "icons/";    
    
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getIconData($product,$type) {      //$type : List (List , Homepage), Detail : Detail page
        
        
        $xml=simplexml_load_file($this->icon_img_file_path);
        //Get Attribute Set
        
        $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
        $attributeSetModel->load($product->getAttributeSetId());
        $attributeSetName  = $attributeSetModel->getAttributeSetName();
       
        
        $list_images =  $xml->$type; 
        
        $icon_imgs=array(); // used as return value "attributecode" => "image location"
        
        if (sizeof($list_images->$attributeSetName) == 0)
            return ;

        $attributeSets=$list_images->$attributeSetName;
     
        foreach ($attributeSets[0] as $attributeCode => $value) {
            
            $attribute = $product->getResource()->getAttribute($attributeCode);
            if ($attribute)
            {
                $attribute_value = $attribute ->getFrontend()->getValue($product);
                if (($attribute_value == null ) || (trim($attribute_value) == "")){
                    continue;
                }
                
                $attribute_value = str_replace(" ", "_", trim($attribute_value));
                
                if (sizeof($value)>0) {// Dropdown list attirbute
                    $p_value = "_".$attribute_value;    
                    $ele = (string)$value->$p_value;
                    
                    if (($ele==null) || ($ele=="")){
                        continue;    
                    }
                    
                    $icon_imgs[$attributeCode] = $ele;
                }                    
                else{ // Simple Product attribute                    
                    $icon_imgs[$attributeCode] = (string)$value;
                }                    
            }             
        }            
        
        
        return $icon_imgs;
        
       
        
    }
}
