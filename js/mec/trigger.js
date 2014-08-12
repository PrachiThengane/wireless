/**
 * Trigger hover events for header menu
 */
document.observe('dom:loaded', function() {
  $('custommenu').observe('mouseover', function(e) {
    $('allCatSon').show();
	$('allCatSon').addClassName('allCatSon-hover');

  });
	$('custommenu').observe('mouseout', function(e) {
    $('allCatSon').hide();
	$('allCatSon').removeClassName('allCatSon-hover');

  });
  
});

/**
 * Trigger hover events for shopcart menu
 */
document.observe('dom:loaded', function() {
  $('menuCartWrapper').observe('mouseover', function(e) {
    $('cart-content').show();
	$('cart-content').addClassName('cart-content-hover');
	$('menuCartWrapper').addClassName('active');

  });
	$('menuCartWrapper').observe('mouseout', function(e) {
    $('cart-content').hide('slow');
	$('cart-content').removeClassName('cart-content-hover');
	$('menuCartWrapper').removeClassName('active');

  });
  
});

/**
 * Trigger hover events for wishlist menu
 */
document.observe('dom:loaded', function() {
  $('menuWishlistWrapper').observe('mouseover', function(e) {
    $('wishlist-content').show();
	$('wishlist-content').addClassName('wishlist-content-hover');
	$('menuWishlistWrapper').addClassName('wishlist-wrapper-hover');
  });
	$('menuWishlistWrapper').observe('mouseout', function(e) {
    $('wishlist-content').hide('slow');
	$('wishlist-content').removeClassName('wishlist-content-hover');
	$('menuWishlistWrapper').removeClassName('wishlist-wrapper-hover');
  });
  
});

/**
 * Trigger hover events for compare menu
 */
document.observe('dom:loaded', function() {
  $('menuCompareWrapper').observe('mouseover', function(e) {
    $('compare-content').show();
	$('compare-content').addClassName('compare-content-hover');
	$('menuCompareWrapper').addClassName('compare-wrapper-hover');

  });
	$('menuCompareWrapper').observe('mouseout', function(e) {
    $('compare-content').hide('slow');
	$('compare-content').removeClassName('compare-content-hover');
	$('menuCompareWrapper').removeClassName('compare-wrapper-hover');
  });
  
});


/**
 * Trigger hover events for quicklogin
 */
document.observe('dom:loaded', function() {
  $('q-login').observe('mouseover', function(e) {  
	$('q-login').addClassName('q-login-hover');
  });
	$('q-login').observe('mouseout', function(e) {
	$('q-login').removeClassName('q-login-hover');
  });
});



jQuery.noConflict()(function(){
    // 使用 jQuery 的代码
	if(jQuery('#wishlist-content:has(div)').length==0){
	jQuery(document).ready(function(){
  jQuery("#wishlist-li").mouseover(function(){
  jQuery("#wishlist-content").html("<div class='block block-wishlist'><div class='block-content'>Wishlist empty.</div></div>");
  });
});}
});

