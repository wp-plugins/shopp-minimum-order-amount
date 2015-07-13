<?php
/*
Plugin Name: Shopp Minimum Order
Plugin URI: http://www.chrisrunnells.com/shopp/minimum-order-plugin/
Description: Set a minimum order amount (total items or order total) for Shopp checkout.
Author: Chris Runnells
Version: 1.3.4
Author URI: http://www.chrisrunnells.com


Todo:

- sanitize user inputs, better error checking for input vars (eg: make sure no floats are set for quantities)
- add custom error messages for Cart
- check if items are in the cart first before displaying error message(s)
- internationalization

Upcoming:
- set minimum amounts on specific products
- allow for BOTH minimum items AND minimum amount?
- set different minimums for different customer types (eg: wholesale)
- check minimum order amount against sub-total OR total
- check minimum against current stock levels

*/

add_filter( 'shopp_validate_checkout', 'smo_check_minimums' );
add_filter( 'shopp_themeapi_product_quantity', 'smo_filter_quantity' );

add_action( 'shopp_cart_add_item', 'smo_check_added_item' );
add_action( 'shopp_cart_updated', 'smo_check_item_minimums' );

add_action( 'shopp_cart_request', 'smo_check_item_minimums' );

add_action( 'shopp_init_checkout', 'smo_check_minimums' );
add_action( 'shopp_product_saved', 'smo_save_postdata' );

// admin stuff
add_action( 'admin_menu', 'smo_menu', 90 );
add_action( 'admin_menu', 'smo_meta', 20 );
add_filter( 'plugin_action_links', 'smo_plugin_action_links', 10, 2 );


function smo_menu () {
	// Shopp 1.3
	add_submenu_page( 'shopp-orders', 'Minimum Order', 'Minimum Order', 'manage_options', 'shopp-minimum-order', 'smo_actions' );
}

function smo_meta () {
	add_meta_box( 'smo_metabox', 'Set Minimum', 'smo_sidebar', 'shopp_product', 'side', 'default' );
}

function smo_plugin_action_links ( $links, $file ) {
    static $this_plugin;

    if ( ! $this_plugin )
    	$this_plugin = plugin_basename( __FILE__ );

    if ( $file == $this_plugin ) {
        // The "page" query string value must be equal to the slug of the Settings admin page
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=shopp-minimum-order">Settings</a>';
        array_unshift( $links, $settings_link );
    }

    return $links;
}

function smo_actions () {
	if ( $_POST['action'] == 'update' ) {
		smo_save();
	} else {
		smo_form();
	}
}

function smo_form ( $response = false ) {

	$type = get_option( 'smo_type' );
	$minimum = get_option( 'smo_minimum' );

	$selected = array( '', '', '' );

	if ( isset( $type ) ) {
		if($type == "quantity") $selected[0] = ' checked="checked"';
		if($type == "total") $selected[1] = ' checked="checked"';
		if($type == "") $selected[2] = ' checked="checked"';
	} else {
		$checked[2] = ' checked="checked"';
	}

	if ( $response )
		$response = '<div id="message" class="updated fade"><p>'.$response.'</p></div>';

	if ( defined('SHOPP_VERSION') && version_compare(SHOPP_VERSION, "1.3", "<") ) // 1.2
		$response .= '<div id="message" class="updated fade"><p>'. sprintf(__('This plugin has only been tested in Shopp version 1.3 and greater. Your current version is %d and we recommend upgrading to the latest version of Shopp.'), SHOPP_VERSION) .'</p></div>';

?>
<style type="text/css">
#smo_form label {
	display: block;
	font-weight: bold;
}
#smo_form .caption {
	display: block;
	margin-left: 2em;
	font-style: italic;
}
</style>
<div class="wrap">
  <div id="icon-options-general" class="icon32"></div>
  <h2><?php echo __('Shopp Minimum Order') ?></h2>
  <?php echo $response; ?>
  <form name="smo_form" id="smo_form" method="post" action="<?php echo admin_url('admin.php?page=shopp-minimum-order'); ?>">

	<p>Individual item quantities are not affected by these settings.</p>

	<h3><?php echo __('Minimum Type') ?></h3>

	<p>This sets the minimum quantity of items or the minimum order amount required before a customer can check out. If they do not meet the required minimums, a message will appear in the cart notifying the customer that they have not met the minimum.</p>

	<ul>
	  <li><label><input type="radio" name="smo_type" value="quantity"<?php echo $selected[0]; ?>> Order Quantity</label> <span class="caption">Use this option for a minimum number of total items in the order.</span></li>
	  <li><label><input type="radio" name="smo_type" value="total"<?php echo $selected[1]; ?>> Order Total</label> <span class="caption">Use this option for a minimum order total in your currency.</span></li>
	  <li><label><input type="radio" name="smo_type" value=""<?php echo $selected[2]; ?>> Disabled</label> <span class="caption">Individual product minimums will still work.</span></li>
    </ul>

    <p><label>Set Minimum Amount: <input type="text" name="smo_minimum" value="<?php echo $minimum; ?>" size="5" maxlength="10"></label>
    <span class="caption">Note: Please just enter a number, do not add currency signs. eg: '35' or '35.00' instead of '$35'.</p>

    <!--p><label>Error Message: (coming soon)<br /> <textarea name="smo_message" rows="4" cols="50"><?php echo $message; ?></textarea></label></p-->

    <p class="submit">
	  <input type="hidden" name="action" value="update">
	  <input class="button-primary" type="submit" name="Submit" value="Save Changes">
    </p>
  </form>
</div>

<?php
}


function smo_save () {
	// sanitize user input
	$type = sanitize_text_field($_POST['smo_type']);
	$minimum = (float) $_POST['smo_minimum'];

	update_option('smo_type', $type);
	update_option('smo_minimum', $minimum);

	smo_form("Your changes were saved.");
}

/* Meta box sidebar on the product page */
function smo_sidebar () {
	global $post;

	// minimum saved to shopp_product_meta()
	// except when it's a new product
	$minimum = $_GET['id'] != "new" ? shopp_product_meta($post->ID, 'minimum', 'meta') : "";
	$smo_min = ! empty($minimum) ? $minimum : "";

	// input field
	echo '<label>Quantity <input type="text" name="smo_min" value="'.$smo_min.'" size="3" placeholder="0" min="1"></label>';
	echo "<p><em>Set minimum to 0 to disable.</em></p>";
	echo "<p>The minimum quantity that will be added to the cart.</p>";
}

/* Save data from product page meta box */
function smo_save_postdata ( $Product ) {
    $minimum = (float) $_POST['smo_min'];
    if ( ! $minimum ) return;
    shopp_set_product_meta( $Product->ID, 'minimum', $minimum, 'meta' );
}

function smo_filter_quantity ( $result, $options, $Product ) {
	// check if this product has a minimum
	$minimum = smo_get_minimum($Product->id);
 	shopp_debug("Check catalog product: " . print_r($minimum, true));

	// if it does, try to set the minimum in the dropdown OR text area input (soon)
	return $result;
}

function smo_check_added_item ( $Item ) {
	// check if this item has minimums
	$minimum = smo_get_minimum($Item->product);
	if ( empty( $minimum ) ) return;

	shopp_debug( "Check added item minimum: " . print_r($minimum, true) );
	// set the quantity to the minimum if it's less
	if ( $Item->quantity < $minimum ) {
		$Item->quantity = $minimum;
		// drop a cart error to let the customer know what happened
		shopp_add_notice('The minimum quantity for ' . $Item->name . ' is ' . $minimum . '. Your cart has been updated.');
	}
}

function smo_check_item_minimums ( $Cart ) {

	// foreach item in the $Cart, check for minimums
	foreach ($Cart->shipped as $Item){
		$minimum = shopp_product_meta($Item->product, 'minimum', 'meta');

		if ( ! is_numeric( $minimum ) ) continue;

		if ($Item->quantity < $minimum ){
			// set the quantity to the minimum if it's less
			$Item->quantity = $minimum;

			// drop a cart error to let the customer know what happened
			// new ShoppError(__('The minimum quantity for ' . $Item->name . ' is ' . $minimum . '. Your cart has been updated.', 'Shopp'), false, SHOPP_ERR);
			shopp_add_notice('The minimum quantity for ' . $Item->name . ' is ' . $minimum . '. Your cart has been updated.');
		}

	}

}

function smo_check_minimums ( $valid ) {
	global $Shopp;

	$type = get_option('smo_type');
	$minimum = get_option('smo_minimum');

	// if type is "total", check against the order subtotal
	if ( isset($type) && !empty($type) && shopp_cart_items_count() > 0 ) {
		if ( $type == "total" ) {
			$Cart = ShoppOrder()->Cart;
			$subtotal = $Cart->total('order');
			$minimum = floatval( $minimum );

			if ( $subtotal < $minimum ){
				shopp_debug('Total minimum: '. $minimum . ' sub-total: '. $subtotal );

				new ShoppError('The minimum order amount is ' . money($minimum) . '. Please add more items to complete your order.','cart_validation');

				return false;
			}
		} else if ( $type == "quantity" ) {

			$total_items = shopp('cart', 'get-total-quantity');

			if ( $total_items < $minimum ) {
				shopp_debug('Quantity minimum: '. $minimum . ' total items: '. $total_items);
				new ShoppError('You must have at least '. $minimum .' items in your cart to check out. You currently have '. $total_items .' items.', 'cart_validation');
				return false;
			}

		}
	}

	// if the check is okay, or if the type hasn't been set yet.
	return true;
}

// Checks during checkout if minimum has been met
function smo_cart_minimums () {

	$minimum = get_option('smo_minimum');

	shopp_debug('minimum: '. $minimum . ' total: ' . shopp('cart.get-total'));

	if ( floatval( shopp('cart.get-total') ) < $minimum ){

		shopp_debug("Minimum is " . money($minimum) . ", total is " . shopp('cart.get-total'));

		// new ShoppError(__("Cart total does not exceed minimum order amount.","Shopp"));
		shopp_add_error("Cart total does not exceed minimum order amount.");
	}
}

/*
 * Shopp apparently returns an empty array if no meta record is found (instead of null)
 * so we have to check for that and return false
 */
function smo_get_minimum ( $id ) {
	if ( ! is_numeric($id) ) return;

	$min = shopp_product_meta($id, 'minimum', 'meta');

	if ( ! empty( $min ) ) return $min;

}

?>