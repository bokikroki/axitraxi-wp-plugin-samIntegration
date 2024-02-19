<?php
    /*
    Plugin Name: SAM Integration
    Description: Date input with pop-up calendar and availability update through SAM API
    Version: 1.0
    Author: Bluecode Solutions
    License: GPL_v2
    */
    
include "api.php";

wp_register_style( 'main_style', '/wp-content/plugins/samIntegration/css/samIntegration.css' );
wp_enqueue_style( 'main_style' );

add_action('woocommerce_before_shop_loop_item_title', 'synchronize_product_price');
add_action('woocommerce_before_single_product_summary','synchronize_product_price');
function synchronize_product_price() {
    global $woocommerce;
    global $axi_price;
    global $axi_sale_price;
    global $axi_product_id;
    
    $product_id = get_the_ID();
    $sku = get_post_meta( $product_id, '_sku', true );
    $product = json_decode( callApi("https://axitraxi.samrental.nl/api/webshop/v1/artikelen/zoeken?artikelnummer=$sku",null,'GET') );

    if(isset($product->id)) {
        $regular_price = number_format( $product->verhuurprijs,2,'.',',' );
        $sale_price = number_format( $product->verkoopprijs,2,'.',',' );
        $axi_price = $regular_price;
        $axi_sale_price = $sale_price;
        $axi_product_id = $product_id;
        $opbouw_uren = $product->opbouw_uren;
        $afbouw_uren = $product->afbouw_uren;
        update_post_meta( $product_id, '_regular_price', $regular_price );
        update_post_meta( $product_id, '_sale_price', $sale_price );
        update_post_meta( $product_id, '_opbouw_uren', $opbouw_uren );
        update_post_meta( $product_id, '_afbouw_uren', $afbouw_uren );
        wc_delete_product_transients( $product_id );
    };
}


// Adding date input to the product add to cart section
add_action('woocommerce_before_add_to_cart_button', 'add_datepicker');
function add_datepicker(){
    global $product;
    $terms = get_the_terms( $product->get_id(), 'product_cat' );
    $targetcatid = 23;
    $rentByHoursId = 7;
    $rent = true;
    foreach($terms as $category) {
        /* if($category->taxonomy === 'product_cat' && $category->term_taxonomy_id === $targetcatid) {
            $rent = false;
        } */
        
    }

    $rent = belongs_to_category_with_parent($product->get_id(), $rentByHoursId);

    if($rent) {

        echo '<div class="prijs-box" style="padding-left: 30px">';
        echo '<div id="loading-icon" class="loading-icon perpetuum-mobile"></div>';
        echo '<div>
                <h4 style="margin: 0 0 15px 0">Huurperiode</h4>
            </div>';
        echo '<div id="datePicks" class="datePicked_input" style="display: inline-block;">';
        //echo '<input type="text" id="start_datePicked" name="start_datePicked" class="begin" value=""><svg fill="#b1d3f0" xmlns="http://www.w3.org/2000/svg" height="25" viewBox="0 96 960 960" width="25"><path d="M197.694 955.999q-23.529 0-40.611-17.082-17.082-17.082-17.082-40.611V319.079q0-23.529 17.082-40.611 17.082-17.082 40.611-17.082h73.846v-70h50.384v70h317.691v-70h49.229v70h73.462q23.529 0 40.611 17.082 17.082 17.082 17.082 40.611v579.227q0 23.529-17.082 40.611-17.082 17.082-40.611 17.082H197.694Zm0-45.384h564.612q4.616 0 8.463-3.846 3.846-3.847 3.846-8.463V501.001h-589.23v397.305q0 4.616 3.846 8.463 3.847 3.846 8.463 3.846Zm-12.309-454.997h589.23V319.079q0-4.616-3.846-8.463-3.847-3.846-8.463-3.846H197.694q-4.616 0-8.463 3.846-3.846 3.847-3.846 8.463v136.539Zm0 0V306.77 455.618ZM480 659.077q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038T480 588.309q14.692 0 25.038 10.346t10.346 25.038q0 14.692-10.346 25.038T480 659.077Zm-160 0q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038T320 588.309q14.692 0 25.038 10.346t10.346 25.038q0 14.692-10.346 25.038T320 659.077Zm320 0q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038T640 588.309q14.692 0 25.038 10.346t10.346 25.038q0 14.692-10.346 25.038T640 659.077ZM480 816q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038 10.346-10.347 25.038-10.347t25.038 10.347q10.346 10.346 10.346 25.038t-10.346 25.038Q494.692 816 480 816Zm-160 0q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038 10.346-10.347 25.038-10.347t25.038 10.347q10.346 10.346 10.346 25.038t-10.346 25.038Q334.692 816 320 816Zm320 0q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038 10.346-10.347 25.038-10.347t25.038 10.347q10.346 10.346 10.346 25.038t-10.346 25.038Q654.692 816 640 816Z"/></svg>';
        echo '</div>';
        echo '<div id="datePicks" class="datePicked_input" style="display: inline-block;">';
        echo '<button onclick="addDatePicker()" type="button" style="background: transparent; border: none; color: #005cb9; display: inline-block; margin-left: 13px; margin-bottom: 13px; "><i class="fa fa-plus-circle" aria-hidden="true"></i> Dag toevoegen</button>';
        echo '<button onclick="removeDatePicker()" id="dag-verwij" type="button" style="background: transparent; border: none; color: #005cb9; display: none; margin-left: 13px;"><i class="fa fa-minus-circle" aria-hidden="true"></i> Dag verwijderen</button>';
        echo '</div>';
        echo '<div class="datePicked_input">';
        //echo '<input type="text" id="end_datePicked" name="end_datePicked" value=""><svg fill="#b1d3f0" xmlns="http://www.w3.org/2000/svg" height="25" viewBox="0 96 960 960" width="25"><path d="M197.694 955.999q-23.529 0-40.611-17.082-17.082-17.082-17.082-40.611V319.079q0-23.529 17.082-40.611 17.082-17.082 40.611-17.082h73.846v-70h50.384v70h317.691v-70h49.229v70h73.462q23.529 0 40.611 17.082 17.082 17.082 17.082 40.611v579.227q0 23.529-17.082 40.611-17.082 17.082-40.611 17.082H197.694Zm0-45.384h564.612q4.616 0 8.463-3.846 3.846-3.847 3.846-8.463V501.001h-589.23v397.305q0 4.616 3.846 8.463 3.847 3.846 8.463 3.846Zm-12.309-454.997h589.23V319.079q0-4.616-3.846-8.463-3.847-3.846-8.463-3.846H197.694q-4.616 0-8.463 3.846-3.846 3.847-3.846 8.463v136.539Zm0 0V306.77 455.618ZM480 659.077q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038T480 588.309q14.692 0 25.038 10.346t10.346 25.038q0 14.692-10.346 25.038T480 659.077Zm-160 0q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038T320 588.309q14.692 0 25.038 10.346t10.346 25.038q0 14.692-10.346 25.038T320 659.077Zm320 0q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038T640 588.309q14.692 0 25.038 10.346t10.346 25.038q0 14.692-10.346 25.038T640 659.077ZM480 816q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038 10.346-10.347 25.038-10.347t25.038 10.347q10.346 10.346 10.346 25.038t-10.346 25.038Q494.692 816 480 816Zm-160 0q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038 10.346-10.347 25.038-10.347t25.038 10.347q10.346 10.346 10.346 25.038t-10.346 25.038Q334.692 816 320 816Zm320 0q-14.692 0-25.038-10.346t-10.346-25.038q0-14.692 10.346-25.038 10.346-10.347 25.038-10.347t25.038 10.347q10.346 10.346 10.346 25.038t-10.346 25.038Q654.692 816 640 816Z"/></svg>';
        echo '</div>';
        echo '<div id="availability-alert" class="alert alert-danger">Dit product is niet beschikbaar<br>Kies een andere datum of een alternatief product</div>';
        echo '</div>';
        echo '<input type="hidden" id="days" name="days" />';
        echo '<input type="hidden" id="startDate" name="startDate" />';
        echo '<input type="hidden" id="endDate" name="endDate" />';
    }
}

/* add_action('woocommerce_before_add_to_cart_button', 'show_price');
function show_price(){
    global $product;

    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();

    echo '<div class="prijs-box">';
    echo'<div class="prijs-box afhalen">';
    echo '<div class="prijs-korting">';
    echo '<div class="row">';
    echo '<div class="huurprijs col-xs-8 col-md-8 col-lg-9">Verhuurprijs:</div>';
    echo '<div class="huurprijs col-xs-4 col-md-4 col-lg-3">€';
    echo "<span class='bedragen'>$regular_price</span></div>";
    echo '<div class="huurprijs col-xs-8 col-md-8 col-lg-9">Beursprijs:</div>';
    echo '<div class="huurprijs col-xs-4 col-md-4 col-lg-3">€';
    echo "<span class='bedragen'>$sale_price</span></div></div>";

} */

// Add datePicked to cart
add_filter('woocommerce_add_cart_item_data', 'add_datePicked_to_cart', 10, 2);
function add_datePicked_to_cart($cart_item_data, $product_id) {
    $datePicked = isset($_POST['datePicked']) ? $_POST['datePicked'] : '';
    if(!empty($datePicked)){
        $cart_item_data['datePicked'] = $datePicked;
    }
    return $cart_item_data;
}

// Add datePicked to order meta
add_action('woocommerce_checkout_create_order_line_item', 'add_datepicked_to_order', 10, 4);
function add_datepicked_to_order($item, $cart_item_key, $cart_item, $order){
    if(isset($cart_item['datePicked'])){
        $item->add_meta_data(__('datePicked', 'woocommerce'), $cart_item['datePicked'], true);
    }
}

// Show datePicked at order resume
add_filter('woocommerce_get_item_data', 'show_datePicked', 10, 2);
function show_datePicked($cart_item_data, $cart_item){
    if(isset($cart_item['datePicked'])){
        $cart_item_data[] = array(
            'name' => __('datePicked', 'woocommerce'),
            'value' => $cart_item['datePicked']
        );
    }
    return $cart_item_data;
}

// wp_enqueue_script(  'ajax-script', '/wp-content/plugins/datepicker/js/datepicker.js', array('jquery'), '1.0', true);
wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'check_availability' => 'check_availability') );

add_action( 'wp_ajax_check_availability', 'check_availability' );
add_action( 'wp_ajax_nopriv_check_availability', 'check_availability' );
function check_availability($date_start = null, $date_end = null, $sku = null, $ajax = true) {
    
    $date_start = $_POST['startDate'];
    $date_end = $_POST['endDate'];
    $sku = $_POST['sku'];

    $product = json_decode(callApi("https://axitraxi.samrental.nl/api/webshop/v1/artikelen/zoeken?artikelnummer=$sku",null,'GET'));
    $product_id = $product->id;
    $url = "https://axitraxi.samrental.nl/api/webshop/v1/beschikbare-voorraad?start=$date_start&eind=$date_end";
    $response = callApi($url, "{\"artikelen\":[$product_id]}", 'POST');

    echo $response;
    die;
    
}

function check_availability_cat ($date) {
    
    if($date === null) {
        $date = $_POST['startDate'];
    }

    $url = "https://axitraxi.samrental.nl/api/webshop/v1/voorraad";

    $response = json_decode(callApi($url, $date, 'GET'));

    return $response;
    
}


add_action( 'wp_ajax_calculate_transport', 'calculate_transport' );
add_action( 'wp_ajax_nopriv_calculate_transport', 'calculate_transport' );
function calculate_transport() {
    $postcode = $_POST['postcode'];
    $huisnummer = $_POST['huisnummer'];

    $postcode = str_replace(' ', '', $postcode);

    if ( isset(WC()->session) && ! WC()->session->has_session() ) {
        WC()->session->set_customer_session_cookie( true );
    }
    
    WC()->session->set('transport_pre_select_postcode', $postcode);
    WC()->session->set('transport_pre_select_number', $huisnummer);

    $transport = json_decode(callApi("https://axitraxi.samrental.nl/api/webshop/v1/transportkosten?postcode=$postcode&land=NL&huisnummer=$huisnummer",null,"GET"));
    $transport = number_format(floatval($transport->transportkosten),2,',','.');
    
    echo $transport; die;
}

function save_cart_data( $cart_item_key, $product_id = null, $quantity= null, $variation_id= null, $variation= null ) {
    
    global $product;
    
	if(!isset($product)){
        if($product_id != null) {
            $product = wc_get_product($product_id);
        }
	}
	
    $now = new \Datetime();
    $days = isset($_POST['days']) ? $_POST['days'] : 1 ;
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : $now->format('d-m-Y');
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : $now->format('d-m-Y');

	$price  = $product->get_regular_price();
    $prices = axi_calculate_price($price, $product, $days);
    
	$cart_totals['prices'] = $prices;
	$cart_totals['days'] = $days;
    $cart_totals['startDate'] = $startDate;
    $cart_totals['endDate'] = $endDate;

	WC()->session->set('cart_totals_' . $product->get_id(), $cart_totals);

}
add_action( 'woocommerce_add_to_cart', 'save_cart_data', 2, 5 );


/* add_filter('woocommerce_add_cart_item_data', 'calculate_cart_totals',10,2);
function calculate_cart_totals($cart_item_data, $product_id) {
    $cart_item_data->set('line_subtotal', 500);
    echo "<pre>"; var_dump($cart_item_data); echo "</pre>";
    echo "<pre>"; var_dump($product_id); echo "</pre>";
    return $cart_item_data;
} */

add_action( 'woocommerce_before_calculate_totals', 'modify_cart_subtotal' , 100);
 function modify_cart_subtotal( $cart ) {

    // This is necessary for WC 3.0+
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    // Avoiding hook repetition (when using price calculations for example | optional)
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;

//    foreach ( $cart->get_cart() as $cart_item ) {
//       $cart_item['data']->set_regular_price(500);
//       $cart_item['data']->set_price(800);
//    }
}

/* add_action( 'woocommerce_before_checkout_process', 'save_data_for_checkout' );
function save_data_for_checkout() {
    WC()->session->set( 'cart_data', $_POST );
} */

/* add_action( 'woocommerce_checkout_create_order', 'save_order_after_checkout', 10, 2 );
function save_order_after_checkout( $order, $data ) {
    $datos_carrito = $_POST['cart_data'];
    $order->update_meta_data( 'cart_data', $cart_data );
} */

/* 
function save_products_totals() {
echo "<pre>"; print_r(WC()->session->get('cart')); echo "</pre>"; die;
    
}
add_action( 'woocommerce_after_calculate_totals', 'save_products_totals', 2, 5 ); */

function get_sam_trasnport($country, $postcode, $addressNumber = null){
    $url = "https://axitraxi.samrental.nl/api/webshop/v1/transportkosten?postcode=$postcode&land=$country";
    if($addressNumber) {
        $url .= '&huisnummer=' . $addressNumber;
    }
    return json_decode(callApi($url, null, "GET"));
}

// Simple, grouped and external products
add_filter('woocommerce_product_get_price', 'axi_initial_calculate_price', 99, 2 );
add_filter('woocommerce_product_get_regular_price', 'axi_initial_calculate_price', 99, 2 );
// Variations 
add_filter('woocommerce_product_variation_get_regular_price', 'axi_initial_calculate_price', 99, 2 );
add_filter('woocommerce_product_variation_get_price', 'axi_initial_calculate_price', 99, 2 );

function axi_calculate_price($price, $product, $days = 1) {
    $price = floatval(str_replace(',', '', $price));
    $pakket2 = $price;
    $korting = 0;
    $days = (float)$days;
	if($days == 2) {
		$korting = $pakket2 * 0.25;
	}
	elseif($days>2) {
		$korting = $pakket2 * 0.5 * ($days - 2) + $pakket2 * 0.25;
	}
	$pakket1 = $pakket2 * $days - $korting; // option 1: they pick up the product
	$pakket2 = $days > 1 ? ($pakket2 * $days) - $korting : $pakket2; // option 2: delivery

    $assistance_article = json_decode( callApi("https://axitraxi.samrental.nl/api/webshop/v1/artikelen/381",null,'GET') );
    $assistance_price = get_field('two_assistance_workers',$product->id) ? $assistance_article->verkoopprijs * 2 : $assistance_article->verkoopprijs;

    $installation_cost_per_hour = (float)str_replace( ',', '.', get_option('options_uurtarief_in_euro'));

    $installation_hours = (get_post_meta( $product->id, '_opbouw_uren' ) && get_post_meta( $product->id, '_afbouw_uren' )) ? (float) get_post_meta( $product->id, '_opbouw_uren' )[0] + (float) get_post_meta( $product->id, '_afbouw_uren' )[0] : NULL;

	$pakket3 = $pakket2 + $installation_hours * $installation_cost_per_hour; // option 3: delivery + installation cost
	$pakket4 = $pakket2 + $installation_hours * $installation_cost_per_hour + ($assistance_price * $days); // option 4: delivery + installation cost + assitance cost
	$inclbtw = $pakket1 * 1.21;

    /* print_r("pakket2: ".$pakket2);
    print_r("installation_hours: ".$installation_hours);
    print_r("installation_cost_per_hour: ".$installation_cost_per_hour);
    print_r("pakket3: ".$pakket3); */

    $sale_price = $price; //(float)get_post_meta( $product->id, '_sale_price' )[0];
    //$base_price = $product->get_sale_price();

    $price = [
        'afhalen' => [
            'regular_price' => number_format($pakket1 * 0.90, 2),
            'base_price' => number_format($price, 2),
            'discount' => number_format($pakket1 * 0.1, 2),
            'inclbtw' => number_format($pakket1 * 0.90 * 1.21, 2)
        ],
        'bezorgen' => [
            'regular_price' => number_format($pakket2, 2),
            'base_price' => number_format($price, 2),
            'discount' => number_format($korting, 2),
            'inclbtw' => number_format($pakket2 * 1.21, 2)
        ],
        'opbouwen' => [
            'regular_price' => number_format($pakket3, 2),
            'base_price' => number_format($price, 2),
            'discount' => number_format($korting, 2),
            'inclbtw' => number_format($pakket3 * 1.21, 2)
        ],
        'begeleiden' => [
            'regular_price' => number_format($pakket4, 2),
            'base_price' => number_format($price, 2),
            'discount' => number_format($korting, 2),
            'inclbtw' => number_format($pakket4 * 1.21, 2)
        ],
        'kopen' => [
            'regular_price' => number_format($sale_price, 2),
            'base_price' => number_format($sale_price, 2),
            'discount' => number_format(0, 2),
            'inclbtw' => number_format($sale_price * 1.21, 2)
        ]
    ];

    return $price;
}


function axi_initial_calculate_price($price, $product) {

    $price = $product->get_data()['regular_price'];
    $prices = axi_calculate_price($price, $product);
    if(belongs_to_category_with_parent($product->get_id(), 7)) {
        $type = 'afhalen';
    } else {
        $type = 'kopen';
        return floatval(str_replace(',', '', $price));
    }

    return floatval($prices[$type]['base_price']);
}


wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'add_product_options_field' => 'axi_calculate_price_ajax') );

add_action('wp_ajax_add_product_options_field', 'axi_calculate_price_ajax');
add_action('wp_ajax_nopriv_add_product_options_field', 'axi_calculate_price_ajax');
function axi_calculate_price_ajax() {
    global $product;
	if(!isset($product)){
		$product = wc_get_product($_POST['product_id']);
	}
	$days = isset($_POST['days']) ? $_POST['days'] : 1;

    $prices = axi_calculate_price($product->get_regular_price(), $product, $days);

    echo json_encode($prices);
    die;
}

add_action('wp_ajax_add_begleiden_info_to_checkout', 'add_begleiden_info_to_checkout');
function add_begleiden_info_to_checkout() {
    
    session_start();

    $_SESSION['pickup_time_from'] = $_POST['pickup_time_from'];
    $_SESSION['pickup_time_to'] = $_POST['pickup_time_to'];
    $_SESSION['return_time_from'] = $_POST['return_time_from'];
    $_SESSION['return_time_to'] = $_POST['return_time_to'];
    $_SESSION['kind_of_ground'] = $_POST['kind_of_ground'];

    print_r($_SESSION['pickup_time_from']);
    print_r($_SESSION['pickup_time_to']);
    print_r($_SESSION['return_time_from']);
    print_r($_SESSION['return_time_to']);
    print_r($_SESSION['kind_of_ground']);
    
    wp_die();
}

function fill_weborder_request_from_order($order) {

    session_start();    

    $toSend = new \stdClass();
    $toSend->type = 'beurs';
    $toSend->projectstatus = 'aanvraag';

    // Contact info
    $contact = [
        "voornaam" => $order->get_billing_first_name(),
        "achternaam" => $order->get_billing_last_name(),
        "aanhef" => "O",
        "adres" => [
            "straat" => $order->get_billing_address_1(),
            "postcode" => $order->get_billing_postcode(),
            "plaats" => $order->get_billing_city(),
            "land" => $order->get_billing_country(),
            "huisnummer" => is_numeric(substr($order->get_billing_address_1(), strrpos($order->get_billing_address_1(), ' ') + 1))?
                                       substr($order->get_billing_address_1(), strrpos($order->get_billing_address_1(), ' ') + 1):
                                       "",
        ],
        'email' => $order->get_billing_email(),
        'telefoonnummers' => [$order->get_billing_phone()]
    ];

    $toSend->bezorgadres = [
        "straat" => $order->get_billing_address_1(),
        "postcode" => $order->get_billing_postcode(),
        "plaats" => $order->get_billing_city(),
        "land" => $order->get_billing_country(),
        "huisnummer" => is_numeric(substr($order->get_billing_address_1(), strrpos($order->get_billing_address_1(), ' ') + 1))?
                                    substr($order->get_billing_address_1(), strrpos($order->get_billing_address_1(), ' ') + 1):
                                    "",
    ];

    $toSend->ophaaladres = [
        "straat" => $order->get_billing_address_1(),
        "postcode" => $order->get_billing_postcode(),
        "plaats" => $order->get_billing_city(),
        "land" => $order->get_billing_country(),
        "huisnummer" => is_numeric(substr($order->get_billing_address_1(), strrpos($order->get_billing_address_1(), ' ') + 1))?
                                    substr($order->get_billing_address_1(), strrpos($order->get_billing_address_1(), ' ') + 1):
                                    "",
    ];

    $toSend->factuuradres = [
        "straat" => "test", //$order->get_shipping_address_1(),
        "postcode" => $order->get_shipping_postcode(),
        "plaats" => $order->get_shipping_city(),
        "land" => $order->get_shipping_country(),
        "huisnummer" => is_numeric(substr($order->get_shipping_address_1(), strrpos($order->get_shipping_address_1(), ' ') + 1))?
                                   substr($order->get_shipping_address_1(), strrpos($order->get_shipping_address_1(), ' ') + 1):
                                   "",
    ];

    // if (WC()->session->__isset( 'pickup_time_from' ) && WC()->session->__isset( 'pickup_time_to' )) {
        $toSend->aanvoertijd = [
            "van" => $_SESSION['pickup_time_from'],
            "tot" => $_SESSION['pickup_time_to'],
        ];
    // }
    // if (WC()->session->__isset( 'return_time_from' ) && WC()->session->__isset( 'return_time_to' )) {
        $toSend->retourtijd = [
            "van" => $_SESSION['return_time_from'],
            "tot" => $_SESSION['return_time_to'],
        ];
    // }
    // if (WC()->session->__isset( 'kind_of_ground' )) {
        $toSend->type_ondergrond = $_SESSION['kind_of_ground'];
    // }

    if(!empty($order->get_shipping_company())) {
        $contact['bedrijfsnaam'] = $order->get_shipping_company();
    }
    elseif(!empty($order->get_billing_company())) {
        $contact['bedrijfsnaam'] = $order->get_billing_company();
    }

    $toSend->contactgegevens = $contact;
    $toSend->correspondentietaal = "nl";

    $delivery = [
        "straat" => $order->get_shipping_address_1(),
        "postcode" => $order->get_shipping_postcode(),
        "plaats" => $order->get_shipping_city(),
        "land" => $order->get_shipping_country(),
        "huisnummer" => is_numeric(substr($order->get_shipping_address_1(), strrpos($order->get_shipping_address_1(), ' ') + 1))?
                                       substr($order->get_shipping_address_1(), strrpos($order->get_shipping_address_1(), ' ') + 1):
                                       "",
    ];

    $prod_ids = WC()->session->get('prod_ids');
    $subtotal = 0;
    $prices = array();
    foreach($prod_ids as $cart_item_key => $prod_id) {

        $session_prod = WC()->session->get('cart_totals_'.$prod_id);
        $prices[$prod_id]['days'] = $session_prod['days'];
        if(WC()->session->__isset( $cart_item_key.'_afhaal_fee' ) && WC()->session->get($cart_item_key.'_afhaal_fee')=="YES") {
            $prices[$prod_id]['price'] = $session_prod['prices']['afhalen']['regular_price'];
            $prices[$prod_id]['type'] = 'afhalen';
            $subtotal += $prices[$prod_id]['price'];
            continue;
        }
        if(WC()->session->__isset( $cart_item_key.'_begeleid_fee' ) && WC()->session->get($cart_item_key.'_begeleid_fee')=="YES") {
            $prices[$prod_id]['price'] = $session_prod['prices']['begeleiden']['regular_price'];
            $prices[$prod_id]['type'] = 'begeleiden';
            $subtotal += $prices[$prod_id]['price'];	
            continue;
        }
        if(WC()->session->__isset( $cart_item_key.'_opbouw_fee' ) && WC()->session->get($cart_item_key.'_opbouw_fee')=="YES") {
            $prices[$prod_id]['price'] = $session_prod['prices']['opbouwen']['regular_price'];
            $prices[$prod_id]['type'] = 'opbouwen';
            $subtotal += $prices[$prod_id]['price'];
            continue;
        }
        if(WC()->session->__isset( $cart_item_key.'_bezorg_fee' ) && WC()->session->get($cart_item_key.'_bezorg_fee')=="YES") {
            $prices[$prod_id]['price'] = $session_prod['prices']['bezorgen']['regular_price'];
            $prices[$prod_id]['type'] = 'bezorgen';
            $subtotal += $prices[$prod_id]['price'];
            continue;
        }
    }

    // Article info
    $articles = [];
    foreach ( $prices as $prod_id => $prod ) {

        if(!belongs_to_category_with_parent($prod_id, 7)) {
            continue;
        }
        
        /* $isBezorg = false;
        $isOpbouw = false;
        $isBegel  = false;

        foreach(WC()->cart->cart_contents as $cart_id => $cart) {
            if($cart['product_id'] === $item->get_product_id()) {
                if(WC()->session->get(  $cart_id . '_bezorg_feecart') === 'YES') {
                    $isBezorg = true;
                }
                if(WC()->session->get(  $cart_id . '_opbouw_feecart') === 'YES') {
                    $isOpbouw = true;
                }
                if(WC()->session->get(  $cart_id . '_begeleid_feecart') === 'YES') {
                    $isBegel = true;
                }
            }
        } */

        $article = [];

        //$article['artikel_id'] = $item->get_product_id();
        $article['artikelnaam'] = get_the_title($prod_id);
        $article['aantal'] = $prod['days'];
        $article['stukprijs'] = $prod['price'] / $prod['days'];
        $article['relatiekorting'] = 0;
        $article['relatiekorting_aantal_factuurperioden'] = 0;
        $article['stukprijs_aantal_factuurperioden'] = 0;


        $sku = get_post_meta( $prod_id, '_sku', true );
        $product = json_decode( callApi("https://axitraxi.samrental.nl/api/webshop/v1/artikelen/zoeken?artikelnummer=$sku",null,'GET') );

        //$article['artikel_id'] = $product->id;
        $article['artikelnummer'] = $sku;

        switch ($prod['type']){
            case 'afhalen':
                $toSend->levering = 'afhalen';
                break;
            case 'bezorgen': 
                $toSend->levering = 'bezorgen';
                $toSend->bezorgadres = $delivery;
                break;
            case 'opbouwen':
                $toSend->ophaaladres = $delivery; 
                $article['artikelnaam'].= ' + Opbouw en Afbouw';
                $toSend->op_en_afbouw = 'true';
                break;
            case 'begeleiden':
                $toSend->verzorging = [
                    "aantal_uur" => get_option('options_aantal_uur_begeleiding'),
                    "aantal_personen" => 1
                ];

                // Special product added for assistance service
                $assistance_article_origin = json_decode( callApi("https://axitraxi.samrental.nl/api/webshop/v1/artikelen/381",null,'GET') );
                $assistance_article['artikelnaam'] = $assistance_article_origin->artikelnaam;
                $assistance_article['aantal'] = get_field('two_assistance_workers',$prod_id) ? 2 : 1; // check 1 or 2 workers (custom field at product details)
                $assistance_article['stukprijs'] = $assistance_article_origin->verkoopprijs;
                $assistance_article['relatiekorting'] = 0;
                $assistance_article['relatiekorting_aantal_factuurperioden'] = 0;
                $assistance_article['stukprijs_aantal_factuurperioden'] = 0;
                $assistance_article['artikelnummer'] = $assistance_article_origin->artikelnummer;

                $articles[] = $assistance_article;

                break;
        }

        $articles[] = $article;
        
        $totals = WC()->session->get('cart_totals_' . $prod_id);
        $toSend->huurdatum_van = $totals['startDate'];
        $toSend->huurdatum_tot = $totals['endDate'];
        
    }
    
    $toSend->artikelen = $articles;
    
    return $toSend;

}

function create_weborder($order, $relation){
    $toSend = fill_weborder_request_from_order($order);
    $toSend->relatie_id = $relation;
    $toSend->contactpersoon_id = $relation;
    $toSend->transportkosten = (float)str_replace( ',', '.', WC()->session->get('Transport'));

    return json_decode(callApi("https://axitraxi.samrental.nl/api/webshop/v1/weborders", json_encode($toSend), "POST"));
}

function create_relation($data) {

    $toSend = [
        "aanhef" => "O",
        "voornaam" => $data['billing_first_name'],
        "achternaam" => $data['billing_last_name'],
        "adres" => [
            "straat" => $data['shipping_address_1'],
            "postcode" => $data['shipping_postcode'],
            "plaats" => $data['shipping_city'],
            "land" => $data['shipping_country']
        ],
        "email" => $data['shipping_email'],
        "telefoonnummers" => [
            $data['shipping_phone']
        ],
        "levering" => "afhalen",
        "correspondentietaal" => "nl"
    ];

    return json_decode(callApi("https://axitraxi.samrental.nl/api/webshop/v1/relaties/particulieren", json_encode($toSend), "POST"));
}

// string(412) "billing_first_name=Test&billing_last_name=McTest&billing_company=&billing_country=NL&wc_address_validation_postcode_lookup_postcode_house_number=&billing_address_1=Test%202&billing_postcode=6511NT&billing_city=Amsterdam&billing_state=&billing_phone=123456789&billing_email=test%40test.com&billing_options=&lang=nl&woocommerce-process-checkout-nonce=6a6309bf1e&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review"

function create_relation_from_order_update($data) {

    $data = str_replace('billing_first_name', 'voornaam', $data);
    $data = str_replace('billing_last_name', 'achternaam', $data);

    $data = explode('&', $data);

    $toSend = new \stdClass();
    $toSend->aanhef = "O";
    foreach($data as $entry) {
        $entryExp = explode('=', $entry);
        if(count($entryExp) === 2) {
            $toSend->{$entryExp[0]} = $entryExp[1];
        }
        
    }

    // $data = '{
    // "aanhef": "O",
    // "voornaam": "",
    // "tussenvoegsel": "string",
    // "achternaam": "string",
    // "adres": {
    //     "straat": "string",
    //     "huisnummer": "string",
    //     "postcode": "string",
    //     "plaats": "string",
    //     "land": "string"
    // },
    // "email": "string",
    // "telefoonnummers": [
    //     "string"
    // ],
    // "levering": "afhalen",
    // "correspondentietaal": "nl"
    // }';

    print_r(json_encode($toSend));

    return json_decode(callApi("https://axitraxi.samrental.nl/api/webshop/v1/relaties/particulieren", json_encode($toSend), "POST"));
}

function redirect_to_cart() {
    global $woocommerce;
    $cart_url = $woocommerce->cart->get_cart_url();
    return $cart_url;
}
// add_filter('add_to_cart_redirect', 'redirect_to_cart');
// function update_cart_for_guest_users() {
//     if (!is_user_logged_in()) {
//         global $woocommerce;
//         $woocommerce->session->set_customer_session_cookie(true);
//         $woocommerce->cart->calculate_totals();
//         $woocommerce->cart->maybe_set_cart_cookies();
//     }
// }
// add_action('woocommerce_add_to_cart', 'update_cart_for_guest_users');

function display_cart_items_table($prices) {
    foreach ($prices as $prod_id => $prod) {
        echo '<tr class="order_item">';
        echo '<td class="td" style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><div style="margin-bottom: 5px">';
        echo get_the_post_thumbnail($prod_id, 'thumbnail');
        echo '</div></td>';
        echo '<td class="td" style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><p style="color:#005CB9;margin:0 !important;font-weight:700;font-size:18px;">';
        echo get_the_title($prod_id).'</p>';
        echo '<p style="font-weigth:300;">';
        switch ($prod['type']){
            case 'afhalen':
                echo 'Afhalen'; break;
            case 'bezorgen':
                echo 'Bezorgen'; break;
            case 'opbouwen':
                echo 'Bezorgen + Opbouwen'; break;
            case 'begeleiden':
                echo 'Bezorgen + Opbouwen + Begeleiden'; break;
        }
        echo '</p></td>';
        echo '<td class="td" style="text-align:left; vertical-align:middle; border: 1px solid #eee;">';
        echo $prod['days'];
        echo '</td>';
        echo '<td class="td" style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">€</span>&nbsp;';
        echo strval(number_format($prod['price'],2,",","."));
        echo '</bdi></span></td></tr>';
    }
}

function display_cart_totals($subtotal, $totals) {
    $transport = WC()->session->get('Transport');
    foreach ( $totals as $total ) {
        if (($total['label']!="Verzending:")) {
            ?><tr>
                <th class="td" scope="row" colspan="3"><?php echo $total['label']; ?></th>
                <td class="td">
                    <?php 
                        switch ($total['label']) {
                            case 'Subtotaal:':
                                echo "€ ".strval(number_format($subtotal,2,",","."));
                                break;
                            case 'Transport:':
                                echo "€ ".$transport;
                                break;
                            case '21% BTW:':
                                echo "€ ".strval(number_format($subtotal * 0.21,2,",","."));
                                break;
                            case 'Betaalmethode:':
                                echo 'Offerte aanvraag'; 
                                break;
                            case 'Totaal incl. 21% BTW:':
                                echo "€ ".strval(number_format($subtotal * 1.21 + floatval(str_replace(",",".",$transport)),2,",","."));;
                        }
                    ?>
                </td>
            </tr><?php
        }
    }
}

add_action('woocommerce_email_after_order_table', 'filterEmailOrderComplete');
function filterEmailOrderComplete($order, $sent_to_admin, $plain_text, $email) {
    if(!empty($_SESSION['pickup_time_from'])) {
        echo '<p><strong>Pickup Time:</strong>'. $_SESSION['pickup_time_from'] . ' - ' . $_SESSION['pickup_time_to'] . '</p>';
    }
    if(!empty($_SESSION['return_time_from'])) {
        echo '<p><strong>Return Time:</strong>'. $_SESSION['return_time_from'] . ' - ' . $_SESSION['return_time_to'] . '</p>';
    }
}