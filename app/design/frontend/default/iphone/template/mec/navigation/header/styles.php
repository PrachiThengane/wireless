<style type="text/css">
	
	.gan-loadinfo{
		
		<?php if($_color = Mage::getStoreConfig('mec_navigation/ajaxloader/bordercolor')):?>
		border-color:<?php echo Mage::helper('mec_navigation')->formatColor($_color);?> !important;
		<?php endif;?>
		
		<?php if($_color = Mage::getStoreConfig('mec_navigation/ajaxloader/textcolor')):?>
		color:<?php echo Mage::helper('mec_navigation')->formatColor($_color);?> !important;
		<?php endif;?>
		
		<?php if($_color = Mage::getStoreConfig('mec_navigation/ajaxloader/bgcolor')):?>
		background-color:<?php echo Mage::helper('mec_navigation')->formatColor($_color);?> !important;
		<?php endif;?>
		
		<?php if($_width = intval(Mage::getStoreConfig('mec_navigation/ajaxloader/width'))):?>
		width:<?php echo $_width;?>px !important;
		<?php endif;?>
		
		<?php if($_height = intval(Mage::getStoreConfig('mec_navigation/ajaxloader/height'))):?>
		height:<?php echo $_height;?>px !important;
		<?php endif;?>
		
		<?php if(0 == intval(Mage::getStoreConfig('mec_navigation/ajaxloader/enable'))):?>
		display:none !important;
		<?php endif;?>
		
	}
	/* Background Color */
	.block.block-layered-nav .block-content{
		<?php if($_color = Mage::getStoreConfig('mec_navigation/filter/style')):?>
		background:<?php echo Mage::helper('mec_navigation')->formatColor($_color);?>;
		<?php else:?>
		background:#E7F1F4;
		<?php endif;?>
	}
	
	/* Buttons Color */
	.block-layered-nav .block-content button.button span span{
		<?php if($_color = Mage::getStoreConfig('mec_navigation/filter/button_style')):?>
		color:<?php echo Mage::helper('mec_navigation')->formatColor($_color);?>;
		<?php else:?>
		color:#519cde;
		<?php endif;?>
		
	}
	
	/* Slider Color */	
	#narrow-by-list .gan-slider-span{
		<?php if($_color = Mage::getStoreConfig('mec_navigation/filter/slider_style')):?>
		background:<?php echo Mage::helper('mec_navigation')->formatColor($_color);?>;
		<?php else:?>
		background:#0000FF;
		<?php endif;?>
	}
	
	/* Popup Window Background */
	#gan-left-nav-main-container .filter-note-content,
	#gan-right-nav-main-container .filter-note-content,
	#narrow-by-list .filter-note-content{
		<?php if($_color = Mage::getStoreConfig('mec_navigation/filter/popup_style')):?>
		background:<?php echo Mage::helper('mec_navigation')->formatColor($_color);?>;
		<?php else:?>
		background:#FFFFFF;
		<?php endif;?>
	}
	
	/* Help Icon View */
	#gan-left-nav-main-container .filter-note-handle,
	#gan-right-nav-main-container .filter-note-handle,
	#narrow-by-list .filter-note-handle{
		<?php if($_color = Mage::getStoreConfig('mec_navigation/filter/icon_style')):?>
		color:<?php echo Mage::helper('mec_navigation')->formatColor($_color);?>;
		<?php else:?>
		color:#519cde;
		<?php endif;?>
	}
	<?php if (Mage::helper('mec_navigation')->isMecNavigation() &&
			  Mage::getStoreConfig('mec_navigation/menubarsettings/navigation') == Mec_Navigation_Model_Layer::FILTER_TYPE_PLAIN): ?>
		<?php $categories = $this->getStoreCategories(); ?>
		<?php foreach ($categories as $cat): ?>
			#gan_nav_top.gan-plain-list > li.nav-<?php echo $cat->getId() ?> > a{
				<?php if (($_color = $cat->getData('navigation_pw_m_bcolor')) && ($_size = $cat->getData('navigation_pw_m_bsize'))): ?>
			 		border:<?php echo $_size; ?>px solid <?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
				<?php endif; ?>
				<?php if ($_color = $cat->getData('navigation_pw_m_bgcolor')): ?>
					background:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
				<?php endif; ?>
				<?php if ($_color = $cat->getData('navigation_pw_m_tcolor')): ?>
			  		color:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
			    <?php endif; ?>			 			
			}
			#gan_nav_top.gan-plain-list > li.nav-<?php echo $cat->getId() ?> > a:hover{
				<?php if ($_color = $cat->getData('navigation_pw_m_otcolor')): ?>
			  		color:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
			    <?php endif; ?>
				<?php if ($_color = $cat->getData('navigation_pw_m_obgcolor')): ?>
					background:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
				<?php endif; ?>			 			
			}
		    #gan_nav_top.gan-plain-list > li.gan-plain-style1.nav-<?php echo $cat->getId() ?> > a:hover,
		    #gan_nav_top.gan-plain-list > li.over.gan-plain-style1.nav-<?php echo $cat->getId() ?> > a{
		          border-bottom:0;
		          z-index:1000;
		          position:relative;
		    }
		    #gan_nav_top.gan-plain-list > li.gan-plain-style1.nav-<?php echo $cat->getId() ?> > a:hover span,
		    #gan_nav_top.gan-plain-list > li.over.gan-plain-style1.nav-<?php echo $cat->getId() ?> > a span{
		        <?php if (($_color = $cat->getData('navigation_pw_m_bcolor')) && ($_size = $cat->getData('navigation_pw_m_bsize'))): ?>
		          padding-bottom:<?php echo $_size; ?>px;
		        <?php endif; ?>
		    }      
		    #gan_nav_top.gan-plain-list > li.gan-plain-style1.nav-<?php echo $cat->getId() ?> .gan-plain{
		        <?php if (($_color = $cat->getData('navigation_pw_s_bcolor')) && ($_size = $cat->getData('navigation_pw_s_bsize'))): ?>
		          margin-top:-<?php echo $_size; ?>px;
		        <?php endif; ?>	
		    }
		    #gan_nav_top.gan-plain-list > li.gan-plain-style2.nav-<?php echo $cat->getId() ?> .gan-plain{
		        <?php if (($_color = $cat->getData('navigation_pw_s_bcolor')) && ($_size = $cat->getData('navigation_pw_s_bsize'))): ?>
		          margin-top:-<?php echo $_size; ?>px;
		        <?php endif; ?>	
		    }      
			#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level1 > a{			
			  <?php if ($_color = $cat->getData('navigation_pw_fl_tcolor')): ?>
			  	color:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			  <?php if ($_size = $cat->getData('navigation_pw_fl_tsize')): ?>
			  	font-size:<?php echo $_size; ?>px;
			  <?php endif; ?>
			  <?php if ($_color = $cat->getData('navigation_pw_fl_bgcolor')): ?>
			  	background:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			}
			#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level1 > a:hover{
			  <?php if ($_color = $cat->getData('navigation_pw_fl_otcolor')): ?>
			  	color:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			  <?php if ($_color = $cat->getData('navigation_pw_fl_obgcolor')): ?>
			  	background:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			}
			#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level2 > a{
			  <?php if ($_color = $cat->getData('navigation_pw_sl_tcolor')): ?>
			  	color:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			  <?php if ($_size = $cat->getData('navigation_pw_sl_tsize')): ?>
			  	font-size:<?php echo $_size; ?>px;
			  <?php endif; ?>
			}
			#gan_nav_top.gan-plain-list .nav-<?php echo $cat->getId() ?> .gan-plain-item li.sub-level2 > a:hover{
			  <?php if ($_color = $cat->getData('navigation_pw_sl_otcolor')): ?>
			  	color:<?php echo Mage::helper('mec_navigation')->formatColor($_color); ?>;
			  <?php endif; ?>
			}
		<?php endforeach; ?>
	<?php endif; ?>
</style>
<script type="text/javascript">
//<![CDATA[
	
	<?php if($loadimage = Mage::getStoreConfig('mec_navigation/ajaxloader/loadimage')):?>
	var loadimage = '<?php echo Mage::getBaseUrl("media")."mec/config/".$loadimage;?>';
	<?php else:?>
	var loadimage = '<?php echo $this->getSkinUrl("images/mec/loadinfo.gif");?>';
	<?php endif;?>
	var loadimagealign = '<?php echo Mage::getStoreConfig('mec_navigation/ajaxloader/imagealign');?>';
	
	<?php		
		$text = trim(Mage::getStoreConfig('mec_navigation/ajaxloader/text')) ? trim(Mage::getStoreConfig('mec_navigation/ajaxloader/text')) : $this->__('Loading, please wait...');
		$text = addslashes(str_replace("\n", "<br/>", str_replace("\r", '', $text)));		
	?>
	
	var mec_navigation_loadinfo_text = "<?php echo $text?>";
	var mec_navigation_urlhash = <?php if (Mage::getStoreConfig('mec_navigation/general/urlhash')): ?>true<?php else: ?>false<?php endif; ?>;
	<?php if ($url = $this->getNavigationCatigoryUrl()): ?>
		var gan_static_navigation_url = '<?php echo $url; ?>';
		mec_navigation_urlhash = false;
	<?php endif; ?>

	<?php if (Mage::getStoreConfig('mec_navigation/general/autoscrolling') == Mec_Navigation_Model_Adminhtml_System_Config_Source_Autoscrolling::AJAX): ?>	
		var gan_more_type_ajax = true;	
	<?php endif; ?>
	var gan_shop_by_area = <?php echo intval(Mage::getStoreConfig('mec_navigation/general/show_shopby')); ?>;

//]]>	
</script>
