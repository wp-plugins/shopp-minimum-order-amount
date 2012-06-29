<?php  
/* 
Plugin Name: Shopp Minimum Order
Description: Set a minimum order amount (total items or order total) for Shopp checkout.
Author: Chris Runnells
Version: 1.1
Author URI: http://www.chrisrunnells.com 
*/  

/* version history

1.1 - added ability to choose between minimum order amount or minimum quantity

1.0 - first release


Todo:

- sanitize user inputs
- allow for BOTH minimum items AND minimum amount
- add custom error messages for Cart
- check for a specific product in the cart?
- set different minimums for different customer types (eg: wholesale)

*/

add_filter('shopp_validate_checkout','smo_check_minimums');
add_action('shopp_init_checkout','smo_check_minimums');
// add_action('shopp_cart_request','smo_cart_minimums');

if ( is_admin() ){
	add_action('admin_menu', 'smo_menu', 20);
}


function smo_menu() {
	add_submenu_page('shopp-settings','Minimum Order','Minimum Order','manage_options','shopp-minimum-order','smo_actions');
}


function smo_actions(){
	if($_POST['action'] == 'update'){
		smo_save();
	} else {
		smo_form();
	}
}


function smo_form() {

	$type = get_option('smo_type');
	$minimum = get_option('smo_minimum');

	$selected[0] = "";
	$selected[1] = "";
	$selected[2] = "";

	if(isset($type)){
		if($type == "quantity") $selected[0] = ' checked="checked"';
		if($type == "total") $selected[1] = ' checked="checked"';
		if($type == "") $selected[2] = ' checked="checked"';
	} else {
		$checked[2] = ' checked="checked"';
	}
?>
<style type="text/css">
#smo_form label {
	display: block;
	font-weight: bold;
}
#smo_form .caption {
	margin-left: 2em;
	font-style: italic;
}
</style>
<div class="wrap">
  <h2>Shopp Minimum Order</h2>
  
  <form name="smo_form" id="smo_form" method="post" action="<?php echo admin_url('admin.php?page=shopp-minimum-order'); ?>">
	<h3>Minimum Type</h3>
	<ul>
	  <li><label><input type="radio" name="smo_type" value="quantity"<?php echo $selected[0]; ?>> Order Quantity</label> <span class="caption">Use this option for a minimum number of total items in the order.</span></li>
	  <li><label><input type="radio" name="smo_type" value="total"<?php echo $selected[1]; ?>> Order Total</label> <span class="caption">Use this option for a minimum order total in your currency.</span></li>
	  <li><label><input type="radio" name="smo_type" value=""<?php echo $selected[2]; ?>> Disabled</label></li>
    </ul>
	    
    <p><label>Set Minimum Amount: <input type="text" name="smo_minimum" value="<?php echo $minimum; ?>" size="5"></label></p>  
    <p class="caption"><em>Note: Please just enter a number, do not add currency signs. eg: '35' or '35.00' instead of '$35'.</em></p>
  
    <p class="submit">  
	  <input type="hidden" name="action" value="update">
	  <input type="submit" name="Submit" value="Save">
    </p>  
  </form>
</div>  

<?php
}


function smo_save(){
	$type = $_POST['smo_type'];
	$minimum = $_POST['smo_minimum'];
	
	update_option('smo_type', $type);
	update_option('smo_minimum', $minimum);
	
	smo_form();
}


function smo_check_minimums ($valid) {
	global $Shopp;

	$type = get_option('smo_type');
	$minimum = get_option('smo_minimum');

	// if type is "total", check against the order subtotal
	if(isset($type) && !empty($type)){
		if($type == "total"){
		
			$subtotal = floatval(str_replace('$',null,shopp('cart','get-subtotal')));
 
			if (SHOPP_DEBUG) new ShoppError('Total minimum: '. $minimum . ' sub-total: '. $subtotal, false, SHOPP_DEBUG_ERR);
 
			if($subtotal < $minimum){
				new ShoppError('The minimum order amount is '.money($minimum).'. Please add more items to complete your order.','cart_validation');
				return false;
			}
		} else if ($type == "quantity"){
			
			$total_items = shopp('cart', 'get-total-quantity');
			
			if (SHOPP_DEBUG) new ShoppError('Quantity minimum: '. $minimum . ' total items: '. $total_items, false, SHOPP_DEBUG_ERR);
			
			if($total_items < $minimum){
				new ShoppError('You must have at least '. $minimum .' items in your cart to check out. You currently have '. $total_items .' items.', 'cart_validation');
				return false;
			}
			
		}
	}

	// if the check is okay, or if the type hasn't been set yet.
	return true;
}


function smo_cart_minimums (){
	global $Shopp;

	$minimum = get_option('smo_minimum');

	if (SHOPP_DEBUG) new ShoppError('minimum: '. $minimum . ' total: '. shopp('cart','get-total'),false,SHOPP_DEBUG_ERR);

	if(floatval(shopp('cart','get-total')) < $minimum){
		if (SHOPP_DEBUG) new ShoppError("Minimum is ".money($minimum).", total is " . shopp('cart','get-total'),false,SHOPP_DEBUG_ERR);
		new ShoppError(__("Cart total does not exceed minimum order amount.","Shopp"));
	}
}
?>