<?php
global $HotelApi;
function my_page_title() {
    return 'Checkout';
}
add_action( 'pre_get_document_title', 'my_page_title' );
get_header();
$hotel_info = $HotelApi->get_hotel_info(get_query_var( 'hid' ));
$hotel_page = $HotelApi->get_hotel_page($_GET,get_query_var( 'hid' ));
extract($_GET);
$RoomData = $hotel_page->rates[(int)explode('inkey',get_query_var( 'rid' ))[1]];

$checkinTop = date('D, M d' ,strtotime(explode(' - ',$from_to)[0]));
$checkoutTop = date('D, M d' ,strtotime(explode(' - ',$from_to)[1]));
$checkinSide = date('D, M d, Y' ,strtotime(explode(' - ',$from_to)[0]));
$checkoutSide = date('D, M d, Y' ,strtotime(explode(' - ',$from_to)[1]));
$hotel_image = isset($hotel_info->images[0])?str_replace('{size}', 'x500', $hotel_info->images[0]):'https://fakeimg.pl/500x500/?text='.$hotel_info->hotel_chain;
$firstname = $lastname = $email = $street_address =  $state = $city = $zip = $country = '';

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
}
$star = '';
for ($i=1; $i <= $hotel_info->star_rating; $i++) {
   $star .='<span class="fa fa-star checked"></span><i class="fas fa-hand-o-right" aria-hidden="true"></i>';
}
for ($i=1; $i <= 5-$hotel_info->star_rating; $i++) {
   $star .='<span class="fa fa-star"></span><i class="fas fa-hand-o-right" aria-hidden="true"></i>';
}
$adults = array_column($rooms,'adults');

$children = array_column($rooms,'children');
$child = 0;
foreach ($children as $type) {
    $child+= count($type);
}
$countries = array( "AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo, the Democratic Republic of the", "CK" => "Cook Islands", "CR" => "Costa Rica", "CI" => "Cote D'Ivoire", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and Mcdonald Islands", "VA" => "Holy See (Vatican City State)", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran, Islamic Republic of", "IQ" => "Iraq", "IE" => "Ireland", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KP" => "Korea, Democratic People's Republic of", "KR" => "Korea, Republic of", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Lao People's Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libyan Arab Jamahiriya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macao", "MK" => "Macedonia, the Former Yugoslav Republic of", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia, Federated States of", "MD" => "Moldova, Republic of", "MC" => "Monaco", "MN" => "Mongolia", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territory, Occupied", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RE" => "Reunion", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda", "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "CS" => "Serbia and Montenegro", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic", "TW" => "Taiwan, Province of China", "TJ" => "Tajikistan", "TZ" => "Tanzania, United Republic of", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "UM" => "United States Minor Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela", "VN" => "Viet Nam", "VG" => "Virgin Islands, British", "VI" => "Virgin Islands, U.s.", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe" );
if(!empty($country)){
    $country = $countries[$country];
}
?>

<div class="site-container prog">
    <div class="progress-main">
        <div class="progress">
            <div class="percent"></div>
      </div>
      <div class="steps">
        <div class="step-content">
            <div class="step" id="0"><i class="fas fa-check tick"></i></div>
            <p class="data">Your Selection</p>
        </div>

      <div class="step-content">
        <div class="step" id="1"><i class="fas fa-check tick-1"></i></div>
        <p class="data">Your Details</p>
    </div>

    <div class="step-content">
        <div class="step final" id="2"><p>3</p></div>
          <p class="data">Final Step</p>
        </div>
      </div>
    </div>

</div>
<div class="site-container">

    <div class="rooBook">

        <div>

        <div class="rooBookForm">
             <div class="price-info">

                    <h5 class="price-details-title">Your Booking Details</h5>

                    <!-- <div class="price-subtitle"><?= $RoomData->room_data_trans->main_name ?></div> -->
                    <ul class="price-info-list">
                        <li class="price-info-item check-1">
                            <span class="price-info-title">Check-In:</span><br>
                            <span class="price-info-value"><?= $checkinSide ?></span>
                        </li>
                        <li class="price-info-item">
                            <span class="price-info-title">Check-Out:</span><br>
                            <span class="price-info-value"><?= $checkoutSide ?></span>
                        </li>


                    </ul>
                      <ul class="price-info-list border-bottom">
                        <li class="price-info-item">
                                <span class="price-info-title">Total length of stay:</span><br>
                                <span class="price-info-value"><?= $nights ?></span> <b>nights</b><br>
                                <span class="price-details-title blue"><a href="<?= site_url('/hotels/').get_query_var( 'hid' ).'?'.http_build_query($_GET) ?>">Travelling on Different dates?</a></span>
                                <!-- <div class="change_date">
                                    <form >
                                        <input class="form-control date_range" type="text" name="from_to" placeholder="Check-in - Check-out" value="<?= $from_to; ?>" required>
                                        <input type="hidden" name="nights" value="<?= $nights ?>" id="booking_nights">
                                        <input type="hidden" name="address" value="<?= $address ?>">
                                        <?php
                                            $s = 1;
                                            foreach ($rooms as $key => $room) {
                                                $active = ($s == 1)?'':'display:none;';
                                                ?>

                                                        <input type="hidden" name="rooms[<?= $s; ?>][adults]" value="<?= $room['adults'] ?>">
                                                <?php
                                                if(!empty($room['children'])):
                                                    foreach ($room['children'] as $key => $age) {
                                                        echo '<input type="hidden" class="" value="'.$age.'"  name="rooms['.$s.'][children][]" required>';
                                                    }
                                                endif;
                                                ?>

                                                <?php
                                                $s++;
                                            }
                                        ?>
                                        <button type="submit" class="btn book_btn">Update Search</button>
                                    </form>
                                </div> -->
                            </li>
                        </ul>
                    <ul class="price-info-list border-bottom">
                          <h5 class="price-details-title">Your Group</h5>
                        <li class="price-info-item">
                            <span class="price-info-title">Adults</span>
                            <span class="price-info-value"><?= array_sum($adults) ?></span>
                            <span class="price-info-title">Children</span>
                            <span class="price-info-value"><?= $child ?></span>
                        </li>
                    </ul>
                     <ul class="price-info-list">
                          <li class="price-info-item">
                            <h5 class="price-details-title">You selected:</h5>
                            <?php
                                $s = 1;
                                foreach ($rooms as $key => $room) {
                                    $ext = '';
                                    if(!empty($room['children'])){
                                        $ext = "(".$room['adults']." adult + ".count($room['children'])." child)";
                                    }
                            ?>
                                <span class="price-info-value font-normal">1 x <?= $RoomData->room_data_trans->main_name.$ext; ?></span><br>
                            <?php
                                $s++;
                            }
                            ?>
                            <span class="price-info-value font-normal blue"><a href="<?= site_url('/hotels/').get_query_var( 'hid' ).'?'.http_build_query($_GET) ?>">Change your selection</a></span>
                        </li>
                    </ul>

                </div>
                <div class="price-summ">
                    <h5 class="price-details-title">Your Price Summary</h5>
                    <span>$<?= array_sum($RoomData->daily_prices) / count($RoomData->daily_prices) ?></span>
                    <small>Average Nightly Rate</small>
                    <ul class="price-list">
                        <?php
                            $days = $HotelApi->date_range(explode(' - ',$from_to)[0], explode(' - ',$from_to)[1], "+1 day", "M d D");
                            foreach ($days as $key => $day) {
                                ?>
                                    <li class="price-item">
                                        <span class="price-title"><?= $day ?></span>
                                        <span class="price-value"><?= $HotelApi->get_currency_symbol() .' '.  $RoomData->daily_prices[$key] ?></span>
                                    </li>
                                <?php
                            }
                        ?>

                    </ul>
                    <ul class="price-list">
                        <li class="price-item">
                            <span class="price-title">Subtotal</span>
                            <span class="price-value orange"><?= $HotelApi->get_currency_symbol($RoomData->payment_options->payment_types[0]->show_currency_code) .' '. $RoomData->payment_options->payment_types[0]->amount ?></span>
                        </li>


                            <?php
                            $taxes = 0;
                                foreach ($RoomData->payment_options->payment_types[0]->tax_data->taxes as $key => $value) {
                                    echo '<li class="price-item">';
                                    echo '<span class="pric-title">'.$HotelApi->get_hotel_static('taxes',$value->name).'</span>';
                                    echo '<span class="price-value orange">'.$HotelApi->get_currency_symbol($value->currency_code) .' '.$value->amount.'</span>';
                                    echo '</li>';
                                    $taxes += $value->amount;
                                }

                            ?>

                        <li class="price-item grnd-total">
                            <span class="price-title">Grand Total in <?= $RoomData->payment_options->payment_types[0]->show_currency_code ?></span>
                            <span class="price-value orange"><?= $HotelApi->get_currency_symbol($RoomData->payment_options->payment_types[0]->show_currency_code).'  '; ?><?= $RoomData->payment_options->payment_types[0]->amount + $taxes ?></span>
                            <div class="tool-text">
                                <p>No Surprises! Final price.</p>
                            </div>
                        </li>

                        <span class="price-info-value"><p>(for  <?= $nights ?> nights & all guests)</p></span>
                    </ul>


                </div>
                <div class="room_duplex">

                    <h3><?= $RoomData->room_data_trans->main_name ?></h3>
                    <p><?= $RoomData->room_data_trans->bedding_type ?></p>
                    <?php
                      echo '<ul class = "room-list">';
                        if(!empty($RoomData->amenities_data)){
                            foreach ($RoomData->amenities_data as $key => $amenity_data) {
                                if($amenity_data == "double")
                                    echo '<li class="room_data"><i class="fas fa-bed"></i>' .$amenity_data.'</li>';
                                else if($amenity_data == "non-smoking")
                                     echo '<li><i class="fas fa-smoking"></i>'.$amenity_data.'</li>';
                                else if($amenity_data == "private-bathroom")
                                     echo '<li><i class="fas fa-toilet"></i>'.$amenity_data.'</li>';
                                else if($amenity_data == "window")
                                     echo '<li><i class="fas fa-dungeon"></i>'.$amenity_data.'</li>';
                                else
                                    echo '<li><i class="fas fa-industry"></i>' .$amenity_data.'</li>';
                            }
                        }
                         echo '</ul>';
                    ?>


                </div>

          <!--   <div class="bookFormHead">
                <h3><span><?=  $hotel_info->name; ?></span> <small>Book your stay for <?= $checkinTop ?> – <?= $checkoutTop ?>, <?= $nights ?> Night<?= $HotelApi->plural($nights);
                 ?></small></h3>
                <!-- <div class="breakfastSpred">
                    <h4><span>Breakfast spread</span> <small>Faraz16113 days ago</small></h4>
                </div> -->
           <!--  </div> -->

        </div>
                    <div class="about_booking">
                        <span  class="about_booking-inner">Free Cancellation before:
                         <?= empty($RoomData->payment_options->payment_types[0]->cancellation_penalties->free_cancellation_before)? "(Days before cancel)" : date(' F j',strtotime($RoomData->payment_options->payment_types[0]->cancellation_penalties->free_cancellation_before)); ?></span>
                    </div><br>

                    <div class="additional-info additional-info-cancellation-policy">
                      <h6><a class="collapsible" data-toggle="collapse" aria-expanded="true" aria-controls="#cancellationPolicyInfo" href="#">Cancellation Policy</a></h6>
                      <div id="cancellationPolicyInfo" class="panel-collapse collapse in">
                        <p>Your credit card is charged in full at the time you book a reservation.</p>
                        <p>Each room in this reservation is subject to the hotel's cancellation policy which is: Cancellations before <?= empty($RoomData->payment_options->payment_types[0]->cancellation_penalties->free_cancellation_before)? "'(Days before cancel)'" : date(' F j',strtotime($RoomData->payment_options->payment_types[0]->cancellation_penalties->free_cancellation_before)); ?> are fully refundable. Bookings cancelled after <?= empty($RoomData->payment_options->payment_types[0]->cancellation_penalties->free_cancellation_before)? "'(Days before cancel)'" : date(' F j',strtotime($RoomData->payment_options->payment_types[0]->cancellation_penalties->free_cancellation_before)); ?> are non-refundable. There is no refund for no-shows or early checkouts.</p>
                      </div>
                    </div>
                    <button type="button" class="collapsible policy">Additional Policies <i class="fas fa-chevron-down"></i></button>
                        <div class="content">
                          <!-- <p>Refund Policy: Refundable Children Policy: All children are welcome. One child under 2 years stays free of charge in a child's cot/crib. One older child or adult is charged 10 doller per night in an extra bed. The maximum number of extra beds in a room is 1. The maximum number of total guests in a room is 4. The maximum number of children's cots/cribs in a room is 1. Hotel Internet Policy: WiFi is available in all areas and is free of charge. Hotel Parking Policy: Free private parking is possible on site (reservation is not needed). Hotel Pet Policy: Pet policy vary depending on the hotel. Charges may be applicable. Hotel Preauthorize Policy: The property reserves the right to pre-authorise credit cards prior to arrival.</p> -->
                          <?php
                            foreach ($hotel_info->policy_struct as $policies) {
                                echo '<p><b>'.$policies->title.'</b> : ';
                                foreach ($policies->paragraphs as  $policy) {
                                    echo $policy;
                                }
                                echo '</p>';
                            }
                          ?>
                        </div><br>
                    <div class="term-condition">
                      <a href="JavaScript:void(0)" id="btnShow" value="Show Popup" >Terms And Conditions For This Booking</a>
                      <a data-toggle="collapse" aria-expanded="true" aria-controls="#cancellationPolicyInfo" href="https://www.getme.pro/privacy-policy/"> Privacy Policy</a>
                    </div>
                    <div id="dialog" style="display: none" align = "center">

                            <?php require_once(get_stylesheet_directory() . "/template/terms.php"); ?>
                    </div>


</div>





        <div class="roomRecipt">
             <div id="cancellationPolicyInfo" class="panel-collapse collapse in">
                        <p><i class="fas fa-info"></i>   In response to the coronavirus (COVID-19), additional safety and sanitation measures are in effect at<br> this property.</p>

                      <!--<button type="button" class="collapsible policy">Read more <i class="fas fa-chevron-down"></i></button>-->
                        <div class="content">

                        </div>
            </div>
            <div class="roomReciptHead">
                <div class="header-sec">
                <div class="left-img">
                <div class="roomReciptImg">
                    <img src="<?= $hotel_image ?>" width="100%">
                </div>
            </div>
            <div class="right-data">
                  <div class="ratings_stars">
                    <?= $star ?> <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="propety-data">
                	<h4 class="property-name"><a href="#"><?=  $hotel_info->name; ?> <?=  !empty($hotel_info->hotel_chain)?', an '.$hotel_info->hotel_chain:''; ?></a></h4>
                	<address class="property-address"><?=  $hotel_info->address; ?></address>
                    <!-- <p class="add-text-green">Guests have rated it <?= $hotel_info->star_rating ?>.</p> -->
                    <div class="beach-front">

                        <?php
                            foreach ($hotel_info->amenity_groups as $key => $amenities) {
                                $amen = array_slice($amenities->amenities, 0, 2);
                                if($amenities->group_name == 'Pool and beach'){
                                    echo '<p class="beach"><i class="fas fa-umbrella-beach"></i>'.implode('.', $amen).'</p>';
                                }else if ($amenities->group_name == 'Parking') {
                                    echo '<p class="beach"><i class="fas fa-parking"></i>'.implode('.', $amen).'</p>';
                                }else if ($amenities->group_name == 'Meals') {
                                    echo '<p class="beach"><i class="fas fa-utensils"></i>'.implode('.', $amen).'</p>';
                                }


                            }

                        ?>

                </div>
                </div>


  </div>
</div>


                    <div class="rooBookFormInner">
                <div class="form_wrap">
                    <form class="checkoutform">
                        <div class="book-rad">
                            <div class="booking-btn">
                                <p>Who are you booking for?</p>
                                <input type="radio" id="age1" name="age" value="30">
                                <label for="age1">I'm the main guest</label>
                            </div>

                            <div class="booking-btn new">
                                <div class="padding-top">
                                <input type="radio" id="age1" name="age" value="30">
                                <label for="age1">I'm booking for someone else</label>
                            </div>
                            </div>
                        </div>

                        <fieldset>
                            <div class="formInnerHead btn_inc">
                                <h5 class="form_inner-heading"> Room 1 <!-- <small><?= $RoomData->room_data_trans->main_name ?></small> --></h5>
                                 <?php if(!is_user_logged_in()){
                                    echo do_shortcode('[popup_anything id="1781"]');
                                }?>

                            </div>
                            <div class="input_wrap">
                               <label>First name</label>
                               <input type="text" id="" name="guests[1][first_name]" value="<?= $firstname ?>" required>
                            </div>
                        <div class="input_wrap">
                           <label for="">Last name</label>
                            <input type="text" id="" name="guests[1][last_name]" value="<?= $lastname ?>" required>
                        </div>
                        <div class="input_wrap">
                            <label for="">Mobile Phone Number in case we need to reach you</label>
                            <div class="phone_wrap">
                                <select name="countryCode" id="phone" required>
                                    <option data-countryCode="GB" value="44" Selected>UK (+44)</option>
                                    <option data-countryCode="US" value="1">USA (+1)</option>
                                    <optgroup label="Other countries">
                                        <option data-countryCode="DZ" value="213">Algeria (+213)</option>
                                        <option data-countryCode="AD" value="376">Andorra (+376)</option>
                                        <option data-countryCode="AO" value="244">Angola (+244)</option>
                                        <option data-countryCode="AI" value="1264">Anguilla (+1264)</option>
                                        <option data-countryCode="AG" value="1268">Antigua &amp; Barbuda (+1268)</option>
                                        <option data-countryCode="AR" value="54">Argentina (+54)</option>
                                        <option data-countryCode="AM" value="374">Armenia (+374)</option>
                                        <option data-countryCode="AW" value="297">Aruba (+297)</option>
                                        <option data-countryCode="AU" value="61">Australia (+61)</option>
                                        <option data-countryCode="AT" value="43">Austria (+43)</option>
                                        <option data-countryCode="AZ" value="994">Azerbaijan (+994)</option>
                                        <option data-countryCode="BS" value="1242">Bahamas (+1242)</option>
                                        <option data-countryCode="BH" value="973">Bahrain (+973)</option>
                                        <option data-countryCode="BD" value="880">Bangladesh (+880)</option>
                                        <option data-countryCode="BB" value="1246">Barbados (+1246)</option>
                                        <option data-countryCode="BY" value="375">Belarus (+375)</option>
                                        <option data-countryCode="BE" value="32">Belgium (+32)</option>
                                        <option data-countryCode="BZ" value="501">Belize (+501)</option>
                                        <option data-countryCode="BJ" value="229">Benin (+229)</option>
                                        <option data-countryCode="BM" value="1441">Bermuda (+1441)</option>
                                        <option data-countryCode="BT" value="975">Bhutan (+975)</option>
                                        <option data-countryCode="BO" value="591">Bolivia (+591)</option>
                                        <option data-countryCode="BA" value="387">Bosnia Herzegovina (+387)</option>
                                        <option data-countryCode="BW" value="267">Botswana (+267)</option>
                                        <option data-countryCode="BR" value="55">Brazil (+55)</option>
                                        <option data-countryCode="BN" value="673">Brunei (+673)</option>
                                        <option data-countryCode="BG" value="359">Bulgaria (+359)</option>
                                        <option data-countryCode="BF" value="226">Burkina Faso (+226)</option>
                                        <option data-countryCode="BI" value="257">Burundi (+257)</option>
                                        <option data-countryCode="KH" value="855">Cambodia (+855)</option>
                                        <option data-countryCode="CM" value="237">Cameroon (+237)</option>
                                        <option data-countryCode="CA" value="1">Canada (+1)</option>
                                        <option data-countryCode="CV" value="238">Cape Verde Islands (+238)</option>
                                        <option data-countryCode="KY" value="1345">Cayman Islands (+1345)</option>
                                        <option data-countryCode="CF" value="236">Central African Republic (+236)</option>
                                        <option data-countryCode="CL" value="56">Chile (+56)</option>
                                        <option data-countryCode="CN" value="86">China (+86)</option>
                                        <option data-countryCode="CO" value="57">Colombia (+57)</option>
                                        <option data-countryCode="KM" value="269">Comoros (+269)</option>
                                        <option data-countryCode="CG" value="242">Congo (+242)</option>
                                        <option data-countryCode="CK" value="682">Cook Islands (+682)</option>
                                        <option data-countryCode="CR" value="506">Costa Rica (+506)</option>
                                        <option data-countryCode="HR" value="385">Croatia (+385)</option>
                                        <option data-countryCode="CU" value="53">Cuba (+53)</option>
                                        <option data-countryCode="CY" value="90392">Cyprus North (+90392)</option>
                                        <option data-countryCode="CY" value="357">Cyprus South (+357)</option>
                                        <option data-countryCode="CZ" value="42">Czech Republic (+42)</option>
                                        <option data-countryCode="DK" value="45">Denmark (+45)</option>
                                        <option data-countryCode="DJ" value="253">Djibouti (+253)</option>
                                        <option data-countryCode="DM" value="1809">Dominica (+1809)</option>
                                        <option data-countryCode="DO" value="1809">Dominican Republic (+1809)</option>
                                        <option data-countryCode="EC" value="593">Ecuador (+593)</option>
                                        <option data-countryCode="EG" value="20">Egypt (+20)</option>
                                        <option data-countryCode="SV" value="503">El Salvador (+503)</option>
                                        <option data-countryCode="GQ" value="240">Equatorial Guinea (+240)</option>
                                        <option data-countryCode="ER" value="291">Eritrea (+291)</option>
                                        <option data-countryCode="EE" value="372">Estonia (+372)</option>
                                        <option data-countryCode="ET" value="251">Ethiopia (+251)</option>
                                        <option data-countryCode="FK" value="500">Falkland Islands (+500)</option>
                                        <option data-countryCode="FO" value="298">Faroe Islands (+298)</option>
                                        <option data-countryCode="FJ" value="679">Fiji (+679)</option>
                                        <option data-countryCode="FI" value="358">Finland (+358)</option>
                                        <option data-countryCode="FR" value="33">France (+33)</option>
                                        <option data-countryCode="GF" value="594">French Guiana (+594)</option>
                                        <option data-countryCode="PF" value="689">French Polynesia (+689)</option>
                                        <option data-countryCode="GA" value="241">Gabon (+241)</option>
                                        <option data-countryCode="GM" value="220">Gambia (+220)</option>
                                        <option data-countryCode="GE" value="7880">Georgia (+7880)</option>
                                        <option data-countryCode="DE" value="49">Germany (+49)</option>
                                        <option data-countryCode="GH" value="233">Ghana (+233)</option>
                                        <option data-countryCode="GI" value="350">Gibraltar (+350)</option>
                                        <option data-countryCode="GR" value="30">Greece (+30)</option>
                                        <option data-countryCode="GL" value="299">Greenland (+299)</option>
                                        <option data-countryCode="GD" value="1473">Grenada (+1473)</option>
                                        <option data-countryCode="GP" value="590">Guadeloupe (+590)</option>
                                        <option data-countryCode="GU" value="671">Guam (+671)</option>
                                        <option data-countryCode="GT" value="502">Guatemala (+502)</option>
                                        <option data-countryCode="GN" value="224">Guinea (+224)</option>
                                        <option data-countryCode="GW" value="245">Guinea - Bissau (+245)</option>
                                        <option data-countryCode="GY" value="592">Guyana (+592)</option>
                                        <option data-countryCode="HT" value="509">Haiti (+509)</option>
                                        <option data-countryCode="HN" value="504">Honduras (+504)</option>
                                        <option data-countryCode="HK" value="852">Hong Kong (+852)</option>
                                        <option data-countryCode="HU" value="36">Hungary (+36)</option>
                                        <option data-countryCode="IS" value="354">Iceland (+354)</option>
                                        <option data-countryCode="IN" value="91">India (+91)</option>
                                        <option data-countryCode="ID" value="62">Indonesia (+62)</option>
                                        <option data-countryCode="IR" value="98">Iran (+98)</option>
                                        <option data-countryCode="IQ" value="964">Iraq (+964)</option>
                                        <option data-countryCode="IE" value="353">Ireland (+353)</option>
                                        <option data-countryCode="IL" value="972">Israel (+972)</option>
                                        <option data-countryCode="IT" value="39">Italy (+39)</option>
                                        <option data-countryCode="JM" value="1876">Jamaica (+1876)</option>
                                        <option data-countryCode="JP" value="81">Japan (+81)</option>
                                        <option data-countryCode="JO" value="962">Jordan (+962)</option>
                                        <option data-countryCode="KZ" value="7">Kazakhstan (+7)</option>
                                        <option data-countryCode="KE" value="254">Kenya (+254)</option>
                                        <option data-countryCode="KI" value="686">Kiribati (+686)</option>
                                        <option data-countryCode="KP" value="850">Korea North (+850)</option>
                                        <option data-countryCode="KR" value="82">Korea South (+82)</option>
                                        <option data-countryCode="KW" value="965">Kuwait (+965)</option>
                                        <option data-countryCode="KG" value="996">Kyrgyzstan (+996)</option>
                                        <option data-countryCode="LA" value="856">Laos (+856)</option>
                                        <option data-countryCode="LV" value="371">Latvia (+371)</option>
                                        <option data-countryCode="LB" value="961">Lebanon (+961)</option>
                                        <option data-countryCode="LS" value="266">Lesotho (+266)</option>
                                        <option data-countryCode="LR" value="231">Liberia (+231)</option>
                                        <option data-countryCode="LY" value="218">Libya (+218)</option>
                                        <option data-countryCode="LI" value="417">Liechtenstein (+417)</option>
                                        <option data-countryCode="LT" value="370">Lithuania (+370)</option>
                                        <option data-countryCode="LU" value="352">Luxembourg (+352)</option>
                                        <option data-countryCode="MO" value="853">Macao (+853)</option>
                                        <option data-countryCode="MK" value="389">Macedonia (+389)</option>
                                        <option data-countryCode="MG" value="261">Madagascar (+261)</option>
                                        <option data-countryCode="MW" value="265">Malawi (+265)</option>
                                        <option data-countryCode="MY" value="60">Malaysia (+60)</option>
                                        <option data-countryCode="MV" value="960">Maldives (+960)</option>
                                        <option data-countryCode="ML" value="223">Mali (+223)</option>
                                        <option data-countryCode="MT" value="356">Malta (+356)</option>
                                        <option data-countryCode="MH" value="692">Marshall Islands (+692)</option>
                                        <option data-countryCode="MQ" value="596">Martinique (+596)</option>
                                        <option data-countryCode="MR" value="222">Mauritania (+222)</option>
                                        <option data-countryCode="YT" value="269">Mayotte (+269)</option>
                                        <option data-countryCode="MX" value="52">Mexico (+52)</option>
                                        <option data-countryCode="FM" value="691">Micronesia (+691)</option>
                                        <option data-countryCode="MD" value="373">Moldova (+373)</option>
                                        <option data-countryCode="MC" value="377">Monaco (+377)</option>
                                        <option data-countryCode="MN" value="976">Mongolia (+976)</option>
                                        <option data-countryCode="MS" value="1664">Montserrat (+1664)</option>
                                        <option data-countryCode="MA" value="212">Morocco (+212)</option>
                                        <option data-countryCode="MZ" value="258">Mozambique (+258)</option>
                                        <option data-countryCode="MN" value="95">Myanmar (+95)</option>
                                        <option data-countryCode="NA" value="264">Namibia (+264)</option>
                                        <option data-countryCode="NR" value="674">Nauru (+674)</option>
                                        <option data-countryCode="NP" value="977">Nepal (+977)</option>
                                        <option data-countryCode="NL" value="31">Netherlands (+31)</option>
                                        <option data-countryCode="NC" value="687">New Caledonia (+687)</option>
                                        <option data-countryCode="NZ" value="64">New Zealand (+64)</option>
                                        <option data-countryCode="NI" value="505">Nicaragua (+505)</option>
                                        <option data-countryCode="NE" value="227">Niger (+227)</option>
                                        <option data-countryCode="NG" value="234">Nigeria (+234)</option>
                                        <option data-countryCode="NU" value="683">Niue (+683)</option>
                                        <option data-countryCode="NF" value="672">Norfolk Islands (+672)</option>
                                        <option data-countryCode="NP" value="670">Northern Marianas (+670)</option>
                                        <option data-countryCode="NO" value="47">Norway (+47)</option>
                                        <option data-countryCode="OM" value="968">Oman (+968)</option>
                                        <option data-countryCode="PW" value="680">Palau (+680)</option>
                                        <option data-countryCode="PA" value="507">Panama (+507)</option>
                                        <option data-countryCode="PG" value="675">Papua New Guinea (+675)</option>
                                        <option data-countryCode="PY" value="595">Paraguay (+595)</option>
                                        <option data-countryCode="PE" value="51">Peru (+51)</option>
                                        <option data-countryCode="PH" value="63">Philippines (+63)</option>
                                        <option data-countryCode="PL" value="48">Poland (+48)</option>
                                        <option data-countryCode="PT" value="351">Portugal (+351)</option>
                                        <option data-countryCode="PR" value="1787">Puerto Rico (+1787)</option>
                                        <option data-countryCode="QA" value="974">Qatar (+974)</option>
                                        <option data-countryCode="RE" value="262">Reunion (+262)</option>
                                        <option data-countryCode="RO" value="40">Romania (+40)</option>
                                        <option data-countryCode="RU" value="7">Russia (+7)</option>
                                        <option data-countryCode="RW" value="250">Rwanda (+250)</option>
                                        <option data-countryCode="SM" value="378">San Marino (+378)</option>
                                        <option data-countryCode="ST" value="239">Sao Tome &amp; Principe (+239)</option>
                                        <option data-countryCode="SA" value="966">Saudi Arabia (+966)</option>
                                        <option data-countryCode="SN" value="221">Senegal (+221)</option>
                                        <option data-countryCode="CS" value="381">Serbia (+381)</option>
                                        <option data-countryCode="SC" value="248">Seychelles (+248)</option>
                                        <option data-countryCode="SL" value="232">Sierra Leone (+232)</option>
                                        <option data-countryCode="SG" value="65">Singapore (+65)</option>
                                        <option data-countryCode="SK" value="421">Slovak Republic (+421)</option>
                                        <option data-countryCode="SI" value="386">Slovenia (+386)</option>
                                        <option data-countryCode="SB" value="677">Solomon Islands (+677)</option>
                                        <option data-countryCode="SO" value="252">Somalia (+252)</option>
                                        <option data-countryCode="ZA" value="27">South Africa (+27)</option>
                                        <option data-countryCode="ES" value="34">Spain (+34)</option>
                                        <option data-countryCode="LK" value="94">Sri Lanka (+94)</option>
                                        <option data-countryCode="SH" value="290">St. Helena (+290)</option>
                                        <option data-countryCode="KN" value="1869">St. Kitts (+1869)</option>
                                        <option data-countryCode="SC" value="1758">St. Lucia (+1758)</option>
                                        <option data-countryCode="SD" value="249">Sudan (+249)</option>
                                        <option data-countryCode="SR" value="597">Suriname (+597)</option>
                                        <option data-countryCode="SZ" value="268">Swaziland (+268)</option>
                                        <option data-countryCode="SE" value="46">Sweden (+46)</option>
                                        <option data-countryCode="CH" value="41">Switzerland (+41)</option>
                                        <option data-countryCode="SI" value="963">Syria (+963)</option>
                                        <option data-countryCode="TW" value="886">Taiwan (+886)</option>
                                        <option data-countryCode="TJ" value="7">Tajikstan (+7)</option>
                                        <option data-countryCode="TH" value="66">Thailand (+66)</option>
                                        <option data-countryCode="TG" value="228">Togo (+228)</option>
                                        <option data-countryCode="TO" value="676">Tonga (+676)</option>
                                        <option data-countryCode="TT" value="1868">Trinidad &amp; Tobago (+1868)</option>
                                        <option data-countryCode="TN" value="216">Tunisia (+216)</option>
                                        <option data-countryCode="TR" value="90">Turkey (+90)</option>
                                        <option data-countryCode="TM" value="7">Turkmenistan (+7)</option>
                                        <option data-countryCode="TM" value="993">Turkmenistan (+993)</option>
                                        <option data-countryCode="TC" value="1649">Turks &amp; Caicos Islands (+1649)</option>
                                        <option data-countryCode="TV" value="688">Tuvalu (+688)</option>
                                        <option data-countryCode="UG" value="256">Uganda (+256)</option>
                                        <!-- <option data-countryCode="GB" value="44">UK (+44)</option> -->
                                        <option data-countryCode="UA" value="380">Ukraine (+380)</option>
                                        <option data-countryCode="AE" value="971">United Arab Emirates (+971)</option>
                                        <option data-countryCode="UY" value="598">Uruguay (+598)</option>
                                        <!-- <option data-countryCode="US" value="1">USA (+1)</option> -->
                                        <option data-countryCode="UZ" value="7">Uzbekistan (+7)</option>
                                        <option data-countryCode="VU" value="678">Vanuatu (+678)</option>
                                        <option data-countryCode="VA" value="379">Vatican City (+379)</option>
                                        <option data-countryCode="VE" value="58">Venezuela (+58)</option>
                                        <option data-countryCode="VN" value="84">Vietnam (+84)</option>
                                        <option data-countryCode="VG" value="84">Virgin Islands - British (+1284)</option>
                                        <option data-countryCode="VI" value="84">Virgin Islands - US (+1340)</option>
                                        <option data-countryCode="WF" value="681">Wallis &amp; Futuna (+681)</option>
                                        <option data-countryCode="YE" value="969">Yemen (North)(+969)</option>
                                        <option data-countryCode="YE" value="967">Yemen (South)(+967)</option>
                                        <option data-countryCode="ZM" value="260">Zambia (+260)</option>
                                        <option data-countryCode="ZW" value="263">Zimbabwe (+263)</option>
                                    </optgroup>
                                </select>
                                <input type="text" id="" name="phone" value="" required>
                            </div>
                        </div>
                        <div class="input_wrap">
                           <label for="">Email Address <small>we'll send the confirmation here</small></label>
                            <input type="email" id="" name="email" value="<?= $email ?>" required>
                        </div>
                         <?php if(!is_user_logged_in()){ ?>
                        <div class="optional_fields-wrap">
                            <div class="input_wrap">
                                <div class="optional_fields-outer">
                                    <div class="optional_fields-inr">
                                        <label for="">Add a password </label>
                                        <input type="password" id="password" name="password" value="" required onKeyUp="checkPasswordStrength();">
                                    </div>
                                    <div class="optional_fields-inr">
                                        <span class="optional_label">Optional</span>
                                        <div class="porgress_wrap">
                                            <span class="progress_label">Weak</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="smaller_text-info">Password must be at least 8 characters in length; use letters (uppercase and lowercase), numbers, and special characters.</span>
                            </div>
                            <div class="input_wrap">
                                <div class="flex_space-btw">
                                    <label for="">Confirm Password</label>
                                    <span class="optional_label">Optional</span>
                                </div>
                                <input type="password" id="confirm_password" name="confirmpassword" value="" required>
                                <span id='message'></span>
                            </div>
                        </div>
                        <?php } ?>
                        </fieldset>
                        <?php for ($i=2; $i <= count($rooms) ; $i++) {
                            ?>
                            <fieldset>
                                <div class="formInnerHead btn_inc">
                                    <h5 class="form_inner-heading">Guest Room <?= $i ?> <small><?= $RoomData->room_data_trans->main_name ?></small></h5>

                                </div>
                                <div class="input_wrap">
                                   <label>First name</label>
                                    <input type="text" id="" name="guests[<?= $i ?>][first_name]" value="" required>
                                </div>
                                <div class="input_wrap">
                                   <label for="">Last name</label>
                                    <input type="text" id="" name="guests[<?= $i ?>][last_name]" value="" required>
                                </div>
                            </fieldset>
                            <?php
                        } ?>

                        <fieldset>
                            <div class="formInnerHead">
                                <h5 class="form_inner-heading">Billing Info</h5>
                            </div>
                            <div class="input_wrap debit">
                                <div class="card_info-wrap" id="card-number-field">
                                    <label>Debit/Credit Card</label>
                                    <input type="text" id="cardNumber" name="credit_card_data_core[card_number]" value="" required>
                                </div>

                                <div class="card_icons" id="credit_cards">
                                    <img src="<?= trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/visa.jpg'; ?>" id="visa" class="transparent">
                                    <img src="<?= trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/mastercard.jpg'; ?>" id="mastercard" class="transparent">
                                    <img src="<?= trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/amex.jpg'; ?>" id="amex" class="transparent">
                                </div>

                            </div>
                            <div class="input_wrap">
                                <div class="card_sec">
                                    <div class="c-info">
                                        <label>CVV</label>
                                        <input type="text" id="cvv" name="cvv" value="" required>
                                    </div>

                                    <div>
                                        <label>Expiration Date</label>
                                        <select name="credit_card_data_core[month]" required>
                                            <option value="01">January</option>
                                            <option value="02">February </option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>

                                    <div class="date-part">

                                        <label class="hide-label">expire</label>
                                        <select name="credit_card_data_core[year]" required>
                                            <?php
                                                for ($i=date('Y'); $i < date('Y',strtotime('+18 years')); $i++) {
                                                   echo '<option value="'.substr( $i, -2 ).'"> '.$i.'</option>';
                                                }
                                            ?>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="formInnerHead">
                                <h5 class="form_inner-heading">Billing Address</h5>
                            </div>
                            <div class="input_wrap">
                               <label>First name</label>
                                <input type="text" id="" name="billing_first_name" value="<?= $firstname ?>" required>
                            </div>
                        <div class="input_wrap">
                           <label for="">Last name</label>
                            <input type="text" id="" name="billing_last_name" value="<?= $lastname ?>" required>
                        </div>
                        <div class="input_wrap">
                           <label for="">Country</label>
                           <select id="country" name ="billing_country" required></select>
                        </div>
                        <div class="input_wrap">
                           <label for="">Address</label>
                            <input type="text" id="" name="billing_address" value="<?= $street_address ?>" required>
                        </div>
                        <div class="input_wrap">
                            <div class="add_details">
                                <div class="add_details-inner Details-first">
                                    <label for="">City</label>
                                    <input type="text" id="" name="billing_city" value="<?= $city ?>" required>
                                </div>
                                <div class="add_details-inner Details-second">
                                    <label for="">State/Province</label>
                                    <select name ="billing_state" id ="state" required></select>
                                </div>
                                <div class="add_details-inner Details-third">
                                    <label for="">ZIP/Postal Code</label>
                                    <input type="text" id="" name="billing_zip" value="<?= $zip ?>" required>
                                    <input type="hidden" id="" name="action" value="checkout">
                                    <input type="hidden" id="" name="book_hash" value="<?= $RoomData->book_hash ?>">
                                </div>




                            </div>


                            <div class="check-part">
                                  <input type="checkbox" id="" name="" value="">
                                  <label for=""> Hotel Deal Newsletter! Subscribe and receive blockbuster deals.</label>

                                </div>

                                <div class="check-part">
                                  <input type="checkbox" id="" name="" value="" required>


                                  <label for=""> By booking this reservation, I agree to comply with the terms and conditions and agree to the cancellation policy. This reservation is fully refundable until <?= empty($RoomData->payment_options->payment_types[0]->cancellation_penalties->free_cancellation_before)? "'(Days before cancel)'" : date('m-d-Y h:i a',strtotime($RoomData->payment_options->payment_types[0]->cancellation_penalties->free_cancellation_before)); ?>. After this time, it is subject to the cancellation policy.</label>

                                </div>

                        </div>
                        </fieldset>
                        <div class="form_foot" style="text-align: center;">
                            <button type="submit" class="btn btn-orange book_submit">Agree & Book this room for <?= $RoomData->payment_options->payment_types[0]->show_currency_code.' '.$HotelApi->get_currency_symbol($RoomData->payment_options->payment_types[0]->show_currency_code).'  '; ?><?= $RoomData->payment_options->payment_types[0]->amount + $RoomData->payment_options->payment_types[0]->tax_data->taxes[0]->amount ?></button>
                        </div>
                    </form>
                </div>
            </div>


            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function(){
        <?php
            if(!empty($country)):

        ?>
        setTimeout(function(){
            jQuery('select[name=billing_country]').val('<?= $country ?>').trigger('change');
        }, 1500);
        setTimeout(function(){
          jQuery('select[name=billing_state]').val(jQuery("#state option:eq(<?= $state+1 ?>)").val());

        }, 2000);
    <?php endif; ?>
    });
</script>
<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}
</script>


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.9/jquery-ui.js" type="text/javascript"></script>
<link href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.9/themes/blitzer/jquery-ui.css"
    rel="stylesheet" type="text/css" />


<style>
.collapsible {
  background-color: #777;
  color: white;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
}

.active, .collapsible:hover {
  background-color: #555;
}

.content {
  padding: 0 18px;
  display: none;
  overflow: hidden;
  background-color: #f1f1f1;
}

div#cancellationPolicyInfo {background-color: #eaebea;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    padding: 10px 10px 1px;}

.additional-info h6 a {
    background-color: #626262;
    color: #eaebea;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    margin-bottom: 0;
    padding: 10px;
    font-family: Arial,sans-serif;
    font-weight: 600;
    display: block;
}

div#cancellationPolicyInfo p:nth-child(1) {
    margin: 0;
}

div#cancellationPolicyInfo p {
    font-size: 15px;
    color: #333;
}

.policy{font-family: Montserrat,Arial,sans-serif;
    color: #537c33!important;
    font-size: 16px;
    font-weight: 600;
    text-transform: capitalize;
    background: transparent!important;
    padding: 15px 0px; box-shadow: none!important;}




    .term-condition {
    padding: 0 0 30px;
}

.term-condition a{color: #337ab7;
    font-size: 15px;
    font-weight: 700;
    text-transform: capitalize;}

</style>
<?php
get_footer();
?>