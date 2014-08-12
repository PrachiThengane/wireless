/**
 * Trigger hover events for cartview
 */
document.observe('dom:loaded', function() {
  $('cartview').observe('mouseover', function(e) {
    $('cartview-panel').show();
	$('cartview-a').addClassName('cart-hove-a');
	$('cartview-span').addClassName('cart-hove-span');
  });
  $('cartview').observe('mouseout', function(e) {
    $('cartview-panel').hide();
	$('cartview-a').removeClassName('cart-hove-a');
	$('cartview-span').removeClassName('cart-hove-span');
  });
   
});

/**
 * Trigger hover events for wishlistview
 */
document.observe('dom:loaded', function() {
  $('wishlistview').observe('mouseover', function(e) {
    $('wishlistview-panel').show();
	$('wishlistview-a').addClassName('wishlist-hove-a');
	$('wishlistview-span').addClassName('wishlist-hove-span');
	
  });
  
  
  
  $('wishlistview').observe('mouseout', function(e) {
    $('wishlistview-panel').hide();
	$('wishlistview-a').removeClassName('wishlist-hove-a');
	$('wishlistview-span').removeClassName('wishlist-hove-span');
  });
});


/**
 * Trigger hover events for header menu
 */
document.observe('dom:loaded', function() {
  $('header-menuha').observe('mouseover', function(e) {
    $('header-menuha-son').show();
	$('header-menuha-son').addClassName('header-menuha-son-hover');
	$('closeall').addClassName('closeall-hover');
  });
	$('header-menuha').observe('mouseout', function(e) {
    $('header-menuha-son').hide();
	$('header-menuha-son').removeClassName('header-menuha-son-hover');
	$('closeall').removeClassName('closeall-hover');
  });
  
});

