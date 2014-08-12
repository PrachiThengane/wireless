<?php
class AW_AdvancedReviews_Model_Observer{
    public function pageLoadBefore(){
        if (!Mage::getStoreConfig('advanced/modules_disable_output/AW_AdvancedReviews'))
        {
            $node = Mage::getConfig()->getNode('global/blocks/review/rewrite');
            $dnode = Mage::getConfig()->getNode('global/blocks/review/links_rewrite/helper');
            $node->appendChild($dnode);
        }
    }
}