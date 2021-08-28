
<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {

        wp_enqueue_style( 'chld_thm_ui_font_asm', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css', array(  ) ,time(),'All');
        wp_enqueue_style( 'chld_thm_ui_css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array(  ) ,time(),'All');
        wp_enqueue_style( 'chld_thm_daterange_css', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/daterangepicker.css', array(  ) ,time(),'All');
        wp_enqueue_style( 'chld_thm_flexslider_css', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/flexslider.css', array(  ) ,time(),'All');
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/ctc-style.css', array( 'kadence-global','kadence-header','kadence-content','kadence-footer','chld_thm_ui_css' ),time(),'All' );
        wp_enqueue_script( 'chld_thm_ui_js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( ),time(),true );
        wp_enqueue_script( 'chld_thm_moment_js', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/moment.min.js', array( ),time(),true );
        wp_enqueue_script( 'chld_thm_daterange_js', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/daterangepicker.min.js', array( ),time(),true );
        wp_enqueue_script( 'chld_thm_flexslider_js', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/jquery.flexslider.js', array(),time(),true );
        wp_enqueue_script( 'chld_thm_credit_js', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/jquery.payform.min.js', array(),time(),true );
        wp_enqueue_script( 'chld_thm_country_js', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/countries.js', array(),time(),true );
        wp_enqueue_script( 'chld_thm_form_js', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/form.js', array('chld_thm_ui_js','chld_thm_moment_js','chld_thm_daterange_js','chld_thm_country_js'),time(),true );
        wp_localize_script('chld_thm_form_js', 'api', array('ajaxurl' => admin_url('admin-ajax.php'),'countries'=>custom_umwoo_country_list_dropdown()));


    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

add_shortcode('HOTEL_SEARCH','hotel_search_form');

function hotel_search_form(){

    $filepath = get_stylesheet_directory() . "/template/hotel_form.php";

    ob_start();
    require $filepath;
    return ob_get_clean();
}
add_action("init" , 'hotel_custom_permalinks');
function hotel_custom_permalinks()
    {
       global $wp_rewrite;
       add_rewrite_rule(
            'search_result',
            'index.php?pagename=search_result',
            'top'
        );
        add_rewrite_rule(
            'hotels/([^/]*)/?',
            'index.php?pagename=hotels&hid=$matches[1]',
            'top'
        );
        add_rewrite_rule(
            'bookroom/([^/]*)/([^/]*)/?',
            'index.php?pagename=bookroom&rid=$matches[1]&hid=$matches[2]',
            'top'
        );
    }
 add_filter("query_vars","hotel_query_vars");
function hotel_query_vars($query_vars)
    {
        $query_vars[] = 'pagename';
        $query_vars[] = 'hid';
        $query_vars[] = 'rid';
        return $query_vars;
    }
add_action("template_include",'hotel_static_templates');
function hotel_static_templates($template)
    {
        if ( get_query_var( 'pagename' ) == false || get_query_var( 'pagename' ) == '' ) {
            return $template;
        }
        $ht_page = get_query_var( 'pagename' );
        $htpages = array('search_result','hotels','bookroom');
        if(in_array($ht_page, $htpages)){
            return get_stylesheet_directory() . "/template/{$ht_page}.php";
        }else{
            return $template;
        }

    }
add_action( 'pre_get_posts', 'rewrite_tag_permalink_pre_get_hotels' );

function rewrite_tag_permalink_pre_get_hotels( $query ){

    if ( $query->is_main_query() ){
        $ht_page = get_query_var( 'pagename' );
        $htpages = array('search_result','hotels','bookroom');
         if(in_array($ht_page, $htpages)){
            $query->is_home = false;
            $query->is_singular = true;
            $query->is_single = true;
            $query->is_paged = true;
         }
    }
}


add_action("wp_ajax_checkout", "book_room");
add_action("wp_ajax_nopriv_checkout", "book_room");

function book_room(){
    global $HotelApi;
    extract($_REQUEST);
    $booking_started = $HotelApi->book_room($book_hash);



    $payment = $HotelApi->GetPayments('now',$booking_started->payment_types);

    $payment1=$HotelApi->payment($booking_started->item_id);

    //$book['partner'] = $HotelApi->uuid();
    $book['language'] = 'en';
    $book['user'] = ['email' => $email,
                     'comment' => 'comment',
                     'phone' => '+'.$countryCode.$phone
                    ];
    $book['partner'] = ['partner_order_id' => $booking_started->partner_order_id];
    $book['return_path'] = 'https://www.getme.pro/';

    $book['rooms'][] = ['guests'=>array_values($guests)];
    $book['payment_type'] = ['type' => $payment->type,
                              'amount' => $payment->amount,
                              'currency_code' => $payment->currency_code,
                              'init_uuid'=>(string)$payment1['init_uuid'],
                              'pay_uuid'=>(string)$payment1['pay_uuid'],
                            ];
    $booking_finished = $HotelApi->booking_finish(json_encode($book));


    if($booking_finished->status == 'ok'){

         $user_id=get_current_user_id();
         if($user_id!=0){
            $booked_history= json_encode($booking_started);
             global $wpdb;
             $tablename = $wpdb->prefix.'booking_history';
             $wpdb->insert( $tablename, array(
                'user_id' => $user_id,
                'booking_data' => $booked_history, ),
                array( '%s', '%s')
            );
         }


        /*$to = $email;
        $subject = 'Booking Successfull';
        $body = 'Booked the room successfully';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $body, $headers );*/

    }
    print_r($booking_started);
    print_r($payment);
    print_r($payment1);
    print_r($booking_finished);
    die;
}
add_action( 'wp_print_footer_scripts', 'um_remove_scripts_and_styles', 9999 );
add_action( 'wp_print_scripts', 'um_remove_scripts_and_styles', 9999 );
add_action( 'wp_print_styles', 'um_remove_scripts_and_styles', 9999 );
function um_remove_scripts_and_styles() {
    global $post, $um_load_assets, $wp_scripts, $wp_styles;



    foreach ( $wp_scripts->queue as $key => $script ) {
        if ( strpos( $script, 'select2' ) === 0 || strpos( $script, 'um-' ) === 0 || strpos( $wp_scripts->registered[$script]->src, '/ultimate-member/assets/' ) !== FALSE ) {
            unset( $wp_scripts->queue[array_search('select2',$wp_scripts->queue,true)] );
        }
    }
}
function custom_umwoo_country_list_dropdown() {
    $countries = array( "AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo, the Democratic Republic of the", "CK" => "Cook Islands", "CR" => "Costa Rica", "CI" => "Cote D'Ivoire", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and Mcdonald Islands", "VA" => "Holy See (Vatican City State)", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran, Islamic Republic of", "IQ" => "Iraq", "IE" => "Ireland", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KP" => "Korea, Democratic People's Republic of", "KR" => "Korea, Republic of", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Lao People's Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libyan Arab Jamahiriya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macao", "MK" => "Macedonia, the Former Yugoslav Republic of", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia, Federated States of", "MD" => "Moldova, Republic of", "MC" => "Monaco", "MN" => "Mongolia", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territory, Occupied", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RE" => "Reunion", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda", "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "CS" => "Serbia and Montenegro", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic", "TW" => "Taiwan, Province of China", "TJ" => "Tajikistan", "TZ" => "Tanzania, United Republic of", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "UM" => "United States Minor Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela", "VN" => "Viet Nam", "VG" => "Virgin Islands, British", "VI" => "Virgin Islands, U.s.", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe" );
    return $countries;
}
function getStates( $has_parent = false ){ //get the value from the 'parent' field, sent via the AJAX post.

//get the value from the 'parent' field, sent via the AJAX post.

   $parent_options = isset( $_POST['parent_option'] ) ? $_POST['parent_option'] : false;

   $json = trim(file_get_contents(get_stylesheet_directory() . "/core/all_countries.json"), "\xEF\xBB\xBF");
   $all_options = json_decode($json,true);
   $countries = custom_umwoo_country_list_dropdown();
   $arr_options = array();

   if ( ! is_array( $parent_options ) ) {
      $parent_options = array( $parent_options );
   }
   foreach ( $parent_options as $parent_option ) {
      if ( isset( $all_options[ $countries[$parent_option] ] ) ) {
         $arr_options = array_merge( $arr_options, $all_options[ $countries[$parent_option] ] );

      } elseif ( ! isset( $_POST['parent_option'] ) ) {
         foreach ( $all_options as $k => $opts ) {
            $arr_options = array_merge( $opts, $arr_options );
         }
      }
   }

   //code to do something if other options are not selected or empty match
   if ( empty( $arr_options ) ) {
      $arr_options[ ] = "no states";
   } else {
      $arr_options = array_unique( $arr_options );
   }

   return $arr_options;
}
add_action("wp_ajax_get_state", "get_ajax_state");
add_action("wp_ajax_nopriv_get_state", "get_ajax_state");
function get_ajax_state()
{
    // print_r($_POST);
    // die;
   $parent_options = isset( $_POST['country'] ) ? $_POST['country'] : false;

   $json = trim(file_get_contents(get_stylesheet_directory() . "/core/all_countries.json"), "\xEF\xBB\xBF");
   $all_options = json_decode($json,true);
   $countries = custom_umwoo_country_list_dropdown();
   $arr_options = array();

   if ( ! is_array( $parent_options ) ) {
      $parent_options = array( $parent_options );
   }
   foreach ( $parent_options as $parent_option ) {
      if ( isset( $all_options[ $countries[$parent_option] ] ) ) {
         $arr_options = array_merge( $arr_options, $all_options[ $countries[$parent_option] ] );

      } elseif ( ! isset( $_POST['parent_option'] ) ) {
         foreach ( $all_options as $k => $opts ) {
            $arr_options = array_merge( $opts, $arr_options );
         }
      }
   }

   //code to do something if other options are not selected or empty match
   if ( empty( $arr_options ) ) {
      $arr_options[ ] = "no states";
   } else {
      $arr_options = array_unique( $arr_options );
   }
    echo json_encode($arr_options);
    die;
}
require_once(get_stylesheet_directory() . "/core/api.php");




//shortcode to display history

function order_history(){
    global $HotelApi;
    global $wpdb;
    $user_id=get_current_user_id();
    $results=$HotelApi->search_order();
    $orders=$results->data->orders;

    ob_start(); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <table id="table_id" class="display">
    <thead>
        <tr>
            <th>Order Id</th>
            <th>Checkin-date</th>
            <th>Invoice Id</th>
            <th>Order Type</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody> <?php
    foreach ($orders as $key => $value) {
           echo '<tr>';
           echo '<td>'.$value->order_id.'</td>';
           echo '<td>'.$value->checkin_at.'</td>';
           echo '<td>'.$value->invoice_id.'</td>';
           echo '<td>'.$value->order_type.'</td>';
           echo '<td>'.$value->amount_payable->amount.'</td>';
           echo '</tr>';
    }
        ?>
    </tbody>
</table>
   <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<script type="text/javascript">
    jQuery(document).ready( function () {
    jQuery('#table_id').DataTable();
} );
</script>
  <?php
    $output = ob_get_clean();
    echo $output;
}
add_shortcode('ORDER-HISTORY','order_history');
//customer account
function customer(){
     global $HotelApi;
     global $wpdb;
     $user=wp_get_current_user();
     $user_detail = json_decode(json_encode($user), true);
     $street_address = get_user_meta( $user->ID , 'billing_address', true );
     $dateOfBirth = get_user_meta( $user->ID , 'birth_date', true );

     // echo "<pre>";
     // print_r($user_detail);
     // print_r($user_detail['data']['display_name']);
     // echo "======================================";
     // print_r($user_detail['data']['user_email']);
     //  echo "======================================";
     // print_r($user_detail['data']['user_registered']);
     //echo date("M Y", strtotime(get_userdata(get_current_user_id( ))->user_registered));
    ob_start();
    ?>
<div class="custom-form">
    <div>
         <button type="button" class="btn btn-primary">Hotels</button>
         <button type="button" class="btn btn-secondary">Flight</button>
         <button type="button" class="btn btn-secondary">Car Rental</button>
    </div>
      <form>
        <div class="form-group">
        <label for="name">Name</label>
        <span><?php echo  $user_detail['data']['display_name'];   ?></span>
    </div>
    <div class="form-group">
        <label for="Birthday">Date Of Birth</label>
        <span><?php echo $dateOfBirth;   ?></span>
    </div>
    <div class="form-group">

    <label for="address">Address</label>
    <span><?php echo $street_address;   ?></span>
</div>
  <div class="form-group">

    <label for="anniversary">Anniversary Date</label>
    <span><?php echo "11/08/2021";   ?></span>
</div>
<div class="form-group">

    <label for="Anniversary Rewards">Anniversary Rewards</label>
    <span><?php echo "$10";   ?></span>
</div>
  <div class="form-group">
    <label for="exampleInputPassword1">Birthday discount</label>
    <span><?php echo "$15";   ?></span>
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Rewards </label>
    <span><?php echo "789 points";   ?></span>
</div>
<div class="earn">
    <p>Earn 1 point for every $1 spent</p>
</div>
</form>
</div>

 <?php
    $output = ob_get_clean();
    echo $output;

}
add_shortcode( 'customer', 'customer' );
// END ENQUEUE PARENT ACTION
add_action("wp_ajax_changeCustomPassword", "changeCustomPassword");
add_action("wp_ajax_nopriv_changeCustomPassword", "changeCustomPassword");
function changeCustomPassword(){

        extract($_POST);
        $current_user = get_userdata( get_current_user_id() );

        $response = [];
        if ( $current_user && wp_check_password( $currentPassword, $current_user->data->user_pass, $current_user->ID ) ) {
            if($newpassword == $rePassword){
                wp_set_password( $newpassword, get_current_user_id() );
                $creds = array(
                    'user_login'    => $current_user->data->user_login,
                    'user_password' => $currentPassword,
                    'remember'      => true
                );
             
                $user = wp_signon( $creds, false );
                $updated_data = wp_update_user( array( 'ID' => get_current_user_id(), 'user_email' => $ChangeEmail ) );
                if ( is_wp_error( $user_data ) ) {
                    $response['status']  = 400;
                    $response['message'] = "Something Went Wrong Please Try again later";
                } else {
                    $response['status']  = 200;
                    $response['message'] = "Password Changed successfully";
                }
            }else{
                $response['status']  = 400;
                $response['message'] = "Password not matched";
            }
        } else {
            $response['status']  = 400;
            $response['message'] = "Incorrect Current Password";
        }
        echo json_encode($response);
        die;
  
}
function changepassword(){
    ?>
<div class="custom-form p-form">
    <div>
         <p>Change Email Address and Password</p>
    </div>
      <form class="changepassword">
        <span id='idv'></span>
        <div class="form-group">
        <input type="hidden" name="action" value="changeCustomPassword">
        <label for="name">New email address</label>
        <input type="text" name="ChangeEmail" id="ChangeEmail" value="" required>
    </div>
    <div class="form-group">
        <label for="Birthday">Current password</label>
        <input type="password" name="currentPassword" id="currentPassword" value="" required>
        <span class="re-enter">Please re-enter your current password (required to save changes)</span>
    </div>
    <div class="form-group">
        <label for="address">New Password</label>
        <input type="password" name="newpassword" id="password" value="" required onKeyUp="checkPasswordStrength();">
    <div class="optional_fields-inr">
        <div class="porgress_wrap">
            <span class="progress_label">Weak</span>
        </div>
    </div>
   </div>
  <div class="form-group">
    <label for="anniversary">Re-enter password</label>
    <input type="password" name="rePassword" id="rePassword" value="" required>
    <span class="current">Password must be at least 8 characters in length; use letter (uppercase and lowercase),numbers and special charcters.</span>
 </div>
 <div class="form-group">
    <button type="submit" class="btn change_submit">Submit</button>
    <button type="cancel" class="btn cancel">Cancel</button>
</div>

</form>
</div>

 <?php

}
add_shortcode('change_password', 'changepassword');


add_action('um_after_account_general', 'showExtraFields', 100);
function showExtraFields()
{
    if(is_user_logged_in()){
    $user = wp_get_current_user();
    $firstname = $user->first_name;
    $lastname = $user->last_name;
    $email = $user->user_email;
    $street_address = get_user_meta( $user->ID , 'billing_address', true );
    $country = get_user_meta( $user->ID , 'billing_country', true );
    $state = get_user_meta( $user->ID , 'billing_state', true );
    $city = get_user_meta( $user->ID , 'billing_city', true );
    $zip = get_user_meta( $user->ID , 'billing_zip', true );


    $custom_fields = [

        "phone_select" =>['title'=> "Phone Number",
                          'class' => "sl-one choose_phone",
                          'type' => "select",
                          'placeholder' => "",
                           'options' => ['44' => 'UK (+44)',
                            '1' => 'USA (+1)',
                            '213' => 'Algeria (+213)',
                            '376' => 'Andorra (+376)',
                            '244' => 'Angola (+244)',
                            '1264' => 'Anguilla (+1264)',
                            '1268' => 'Antigua & Barbuda (+1268)',
                            '54' => 'Argentina (+54)',
                            '374' => 'Armenia (+374)',
                            '297' => 'Aruba (+297)',
                            '61' => 'Australia (+61)',
                            '43' => 'Austria (+43)',
                            '994' => 'Azerbaijan (+994)',
                            '1242' => 'Bahamas (+1242)',
                            '973' => 'Bahrain (+973)',
                            '880' => 'Bangladesh (+880)',
                            '1246' => 'Barbados (+1246)',
                            '375' => 'Belarus (+375)',
                            '32' => 'Belgium (+32)',
                            '501' => 'Belize (+501)',
                            '229' => 'Benin (+229)',
                            '1441' => 'Bermuda (+1441)',
                            '975' => 'Bhutan (+975)',
                            '591' => 'Bolivia (+591)',
                            '387' => 'Bosnia Herzegovina (+387)',
                            '267' => 'Botswana (+267)',
                            '55' => 'Brazil (+55)',
                            '673' => 'Brunei (+673)',
                            '359' => 'Bulgaria (+359)',
                            '226' => 'Burkina Faso (+226)',
                            '257' => 'Burundi (+257)',
                            '855' => 'Cambodia (+855)',
                            '237' => 'Cameroon (+237)',
                            '1' => 'Canada (+1)',
                            '238' => 'Cape Verde Islands (+238)',
                            '1345' => 'Cayman Islands (+1345)',
                            '236' => 'Central African Republic (+236)',
                            '56' => 'Chile (+56)',
                            '86' => 'China (+86)',
                            '57' => 'Colombia (+57)',
                            '269' => 'Comoros (+269)',
                            '242' => 'Congo (+242)',
                            '682' => 'Cook Islands (+682)',
                            '506' => 'Costa Rica (+506)',
                            '385' => 'Croatia (+385)',
                            '53' => 'Cuba (+53)',
                            '90392' => 'Cyprus North (+90392)',
                            '357' => 'Cyprus South (+357)',
                            '42' => 'Czech Republic (+42)',
                            '45' => 'Denmark (+45)',
                            '253' => 'Djibouti (+253)',
                            '1809' => 'Dominica (+1809)',
                            '1809' => 'Dominican Republic (+1809)',
                            '593' => 'Ecuador (+593)',
                            '20' => 'Egypt (+20)',
                            '503' => 'El Salvador (+503)',
                            '240' => 'Equatorial Guinea (+240)',
                            '291' => 'Eritrea (+291)',
                            '372' => 'Estonia (+372)',
                            '251' => 'Ethiopia (+251)',
                            '500' => 'Falkland Islands (+500)',
                            '298' => 'Faroe Islands (+298)',
                            '679' => 'Fiji (+679)',
                            '358' => 'Finland (+358)',
                            '33' => 'France (+33)',
                            '594' => 'French Guiana (+594)',
                            '689' => 'French Polynesia (+689)',
                            '241' => 'Gabon (+241)',
                            '220' => 'Gambia (+220)',
                            '7880' => 'Georgia (+7880)',
                            '49' => 'Germany (+49)',
                            '233' => 'Ghana (+233)',
                            '350' => 'Gibraltar (+350)',
                            '30' => 'Greece (+30)',
                            '299' => 'Greenland (+299)',
                            '1473' => 'Grenada (+1473)',
                            '590' => 'Guadeloupe (+590)',
                            '671' => 'Guam (+671)',
                            '502' => 'Guatemala (+502)',
                            '224' => 'Guinea (+224)',
                            '245' => 'Guinea - Bissau (+245)',
                            '592' => 'Guyana (+592)',
                            '509' => 'Haiti (+509)',
                            '504' => 'Honduras (+504)',
                            '852' => 'Hong Kong (+852)',
                            '36' => 'Hungary (+36)',
                            '354' => 'Iceland (+354)',
                            '91' => 'India (+91)',
                            '62' => 'Indonesia (+62)',
                            '98' => 'Iran (+98)',
                            '964' => 'Iraq (+964)',
                            '353' => 'Ireland (+353)',
                            '972' => 'Israel (+972)',
                            '39' => 'Italy (+39)',
                            '1876' => 'Jamaica (+1876)',
                            '81' => 'Japan (+81)',
                            '962' => 'Jordan (+962)',
                            '7' => 'Kazakhstan (+7)',
                            '254' => 'Kenya (+254)',
                            '686' => 'Kiribati (+686)',
                            '850' => 'Korea North (+850)',
                            '82' => 'Korea South (+82)',
                            '965' => 'Kuwait (+965)',
                            '996' => 'Kyrgyzstan (+996)',
                            '856' => 'Laos (+856)',
                            '371' => 'Latvia (+371)',
                            '961' => 'Lebanon (+961)',
                            '266' => 'Lesotho (+266)',
                            '231' => 'Liberia (+231)',
                            '218' => 'Libya (+218)',
                            '417' => 'Liechtenstein (+417)',
                            '370' => 'Lithuania (+370)',
                            '352' => 'Luxembourg (+352)',
                            '853' => 'Macao (+853)',
                            '389' => 'Macedonia (+389)',
                            '261' => 'Madagascar (+261)',
                            '265' => 'Malawi (+265)',
                            '60' => 'Malaysia (+60)',
                            '960' => 'Maldives (+960)',
                            '223' => 'Mali (+223)',
                            '356' => 'Malta (+356)',
                            '692' => 'Marshall Islands (+692)',
                            '596' => 'Martinique (+596)',
                            '222' => 'Mauritania (+222)',
                            '269' => 'Mayotte (+269)',
                            '52' => 'Mexico (+52)',
                            '691' => 'Micronesia (+691)',
                            '373' => 'Moldova (+373)',
                            '377' => 'Monaco (+377)',
                            '976' => 'Mongolia (+976)',
                            '1664' => 'Montserrat (+1664)',
                            '212' => 'Morocco (+212)',
                            '258' => 'Mozambique (+258)',
                            '95' => 'Myanmar (+95)',
                            '264' => 'Namibia (+264)',
                            '674' => 'Nauru (+674)',
                            '977' => 'Nepal (+977)',
                            '31' => 'Netherlands (+31)',
                            '687' => 'New Caledonia (+687)',
                            '64' => 'New Zealand (+64)',
                            '505' => 'Nicaragua (+505)',
                            '227' => 'Niger (+227)',
                            '234' => 'Nigeria (+234)',
                            '683' => 'Niue (+683)',
                            '672' => 'Norfolk Islands (+672)',
                            '670' => 'Northern Marianas (+670)',
                            '47' => 'Norway (+47)',
                            '968' => 'Oman (+968)',
                            '680' => 'Palau (+680)',
                            '507' => 'Panama (+507)',
                            '675' => 'Papua New Guinea (+675)',
                            '595' => 'Paraguay (+595)',
                            '51' => 'Peru (+51)',
                            '63' => 'Philippines (+63)',
                            '48' => 'Poland (+48)',
                            '351' => 'Portugal (+351)',
                            '1787' => 'Puerto Rico (+1787)',
                            '974' => 'Qatar (+974)',
                            '262' => 'Reunion (+262)',
                            '40' => 'Romania (+40)',
                            '7' => 'Russia (+7)',
                            '250' => 'Rwanda (+250)',
                            '378' => 'San Marino (+378)',
                            '239' => 'Sao Tome & Principe (+239)',
                            '966' => 'Saudi Arabia (+966)',
                            '221' => 'Senegal (+221)',
                            '381' => 'Serbia (+381)',
                            '248' => 'Seychelles (+248)',
                            '232' => 'Sierra Leone (+232)',
                            '65' => 'Singapore (+65)',
                            '421' => 'Slovak Republic (+421)',
                            '386' => 'Slovenia (+386)',
                            '677' => 'Solomon Islands (+677)',
                            '252' => 'Somalia (+252)',
                            '27' => 'South Africa (+27)',
                            '34' => 'Spain (+34)',
                            '94' => 'Sri Lanka (+94)',
                            '290' => 'St. Helena (+290)',
                            '1869' => 'St. Kitts (+1869)',
                            '1758' => 'St. Lucia (+1758)',
                            '249' => 'Sudan (+249)',
                            '597' => 'Suriname (+597)',
                            '268' => 'Swaziland (+268)',
                            '46' => 'Sweden (+46)',
                            '41' => 'Switzerland (+41)',
                            '963' => 'Syria (+963)',
                            '886' => 'Taiwan (+886)',
                            '7' => 'Tajikstan (+7)',
                            '66' => 'Thailand (+66)',
                            '228' => 'Togo (+228)',
                            '676' => 'Tonga (+676)',
                            '1868' => 'Trinidad & Tobago (+1868)',
                            '216' => 'Tunisia (+216)',
                            '90' => 'Turkey (+90)',
                            '7' => 'Turkmenistan (+7)',
                            '993' => 'Turkmenistan (+993)',
                            '1649' => 'Turks & Caicos Islands (+1649)',
                            '688' => 'Tuvalu (+688)',
                            '256' => 'Uganda (+256)',
                            '380' => 'Ukraine (+380)',
                            '971' => 'United Arab Emirates (+971)',
                            '598' => 'Uruguay (+598)',
                            '7' => 'Uzbekistan (+7)',
                            '678' => 'Vanuatu (+678)',
                            '379' => 'Vatican City (+379)',
                            '58' => 'Venezuela (+58)',
                            '84' => 'Vietnam (+84)',
                            '84' => 'Virgin Islands - British (+1284)',
                            '84' => 'Virgin Islands - US (+1340)',
                            '681' => 'Wallis & Futuna (+681)',
                            '969' => 'Yemen (North)(+969)',
                            '967' => 'Yemen (South)(+967)',
                            '260' => 'Zambia (+260)',
                            '263' => 'Zimbabwe (+263)',]
                                                ],
        "phone_number" =>['title'=> "",
                          'class' => "sl-two",
                          'type' => "text",
                          'placeholder' => "Phone Number",
                        ],

        "address" => ['title'=> "Address",
                      'class' => "",
                      'type' => "text",
                      'placeholder' => 'address',
                      'value' => $street_address,
                     ],
        "country" => ['title' => "Country",
                      'class' => "choose_country",
                      'type' => "select",
                      'placeholder' => 'country',
                      'options' => []


                     ],
        "city" => ['title' => "City",
                   'class' => "",
                   'type' => "text",
                   'placeholder' => 'City',
                   'value' => $city,
                  ],
        "state" => ['title' => "State",
                    'class' => "choose_state",
                    'type' => "select",
                    'placeholder' => 'state',
                    'options' => [],


                   ],
        "zip_code" => ['title' => "Zip code",
                      'class' => "",
                      'type' => "text",
                      'placeholder' => 'zip_code',
                      'value' => $zip,
                     ],
        ];

    foreach ($custom_fields as $key => $value) {

        $fields[ $key ] = array(
                'title' => $value,
                'metakey' => $key,
                'type' => 'select',
                'label' => $value,
        );

        apply_filters('um_account_secure_fields', $fields, 'general' );

        $field_value = get_user_meta(um_user('ID'), $key, true) ? : '';

        $html = '<div class="um-field um-field-'.$key.'" data-key="'.$key.'">
        <div class="um-field-label">
        <label for="'.$key.'">'.$value['title'].'</label>
        <div class="um-clear"></div>
        </div>
        <div class="um-field-area">';
        if($value['type'] == 'select'){
            $html .='<select class="'.$value['class'].'" name="'.$key.'" id="'.$key.'" data-validate="" data-key="'.$key.'">';
            foreach($value['options']  as  $option) {
                $html .='<option value="'.$option.'">'.$option.'</option>';
            }

        $html .='</select>';
        }else{
           $html .='<input class="'.$value['class'].'"
        type="'.$value['type'].'"
        name="'.$key.'"
        id="'.$key.'" value="'.$value['value'].'"
        placeholder="'.$value['title'].'"
        data-validate="" data-key="'.$key.'">';
        }
        $html .= '</div>
        </div>';

        echo $html;

    }
}
}

add_shortcode('addditonal_header', 'logged_navigation');

function logged_navigation(){

    if(is_user_logged_in()){
        $user=wp_get_current_user();
        $id=get_current_user_id();
        $user_id = 'G'.str_pad($id, 8, '0', STR_PAD_LEFT);
        $user_detail = json_decode(json_encode($user), true);
        echo '<div class="right-text">';
        echo   "<span><a href='#'>".$user_id."</a></span>";
        echo   "<span><a href='#'>".$user_detail['data']['user_email']."</a></span><br>";
        echo '</div>';
    }
}