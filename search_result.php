<?php
global $HotelApi;
function my_page_title() {
    return 'Hotel Search '; // add dynamic content to this title (if needed)
}
add_action( 'pre_get_document_title', 'my_page_title' );
$all_hotels = '[]';
if(!empty($_GET)){
	if(!isset($_GET['from_to'])){
		$_GET['from_to'] = date('Y-m-d').' - '.date('Y-m-d',strtotime('+1 day'));
	}
	extract($_GET);
  $all_hotels = $HotelApi->get_available_hotel($_GET);
  $checkin = date('D m/d' ,strtotime(explode(' - ',$from_to)[0]));
  $checkout = date('D m/d' ,strtotime(explode(' - ',$from_to)[1]));
  $total_guests = count($rooms).' Room'.$HotelApi->plural(count($rooms));
  $adults = array_column($rooms,'adults');
  $total_guests .= ' - '.array_sum($adults).' Adult'.$HotelApi->plural(array_sum($adults));
  $children = array_column($rooms,'children');
  $child = 0;
  foreach ($children as $type) {
      $child+= count($type);
  }
  if($child > 0){
    $total_guests .= ' - '.$child.' Children'.$HotelApi->plural($child);
  }else{
    $total_guests .= ' - No Children';
  }
  $total_guests .= ' - '.$nights.' Night'.$HotelApi->plural($nights);
  }else{
  	header('Location: '.site_url().'');
  }
get_header();
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<form class="search_update">
	<div class="book_update">
		<div class="site-container ">
			<div class="main">
				<div class="input-group desti-input">
			        <input type="text" id="autocomplete" class="form-control" name="address" value="<?= $address ?>" placeholder="City or airport" autocomplete="off" required>
			        <i class="fas fa-map-marker-alt"></i>
        		</div>
				<div class="date_picker desti-input">
					<input class="form-control date_range" type="text" name="from_to" placeholder="Check-in - Check-out" value="<?= $from_to; ?>" required>

					<i class="fa fa-calendar" aria-hidden="true"></i>
				</div>

				<div class="book_con desti-input">
					<a href="#"><?= $total_guests ?></a>
				</div>
				<div class="tab-part" style="display:none;">
		<input type="hidden" name="nights" value="<?= $nights ?>" id="booking_nights">
		<select name="" class="single_hotel_rooms count_infants">
			<?php

				for ($i=1; $i <=6 ; $i++) {
					?>
						<option value="<?= $i ?>" <?= (count($rooms) == $i)?'selected':'' ?>><?= $i ?> room</option>
					<?php
				}
			?>

		</select>

		<ul class="nav nav-tabs nav-pills nav-stacked">
			<?php
				$r = 1;
				foreach ($rooms as $key => $room) {
					$active = ($r == 1)?'active':'';
					echo '<li class="'.$active.' room_tabs" data-room="'.$r.'"><a href="javascript:void(0);">Room '.$r.'</a></li>';
					$r++;
				}
			?>

		</ul>

		<div class="b-part">
			<?php
				$s = 1;
				foreach ($rooms as $key => $room) {
					$active = ($s == 1)?'':'display:none;';
					?>
						<div class="single_room_<?= $s; ?> single_rooms" style="<?= $active ?>">
							<select name="rooms[<?= $s; ?>][adults]" class="adults count_infants">
								<?php
									for ($i=1; $i <=6 ; $i++) {
										?>
											<option value="<?= $i ?>" <?= ($room['adults'] == $i)?'selected':'' ?>><?= $i ?> adults</option>
										<?php
									}
								?>
							</select>

							<select name="" room='<?= $s; ?>' class="s_num_child count_infants">
								<option value="0">No Children</option>
					            <?php
									for ($i=1; $i <=4 ; $i++) {
										?>
											<option value="<?= $i ?>" <?= (isset($room['children']) && count($room['children']) == $i)?'selected':'' ?>><?= $i ?> Childrens</option>
										<?php
									}
								?>
							</select>
							<div class="children_<?= $s; ?>" <?= empty($room['children'])?"style='display:none;'":''; ?>><label class="chil-age">Children Age<span> (1-17, enter 0 for infants)</span></label>
					              <div class="chil-input">
					              		<?php
					              		if(!empty($room['children'])):
					              			foreach ($room['children'] as $key => $age) {
					              				echo '<input type="number" class="" value="'.$age.'"  name="rooms['.$s.'][children][]" required>';
					              			}
					              		endif;
					              		?>
					              </div>
          					</div>
						</div>
					<?php
					$s++;
				}
			?>
		</div>

	</div>

				<div class="book">
					<button type="submit" class="btn book_btn">Update Search</button>
				</div>
			</div>
		</div>
	</div>

	
</form>
<div class="background-gray">
<div class="site-container">

	<div class="hotel_search-wrapper">
	<div class="hotel_search-inner">
		<div class="hotels_filter-wrap">
			<div class="hotels_filter-inner">
				<div class="mapBox" id="mapBox">

				</div>
				<div class="filter-wrapper">
					<form>
						<div class="filter_inner filter_header">
							<div class="filter_header-inner">
								<p>FILTER RESULTS BY:</p>
								<button type="button" class="btn clear_filter-btn">Clear All</button>
							</div>
						</div>
						<div class="filter_inner filter_hotel-name">
							<h3>Hotel Name</h3>
							<div class="filter_fields">
								<input type="text" name="" id="filter_hotel_name" placeholder="Enter hotel name">
							</div>
						</div>
						<div class="filter_inner filter_amenities">
							<h3>Amenities</h3>
							<div class="filter_fields">
								<div class="filter_field-inner">
									<input type="checkbox" id="babysitting">
									<label for="babysitting">Babysitting Services</label>
								</div>
								<div class="filter_field-inner">
									<input type="checkbox" id="business">
									<label for="business">Business Center</label>
								</div>
								<div class="filter_field-inner">
									<input type="checkbox" id="cable-satellite">
									<label for="cable-satellite">Cable/Satellite TV</label>
								</div>
								<div class="filter_field-inner">
									<input type="checkbox" id="fitness">
									<label for="fitness">Fitness Center</label>
								</div>
								<div class="filter_field-inner">
									<input type="checkbox" id="breakfast">
									<label for="breakfast">Free Breakfast</label>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="hotels_filter-wrap">
			<div class="results-heading-wrap">
				<h3 class="results-heading"><?php if(!empty($all_hotels->hotels)){
					echo $all_hotels->total_hotels;
				}else{
					echo 'NO';
				}  ?> Hotels near <?= $address ?></h3>
			</div>
			<div class="short_by-outer">
				<div class="shourt_by-title">SORT BY :</div>
				<div class="result_shortby-wrap">
				<div class="result-shortby-inr">
					<input type="radio" name="results_sort_by" id="best-sellers" checked="">
					<label for="best-sellers">Best Sellers</label>
				</div>
				<div class="result-shortby-inr">
					<input type="radio" name="results_sort_by" id="lowest-price">
					<label for="lowest-price">Lowest Price</label>
				</div>
				<div class="result-shortby-inr">
					<input type="radio" name="results_sort_by" id="highest-price">
					<label for="highest-price">Highest Price</label>
				</div>
				<div class="result-shortby-inr">
					<input type="radio" name="results_sort_by" id="guest-rating">
					<label for="guest-rating">Guest Rating</label>
				</div>
				<div class="result-shortby-inr">
					<input type="radio" name="results_sort_by" id="star-rating">
					<label for="star-rating">Star Rating</label>
				</div>
			</div>
			</div>
			<ul class="filter_results-list">
        <?php
          $hotels_map = '';
          $hotels_loc = '';
          if(!empty($all_hotels->hotels)){
            foreach ($all_hotels->hotels as $key => $hotel) {

              $hotel_info = $HotelApi->get_hotel_info($hotel->id);
              if(!empty($hotel_info)){
              $hotel_image = isset($hotel_info->images[0])?str_replace('{size}', 'x500', $hotel_info->images[0]):'https://fakeimg.pl/500x500/?text='.$hotel_info->hotel_chain;
              $hotels_map .= "['$hotel_info->name', $hotel_info->latitude, $hotel_info->longitude, $key],";
              $hotels_loc = number_format((float)$hotel_info->latitude, 2, '.', '') .','.number_format((float)$hotel_info->longitude, 2, '.', '');
              $hotel_description = '';
              $star = '';
                for ($i=1; $i <= $hotel_info->star_rating; $i++) {
                   $star .='<span class="fa fa-star checked"></span>';
                }
                for ($i=1; $i <= 5-$hotel_info->star_rating; $i++) {
                   $star .='<span class="fa fa-star"></span>';
                }
              if(!empty($hotel_info->description_struct)):
                $hotel_description .= '<p>'.$hotel_info->description_struct[0]->paragraphs[0].'</p>';
              endif;
            ?>
              <li class="filter_results-item" data-price="<?= $hotel->rates[0]->daily_prices[0] ?>" data-star="<?= $hotel_info->star_rating ?>">
                    <div class="hotel_card">
                      <a href="<?= site_url('/hotels/').$hotel->id.'?'.http_build_query($_GET) ?>" class="filter_results-link"></a>
                      <div class="hotel_img-wrap">
                        <img src="<?= $hotel_image ?>">
                      </div>
                      <div class="hotel_content-wrap">
                        <div class="hotel_info-wrap">
                          <h5 class="hotel_name"><a href="<?= site_url('/hotels/').$hotel->id.'?'.http_build_query($_GET) ?>"><?= $hotel_info->name ?></a></h5>
                          <a href="<?= site_url('/hotels/').$hotel->id.'?'.http_build_query($_GET) ?>" class="hotel_loc"><?= $hotel_info->region->name ?></a>
                          <div class="ratings_stars">
                            <!-- <img src="https://www.getme.pro/wp-content/uploads/2021/07/stars.jpg"> -->
                            <?= $star ?>
                          </div>
                          <div class="features">
                            <span class="features_text"><?= $hotel_info->address ?></span>
                          </div>
                        </div>
                        <div class="hotel_price-wrap">
                          <div class="hotel_pricing">
                            <div class="hotel_price">
                              <span class="currency">$</span>
                               <span class="amount"><?= $hotel->rates[0]->daily_prices[0] ?></span>
                            </div>
                            <div class="price-per">avg. per night</div>
                          </div>
                        </div>
                      </div>
                    </div>
                </li>
            <?php
            }
          }
          }else{
              ?>
                  <li class="filter_results-item">
                      <div class="hotel_card">

                        <div class="hotel_img-wrap">
                          <img src="<?= trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/lost.jpg'; ?>">
                        </div>
                        <div class="hotel_content-wrap">
                          <div class="hotel_info-wrap">
                            <h5 class="hotel_name">Your search has reached a dead end.</h5>
                            <p>We tried very hard to find the perfect hotel but we couldn't match anything based on your filter selections.</p>
                            <p>You can keep searching for more properties by resetting or adjusting your filters</p>
                          </div>

                        </div>
                      </div>
                  </li>
              <?php
          }
        ?>

			</ul>
		</div>
	</div>
</div>
</div>
</div>
<script
      src="https://maps.google.com/maps/api/js?sensor=false&libraries=places&language=en-AU&key=AIzaSyB75FX0lcPpY9KdqeWrDtL5S6CXGpsGoys" type="text/javascript"></script>
<script type="text/javascript">
var locations = [<?= $hotels_map ?>];

    var map = new google.maps.Map(document.getElementById('mapBox'), {
      zoom: 12,
      center: new google.maps.LatLng(<?= $hotels_loc ?>),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();
    var marker, i;
    
    for (var i = 0; i < locations.length; i++) {
      let marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][2]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
    
    var autocomplete = new google.maps.places.Autocomplete(jQuery("#autocomplete")[0], {});

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                console.log(place.address_components);
            });
</script>
<!-- <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB75FX0lcPpY9KdqeWrDtL5S6CXGpsGoys&callback=initMap&libraries=&v=weekly" async ></script> -->
<?php

get_footer();
?>