
 <div class="hotel-form">

      <div class="hotel-form-head">
        <h2>Find a Hotel</h2>
      </div>
      <div class="hotel-form-inner">
        <form method="GET" action="<?php echo site_url('/search_result/'); ?>">
          <label class="desti">Destination</label>
        <div class="input-group desti-input">


        <input type="text" id="autocomplete" class="form-control" name="address" placeholder="City or airport" autocomplete="off" required>
        <i class="fas fa-map-marker-alt"></i>


        </div>

        <div class="input-group date-input">


<div class="form-for">

<div class="formcheck">
<label class="desti">Stay Dates</label>
<input class="form-control date_range" type="text" name="from_to" placeholder="Check-in - Check-out" value="" autocomplete="off" required>
<input type="hidden" name="nights" value="" id="booking_nights">
<i class="far fa-calendar-alt"></i>
</div>

<div class="nights">
<label class="desti">Nights</label>
<div class="booking_nights">
0
</div>
</div>

</div>

        </div>

   <div class="rooms">
          <div class="room_1 room_c">
            <label class="room">Rooms</label>
            <div class="room-input">

            <select class="form-control hotel_rooms" >
              <option value='1'>1 Room</option>
              <option value='2'>2 Rooms</option>
              <option value='3'>3 Rooms</option>
              <option value='4'>4 Rooms</option>
              <option value='5'>5 Rooms</option>
              <option value='6'>6 Rooms</option>
            </select>
            <select class="form-control" name='rooms[1][adults]'>
              <option value='1'>1 Adult</option>
              <option value='2'>2 Adults</option>
              <option value='3'>3 Adults</option>
              <option value='4'>4 Adults</option>
              <option value='5'>5 Adults</option>
              <option value='6'>6 Adults</option>
            </select>
            <select class="form-control s_num_child" room='1'>
              <option value="0">No Children</option>
              <option value='1'>1 Children</option>
              <option value='2'>2 Childrens</option>
              <option value='3'>3 Childrens</option>
              <option value='4'>4 Childrens</option>
            </select>
            </div>
            <div class="children_1"  style="display:none;">
              <label class="chil-age">Children Age<span> (1-17, enter 0 for infants)</span></label>
              <div class="chil-input"></div>
            </div>

          </div>

        </div>

        <div class="srch-btn">
        <button class="btn btn-primary">Search</button>
        </div>


        </form>
        



      </div>



</div>



 <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=places&language=en-AU&key=AIzaSyB75FX0lcPpY9KdqeWrDtL5S6CXGpsGoys"></script>
        <script>
            var autocomplete = new google.maps.places.Autocomplete(jQuery("#autocomplete")[0], {});

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                console.log(place.address_components);
            });
        </script>
