/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Hometabspro
 * @copyright  Copyright (c) 2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */
 
AW_Tabs = Class.create();
AW_Tabs.prototype = {
  initialize: function(selector, url, container, skey) {
    var self=this;
	this.url = url;
	this.container = container;
	this.selector = selector;
    this.skey = skey;
    $$(this.selector+' a').each(this.initTab.bind(this));
  },

  initTab: function(el) {
      el.href = 'javascript:void(0)';
      el.observe('click', this.showContent.bind(this, el, this.url, this.container, this.skey));
  },

  showContent: function(a, url, container, skey) {
      	  var li = $(a.parentNode), ul = $(li.parentNode);	
		  
		  $$('div.catalog-listing#hometabspro').each(function(el){ 
		  		el.style.visibility = 'hidden';
		  });	
		  $$('div.aw-htp-loader').each(function(el){
		  		el.style.display = 'block';
		  });	  

		  $$('li.htp-item').each(function(el){		  	
		  if (el==li) {		  
			el.addClassName('active');
	        //Start ajax here
			var ajaxParam = 'item/' + li.id + '/';
			var ajaxSkey = 'skey/' + skey + '/';
			var ajaxBlockUrl = url + ajaxParam + ajaxSkey;
            ajaxBlockUrl = ajaxBlockUrl.replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, ''));
        	new Ajax.Updater(container, ajaxBlockUrl, {
            	method: 'post',
            	parameters: {isAjax: 'true'},
				onComplete: function(transport) {
    				if (200 == transport.status){
						$$('div.aw-htp-loader').each(function(el){
							el.style.width = el.parentNode.getWidth() + 'px' ;
						});	
						decorateGeneric($$('.grid-row'), ['last', 'odd', 'even']);							
					}					
  				}
        	});						
	      } else {
	        el.removeClassName('active');
	      }
      });
  },

  moveMessagesBefore: function(awHtpClass) {

        var pageMessages = $$('.'+awHtpClass)[0].adjacent('.messages');
        var successMessages = [];

        if(pageMessages.size()) {
            pageMessages.each(function(element,i) {
                    if(element.down().hasClassName('success-msg')) {
                            successMessages[i] = element;
                    }
            });

            if(successMessages.size()) {
                    successMessages.each(function(element) {
                            document.getElementsByClassName(awHtpClass)[0].insert ({
                                    'before'  :  element
                            });
                    });
            }
      }

  }






}

function centerLoader(){
	$$('div.aw-htp-loader').each(function(el){
		el.style.width = $(el.parentNode.id).getWidth() + 'px' ;
	});	
}


 
	;
