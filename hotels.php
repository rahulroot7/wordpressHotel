<?php
global $HotelApi;
function my_page_title() {
    return ucfirst(get_query_var( 'pagename' )); // add dynamic content to this title (if needed)
}
add_action( 'pre_get_document_title', 'my_page_title' );
get_header();
$hotel_info = $HotelApi->get_hotel_info(get_query_var( 'hid' ));
$hotel_page = $HotelApi->get_hotel_page($_GET,get_query_var( 'hid' ));



extract($_GET);
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
/*1 Room - 1 Adult - No Children -*/
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<div class="sg_hotel-wrap-outer">
	<div class="site-container sg_hotel-wrap">
	<div class="hotel_detail_outer">
		<div class="back_to-more">
			<a href="#">More Hotels in <?= $address ?></a>
		</div>
	<div class="hotel-detail">
	<h1><?=  $hotel_info->name; ?></h1>
		<p><?=  $hotel_info->address; ?></p>
	</div>
	<div class="hotel-detail-bar">
		<div class="hotel-nav">
			<ul>
				<li><a href="#photos">Photos</a></li>
				<li><a href="#rooms">Rooms</a></li>
				<li><a href="#">Reviews</a></li>
				<li><a href="#">Details</a></li>
			</ul>
		</div>
		<div class="hotel-nav2">
			<p><?= $checkin ?> – <?= $checkout ?> </p>
			<h5>$<?= !empty($hotel_page)? array_sum($hotel_page->rates[0]->daily_prices) / count($hotel_page->rates[0]->daily_prices):'0' ?></h5>
			<a href="<?= !empty($hotel_page)?site_url('/bookroom/').$hotel_page->rates[0]->book_hash.'inkey0/'.get_query_var( "hid" ).'?'.http_build_query($_GET):"javascript:void(0);" ?>" class="btn book-room">Book Room</a>
		</div>
	</div>
	</div>
	</div>
</div>
	<div class="site-container sg_hotel-wrap sg_hotel">
	<div class="hotel-cro" id="photos">
		<div class="hotel-carousel">
			<section class="slider">
		        <div id="slider" class="flexslider">
		          <ul class="slides">
		          	<?php
		          		foreach ($hotel_info->images as $key => $value) {
		          			?>
		          				<li><img src="<?= str_replace('{size}', '1024x768',$value); ?>" alt="hotel-img"></li>
		          			<?php
		          		}
		          	?>
		          </ul>
		        </div>
		        <div id="carousel" class="flexslider">
		          <ul class="slides">
		          		<?php
			          		foreach ($hotel_info->images as $key => $value) {
			          			?>
			          				<li><img src="<?= str_replace('{size}', '100x100',$value); ?>" alt="hotel-img"></li>
			          			<?php
			          		}
			          	?>
		          </ul>
		        </div>
		      </section>
		</div>
		<div class="hotel-map">
			 <div id="map"></div>
		</div>
	</div>
	<form >
	<div class="book_update">

			<div class="main">
		<div class="date_picker">
			<input class="form-control date_range" type="text" name="from_to" placeholder="Check-in - Check-out" value="<?= $from_to; ?>" required>
		</div>

		<div class="book_con">
			<a href="#"><?= $total_guests?></a>
		</div>

		<div class="book">
			<button type="submit" class="btn book_btn">Update Search</button>
		</div>
		</div>



	</div>

	<div class="tab-part" style="display:none;">
		<input type="hidden" name="nights" value="<?= $nights ?>" id="booking_nights">
		<input type="hidden" name="address" value="<?= $address ?>">
		<select name="" class="single_hotel_rooms count_infants">
			<?php

				for ($i=1; $i <=6 ; $i++) {
					?>
						<option value="<?= $i ?>" <?= (count($rooms) == $i)?'selected':'' ?>><?= $i ?> room</option>
					<?php
				}
			?>

		</select>

		<ul class="nav nav-tabs">
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
              </div></div>
						</div>
					<?php
					$s++;
				}
			?>


		</div>
	</div>
	</form>

	


	<div id="rooms">

	<?php
		if(empty($hotel_page)):
			?>
				<div class="roomDetail">
                    <div class="roomImg">
                        <img src="<?= trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/lost.jpg'; ?>">
                    </div>
                    <div class="roomCon">
                      	<h5 class="hotel_name">Your search has reached a dead end.</h5>
                    	<p>We tried very hard to find the perfect hotel but we couldn't match anything based on your filter selections.</p>
                    	<p>You can keep searching for more properties by resetting or adjusting your filters</p>
					</div>
                </div>
			<?php
		else:
		foreach ($hotel_page->rates as $key => $room):
			$room_data = $HotelApi->GetRoomImages($room->room_data_trans->main_name,$hotel_info->room_groups);
		?>
	<div class="roomDetail">
		<div class="roomImg">
		 <?php
			if(empty($room_data->images)){
				echo '<img src="https://fakeimg.pl/350x200/?text='.$room_data->room_data_trans->main_name.'">';
			}else{
				?>
		        	<img src="<?= str_replace('{size}', '240x240',$room_data->images[0]); ?>">
		        <?php
			}
		?>
		</div>
		<div class="roomCon">
			<h3><?= $room->room_data_trans->main_name ?></h3>
			<?php
				foreach($room->room_data_trans as $amenity)
				{
				    if(!empty($amenity)){
				    	$amenities[] = $amenity;
				    }
				}
			?>
			<p><?= implode(',',$amenities) ?></p>
		</div>
		<div class="roomPri">
			<h2>$ <?= array_sum($room->daily_prices) / count($room->daily_prices) ?></h2>
			<small>Avg Nightly Rate</small>
		</div>
		<div class="roombtn">
			<a href="<?= site_url('/bookroom/').$room->book_hash.'inkey'.$key.'/'.get_query_var( "hid" ).'?'.http_build_query($_GET) ?>" class="btn roomBtnInner">Book Room</a>
		</div>
	</div>
	<?php
	unset($amenities);
	endforeach;
	endif;
	?>
</div>
<div class="hotldetails">
	<div class="hotldetails-inner">
		<h4 class="lead">Hotel Details</h4>
		<?php
			$hotel_description = '';
	          if(!empty($hotel_info->description_struct)):
	            foreach ($hotel_info->description_struct as $key => $desc) {
	              $hotel_description .= '<h3>'.$desc->title.'</h3>';
	              foreach ($desc->paragraphs as $key1 => $para) {
	              	$hotel_description .= '<p>'.$para.'</p>';
	              }

	            }
	          endif;

		?>
		<div class="hotldetails-info">
			<?= $hotel_description ?>
		</div>
		<div class="hotldetails-group2">
		<div class="hotldetails-group">
			
			<?php

				foreach ($hotel_info->amenity_groups as $key => $amenities) {
					echo  '<div class="amens">';
					if($amenities->group_name == 'General'){
						echo '<p class="group_list"><i class="fas fa-hotel"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Rooms') {
						echo '<p class="group_list"><i class="fas fa-bed"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Accessibility') {
						echo '<p class="group_list"><i class="fas fa-wheelchair"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Room Amenities') {
						echo '<p class="group_list"><i class="fas fa-universal-access"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Meals') {
						echo '<p class="group_list"><i class="fas fa-weight"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Internet') {
						echo '<p class="group_list"><i class="fas fa-wifi"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Transfer') {
						echo '<p class="group_list"><i class="fas fa-car"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Languages Spoken') {
						echo '<p class="group_list"><i class="fas fa-language"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Tourist services') {
						echo '<p class="group_list"><i class="fas fa-place-of-worship"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Parking') {
						echo '<p class="group_list"><i class="fas fa-parking"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Pool and beach') {
						echo '<p class="group_list"><i class="fas fa-swimming-pool"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Business') {
						echo '<p class="group_list"><i class="fas fa-business-time"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Sports') {
						echo '<p class="group_list"><i class="fas fa-snowboarding"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Beauty and wellness') {
						echo '<p class="group_list"><i class="fas fa-spa"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Kids') {
						echo '<p class="group_list"><i class="fas fa-baby"><span>'.$amenities->group_name.'</span></i></p>';
					}
					else if ($amenities->group_name == 'Pets') {
						echo '<p class="group_list"><i class="fas fa-dog"><span>'.$amenities->group_name.'</span></i></p>';
					}

					echo '<ul>';
					if($amenities->group_name){
						foreach ($amenities->amenities as $key1 => $aminity) {
								echo '<li>'.$aminity.'</li>';
						}
					}
					echo '</ul>';
echo '</div>';
				}


			?>


	</div>
</div>
		<div class="hotldetails-note">
			<small>Note: It is the responsibility of the hotel chain and/or the individual property to ensure the accuracy of the photos displayed. Getme.pro is not responsible for any inaccuracies in the photos. The room rates listed are for double occupancy per room unless otherwise stated and exclude tax recovery charges and service fees.</small>
		</div>
	</div>
</div>
</div>
<script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB75FX0lcPpY9KdqeWrDtL5S6CXGpsGoys&callback=initMap&libraries=&v=weekly" async ></script>
<script type="text/javascript">
function initMap() {
  // The location of Uluru
  const uluru = { lat: <?= $hotel_info->latitude; ?>, lng: <?= $hotel_info->longitude; ?> };
  // The map, centered at Uluru
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 16,
    center: uluru,
  });
  // The marker, positioned at Uluru
  const marker = new google.maps.Marker({
    position: uluru,
    map: map,
  });
}
</script>
<?php
get_footer();
?>