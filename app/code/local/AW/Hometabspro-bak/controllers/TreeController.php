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
 */?>
<?php
/**
 * Home Tabs Pro Tree Controller
 */
class AW_Hometabspro_TreeController extends Mage_Adminhtml_Controller_Action
{

    public function getCatChildrensAction($id = null)
    {

        $category = $this->getRequest()->getParam('category');

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');

        $select = $db->select()
            ->from($resource->getTableName('catalog/category'), 'entity_id')
            ->where('parent_id='.$category);

        $childrens = '';
        foreach($db->fetchAll($select) as $id){
                $childrens.=$id['entity_id'].',';
        }
        echo substr($childrens,0,-1);
    }

}
