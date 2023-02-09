<?php
//frontend booking from shortcode code
add_shortcode( 'schedule_booking_form', 'wp_schedulebull_booking' );
function wp_schedulebull_booking( $atts ) {
   ob_start();
   $getbookedpage="";
   $FromLocation="";
   $ToLocation="";
   $Outbound="";
   $fromPaSSenger="";
   $book_Price ="";
   $reserve_Price="";
   $TOtotalPrice="";
   //get api from server
   $getApikey=get_option( 'schedule_bull_api_key' );
    Global $wpdb;
    $getfromdata=$wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'from_location ORDER BY id ASC');
	if(!empty($_GET['booking_form'])){
    $getbookedpage=$_GET['booking_form'];
	}
	if($getbookedpage == 'true'){
	if(isset($_POST['bfsubmit'])){
	 $FromLocation=$_POST['bfromlocation'];
	 $ToLocation=$_POST['btlocation'];
	 $Outbound=$_POST['outbound_date'];
	 $fromPaSSenger=$_POST['fromPassenger'];
	 $returnDate=$_POST['returndate'];
	 $TOPassenger=$_POST['tOPassenger'];
	//get tolaocation return price \
	if($atts['fromlocation']  && $atts['tolocation'] ){
	$tolo_priceapi='https://schedulebull.com/api3.php?key='.$getApikey.'&q=transphere/routePrice&from='.$atts['fromlocation'].'&to='.$atts['tolocation'].'&datetime='.$returnDate.'';
	} else {
	    $tolo_priceapi='https://schedulebull.com/api3.php?key='.$getApikey.'&q=transphere/routePrice&from='.$FromLocation.'&to='.$ToLocation.'&datetime='.$returnDate.'';
	}
$discounttypeTO="";
$discountpriceTO="";
$response4 = file_get_contents($tolo_priceapi);
$fetchtodatePrice=json_decode($response4, true);
$promoCODE = $_POST['bpromocode'];	
 if(!empty($fetchtodatePrice)){
//$toLprice= $fetchtodatePrice['sum'];
if($TOPassenger >= 5) {
	      $percenthike = $fetchtodatePrice['sum']/100*10;
	      $toLprice=$fetchtodatePrice['sum']+$percenthike;
	   } else {
	        $toLprice=$fetchtodatePrice['sum'];
}
}

//Match promoCode from database return date
$getCouponbyretrundate=$wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'postmeta WHERE meta_key="hcf_promocode" and meta_value="'.$promoCODE.'"');
  //print_r($getCouponbynext);
  if(!empty($getCouponbyretrundate)){ 
    $discounttypeTO=get_post_meta($getCouponbyretrundate->post_id,'discounttype',true);
    $discountpriceTO=get_post_meta($getCouponbyretrundate->post_id, 'hcf_discountprice',true);
  } 
  
      $Tpassangers=$TOPassenger;
if($Tpassangers <= 8){
	$queuntity=1;	
}elseif($Tpassangers <= 16){
 $queuntity =2;	
}elseif($Tpassangers <= 24){
	 $queuntity=3;	
}
  
   if($discounttypeTO == '%'){
   $totalP=  $toLprice/100*$discountpriceTO;
    $couponDiscountTO1=$toLprice-$totalP;
    $couponDiscountTO=$couponDiscountTO1*$queuntity;
	 }
   else{
    $couponDiscountTO1= $toLprice-(int)$discountpriceTO; 
    $couponDiscountTO=$couponDiscountTO1*$queuntity;
   }

$fromto_apiurl='https://schedulebull.com/api3.php?key='.$getApikey.'&q=transphere/routePrice&from='.$FromLocation.'&to='.$ToLocation.'&datetime='.$Outbound.'';
$response = file_get_contents($fromto_apiurl);
$nextpagePrice=json_decode($response, true);
$totalPrice="";
$discounttype="";
$discountprice="";

$promoCODE = $_POST['bpromocode'];
if(!empty($nextpagePrice)){
//$totalPrice=$nextpagePrice['sum']; 
if($fromPaSSenger >= 5) {
	      $percenthike = $nextpagePrice['sum']/100*10;
	      $totalPrice=$nextpagePrice['sum']+$percenthike;
	   } else {
	       $totalPrice=$nextpagePrice['sum'];
	   } 
}
//Match promoCode from database from date
$getCouponbynext=$wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'postmeta WHERE meta_key="hcf_promocode" and meta_value="'.$promoCODE.'"');
  //print_r($getCouponbynext);
  if(!empty($getCouponbynext)){ 
     $discounttype=get_post_meta($getCouponbynext->post_id,'discounttype',true);
     $discountprice=get_post_meta($getCouponbynext->post_id, 'hcf_discountprice',true);
  } 
     $Fpassanger=$fromPaSSenger;
if($Fpassanger <= 8){
	$queuntity=1;	
}elseif($Fpassanger<= 16){
 $queuntity =2;	
}elseif($Fpassanger <= 24){
	 $queuntity=3;	
}
   if($discounttype == '%'){
   $totalP=  $totalPrice/100*$discountprice;
    $couponDiscount1=$totalPrice-$totalP;
    $couponDiscount=$couponDiscount1*$queuntity;
	 }
    else {
     $couponDiscount1= $totalPrice-(int)$discountprice; 
    $couponDiscount=$couponDiscount1*$queuntity;
   }
   //total price
   $from_to_totalprice=$couponDiscount+$couponDiscountTO;
    $book_Price='&euro;'.number_format($from_to_totalprice,2);
    $reserve_Price1=number_format($couponDiscount,2);
    $reserve_Price=str_replace(str_split('\\/:*?"<>|€,'),'', $reserve_Price1);
    $TOtotal_Price1=number_format($couponDiscountTO,2);
    $TOtotal_Price=str_replace(str_split('\\/:*?"<>|€,'),'', $TOtotal_Price1);
	}
/////////////
if(isset($_POST['ordersubmit'])){
    
	Global $wpdb;
	//get api from server
    $getApikey=get_option( 'schedule_bull_api_key' );
	$Customerfirstname = $_POST['customerName'];
	$customersurname   = $_POST['customersurName'];
	$customeremail     = $_POST['customerEmail'];
	$dialingcode       = $_POST['dialing_code'];
	$customermobilephone= $dialingcode.'-'.$_POST['mobile_phone'];
	$mob_dialcode2     = $_POST['2mob_dialcode'];
	$moible_number2    = $mob_dialcode2.'-'.$_POST['2moible_number'];
	$luggageinfo       = $_POST['luggage_info'];
	$childseats       = $_POST['child_seats'];
	$childseats2       = $_POST['child_seats2'];
	$childseats3       = $_POST['child_seats3'];
	$oversizedluggage = $_POST['oversized_luggage'];
	$flightarrivaltime = $_POST['flight_arrival_time'];
	$flightDeparturetime = $_POST['flight_Departure_time'];
	$hotelnameaddress = $_POST['hotel_name_address'];
	$paymentviastripe = $_POST['paymentviastripe'];
	$payremainingbalance = $_POST['remaining_blance'];
	$additionalnote   = $_POST['additional_note'];
	$comment = 'Email:'.$customeremail.'| 2nd mobile:'.$moible_number2.'| Luggage info:'.$luggageinfo.'| Oversize luggage info:'.$oversizedluggage.'| Payment via Stripe:'.$paymentviastripe.'| Remaining balance:'.$payremainingbalance.'| Notes:'.$additionalnote;
	$termsconditionspage   = $_POST['terms_conditions_page'];

	   $varFromLocation   = $_POST['varFromLocation'];
	   $varToLocation     = $_POST['varToLocation'];
	   $dateOutbound      = $_POST['dateOutbound'];
	   $getroutehourapi='http://schedulebull.com/api3.php?key='.$getApikey.'&q=transphere/routes';
	  
$responsehour = file_get_contents($getroutehourapi);
$GetHour=json_decode($responsehour, true);
//echo "<pre>";
//print_r($GetHour);
foreach($GetHour as $HOUR){
$target = array($varFromLocation, $varToLocation);
if(count(array_intersect($HOUR, $target)) == count($target)){
   $tillrouteHour=	$HOUR['hours'];
    }
}
$returntime = sprintf($tillrouteHour == intval($tillrouteHour) ? "%d" : "%.2f", $tillrouteHour);
//change time datatypes
$checkdatatype = sprintf($tillrouteHour == intval($tillrouteHour) ? "hours" : "minutes");	  
   $tiifinaltime='+'.$returntime.' '.$checkdatatype;

    $tilldate = date('Y-m-d H:i:s',strtotime($tiifinaltime,strtotime($dateOutbound)));
	   $returnDate        = $_POST['returnDate'];
	   $tillreturndate = date('Y-m-d H:i:s',strtotime($tiifinaltime,strtotime($returnDate)));
	   $fromPaSSenger     = $_POST['fromPaSSenger'];
	   $TOPassenger       = $_POST['TOPassenger'];
		$totalpassenger    =$TOPassenger+$fromPaSSenger;
		$book_Price        = $_POST['book_Price'];
		$reservePrice      = $_POST['reserve_Price'];
		$TOtotalPrice      = $_POST['tototal_Price'];
		$name_surname      = $Customerfirstname . ' ' . $customersurname;
if(!empty($reservePrice)){
    $Fpassanger=$fromPaSSenger;
if($Fpassanger <= 8){
	$queuntity=1;	
}elseif($Fpassanger<= 16){
 $queuntity =2;	
}elseif($Fpassanger <= 24){
	 $queuntity=3;	
}
$reservePriceMain= $reservePrice/$queuntity;
$placeORDER=array();
for($havepassanger=1; $havepassanger <= $queuntity; $havepassanger++)
{
   	// insert from query
	   $table = $wpdb->prefix.'customer_schedule_booking';
		$data = array('customer_firstname' => $Customerfirstname, 'customer_surname' => $customersurname,'customer_email' => $customeremail,'customer_phone1' => $customermobilephone,'customer_phone2' => $moible_number2,'luggage_info' => $luggageinfo,'child_seats' => $childseats,'oversized_luggage' => $oversizedluggage,'flight_accommodation' => $flightarrivaltime,'flight_Departure_time' => $flightDeparturetime,
		'hotel_name_address' => $hotelnameaddress,'payment_via_stripe' => $paymentviastripe,'additional_note' => $additionalnote,'terms_conditions' => $termsconditionspage);
		//$format = array('%s');
		$wpdb->insert($table,$data);
		$last_insert_id = $wpdb->insert_id;
		
		$table2 = $wpdb->prefix.'customer_booking_details';
		$data2 = array('schedule_booking_id' => $last_insert_id, 'from_location' => $varFromLocation,'to_location' => $varToLocation,'Outbound_date' => $dateOutbound,'return_date' => $returnDate,'from_passenger' => $fromPaSSenger,'to_passenger' => $TOPassenger,'book_price' => $reservePriceMain);
		//$format = array('%s');
		 $wpdb->insert($table2,$data2);	
		 
		 $palceorderapi='http://schedulebull.com/api3.php?place_from='.$varFromLocation.'&place_to='.$varToLocation.'&from='.$dateOutbound.'&till='.$tilldate.'&agency=Website&client='.$name_surname.'&flightNr='.$flightarrivaltime.'&comment='.$comment.'&telnr='.$customermobilephone.'&price='.$reservePriceMain.'&passengers='.$fromPaSSenger.'&q=transphere/shedule/save&key='.$getApikey.'';
		// echo $palceorderapi;
 $responsep = file_get_contents($palceorderapi);
$placeORDER[]=json_decode($responsep, true);
//print_r($placeORDER);

   $successmsg2="Thank you for your order. Your reservation has been booked. Reservation number sent in your email Id.";
   $cc_admin_email =get_option( 'admin_email_booking' );
}
	
} 
///////////////return reservation ////////////////
  if($TOtotalPrice != '0.00'){
$TOpassangers=$TOPassenger;
if($TOpassangers <= 8){
	$queuntity=1;	
}elseif($TOpassangers<= 16){
$queuntity=2;	
}elseif($TOpassangers <= 24){
	 $queuntity=3;	
}
   $TOPassengerMain= $TOtotalPrice/$queuntity;
   $placeORDER2=array();
      for($havepassanger=1; $havepassanger <= $queuntity; $havepassanger++)
{
		// insert from query
	   $table = $wpdb->prefix.'customer_schedule_booking';
		$data = array('customer_firstname' => $Customerfirstname, 'customer_surname' => $customersurname,'customer_email' => $customeremail,'customer_phone1' => $customermobilephone,'customer_phone2' => $moible_number2,'luggage_info' => $luggageinfo,'child_seats' => $childseats,'oversized_luggage' => $oversizedluggage,'flight_accommodation' => $flightarrivaltime,'flight_Departure_time' => $flightDeparturetime,
		'hotel_name_address' => $hotelnameaddress,'payment_via_stripe' => $paymentviastripe,'additional_note' => $additionalnote,'terms_conditions' => $termsconditionspage);
		//$format = array('%s');
		$wpdb->insert($table,$data);
		$last_insert_id = $wpdb->insert_id;
		
		$table2 = $wpdb->prefix.'customer_booking_details';
		$data2 = array('schedule_booking_id' => $last_insert_id, 'from_location' => $varFromLocation,'to_location' => $varToLocation,'Outbound_date' => $dateOutbound,'return_date' => $returnDate,'from_passenger' => $fromPaSSenger,'to_passenger' => $TOPassenger,'book_price' => $book_Price);
		//$format = array('%s');
		 $wpdb->insert($table2,$data2);	
		 
		 $palceorderapi1='http://schedulebull.com/api3.php?place_from='.$varToLocation.'&place_to='.$varFromLocation.'&from='.$returnDate.'&till='.$tillreturndate.'&agency=Website&client='.$name_surname.'&flightNr='.$flightDeparturetime.'&comment='.$comment.'&telnr='.$customermobilephone.'&price='.$TOPassengerMain.'&passengers='.$TOPassenger.'&q=transphere/shedule/save&key='.$getApikey.'';
		 //echo $palceorderapi1;
 $responsep = file_get_contents($palceorderapi1);
$placeORDER2[]=json_decode($responsep, true);
//print_r($placeORDER2);

$successmsg2="Thank you for your order. Your reservation has been booked. Reservation number sent in your email Id.";
}
}

$pushorderid= array_merge((array)$placeORDER,(array)$placeORDER2);
//email function.

  //admin email
  $cc_admin_email =get_option( 'admin_email_booking' );
  
  $to = $_POST['customerEmail'];
  $subject='Transfer Availability Request Order no '.implode(",",$pushorderid).'';
  $headers[] = 'From: Loyal Transfers <'.$cc_admin_email.'>';
  $headers[] = 'Cc: Loyal Transfers <'.$cc_admin_email.'>';
  $headers[]= "MIME-Version: 1.0\r\n";
  $headers[]= "Content-Type: text/html; charset=UTF-8";
$message = '<html><body>';
$message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
$message .= '<tr><th colspan="2">We have received your transfer request, we will contact you shortly to confirm our availability.
</th></tr>';
$message .= "<tr style='background: #eee;'><td><strong>Booked ID:</strong> </td><td>" . implode(",",$pushorderid) . "</td></tr>";
$message .= "<tr><td><strong>Transfer from:</strong> </td><td>" . $varFromLocation . "</td></tr>";
$message .= "<tr><td><strong>Transfer to:</strong> </td><td>" . $varToLocation . "</td></tr>";
$message .= "<tr><td><strong>Date:</strong> </td><td>" .date("d-m-Y", strtotime($dateOutbound))  . "</td></tr>";
$message .= "<tr><td><strong>Time:</strong> </td><td>" . date("G:i", strtotime($dateOutbound)) . "</td></tr>";
if(!empty($returnDate)){
$message .= "<tr><td><strong>Trip Type:</strong> </td><td>Round trip</td></tr>";
}else{
    $message .= "<tr><td><strong>Trip Type:</strong> </td><td>One Way</td></tr>"; }
$message .= "<tr><td><strong>Passangers:</strong> </td><td>" . $fromPaSSenger. "</td></tr>";    
if(!empty($returnDate)){
$message .= "<tr><td><strong>Return date:</strong> </td><td>" . date("d-m-Y", strtotime($returnDate)) . "</td></tr>";
$message .= "<tr><td><strong>Return time:</strong> </td><td>" . date("G:i", strtotime($returnDate)). "</td></tr>";
$message .= "<tr><td><strong>Return passangers:</strong> </td><td>" . $TOPassenger. "</td></tr>";
} 

$message .= "<tr><td><strong>Payment Via stripe(percent):</strong> </td><td>" . $paymentviastripe. "</td></tr>";
if($paymentviastripe != '100%'){
$message .= "<tr><td><strong>Pay remaining balance:</strong> </td><td>" . $payremainingbalance. "</td></tr>";
}
$message .= "<tr><td><strong>Total transfer price(euro):</strong> </td><td>" . str_replace(str_split('\\/:*?"<>|€,'),'',$book_Price). "</td></tr>";
$message .= "<tr><td><strong>Name:</strong> </td><td>" . $Customerfirstname. "</td></tr>";
$message .= "<tr><td><strong>Surname:</strong> </td><td>" . $customersurname. "</td></tr>";
$message .= "<tr><td><strong>Email:</strong> </td><td>" . $customeremail. "</td></tr>";
$message .= "<tr><td><strong>Mobile phone:</strong> </td><td>" . $customermobilephone. "</td></tr>";
$message .= "<tr><td><strong>2nd Mobile phone:</strong> </td><td>" . $moible_number2. "</td></tr>";
$message .= "<tr><td><strong>Luggage info:</strong> </td><td>" . $luggageinfo. "</td></tr>";
$message .= "<tr><td><strong>Do you required Infant seats?:</strong> </td><td>" . $childseats. "</td></tr>";
$message .= "<tr><td><strong>Do you required Child seats?:</strong> </td><td>" . $childseats2. "</td></tr>";
$message .= "<tr><td><strong>Do you required Booster seats?:</strong> </td><td>" . $childseats3. "</td></tr>";
$message .= "<tr><td><strong>Are you bringing any oversized luggage?:</strong> </td><td>" . $oversizedluggage. "</td></tr>";
$message .= "<tr><td><strong>Outbound flight number, Arrival time, Date:</strong> </td><td>" . $flightarrivaltime. "</td></tr>";
if(!empty($flightDeparturetime)){
$message .= "<tr><td><strong>Return flight number, Departure time, Date:</strong> </td><td>" . $flightDeparturetime. "</td></tr>";
}
$message .= "<tr><td><strong>Hotel name & address:</strong> </td><td>" . $hotelnameaddress. "</td></tr>";

$message .= "<tr><td><strong>Additional notes:</strong> </td><td>" . $additionalnote. "</td></tr>";
$message .= "</table>";
$message .= "</body></html>";
wp_mail( $to, $subject, $message, $headers );
//wp_redirect(get_permalink( get_the_ID() ).'/?successfull_booking=done');
//wp_redirect(get_permalink().'/successfull_booking');?>
<script type="text/javascript">
var successlink = "/Projects/schedulebullbooking/successfull_booking";
window.location = successlink;
</script>
<?php 
 } // order submitted loop end

?>

<section class=" mt-5 py-5 main-form-booked">
      	<div class="container">
	<?php 	if(!empty($successmsg2)){?>
	<div class="alert alert-success" role="alert">
 <?php echo $successmsg2;?>
</div>	
	<?php }?>
      		<div class="row">
			<div class="d-flex justify-content-start align-items-center">
 			<h4 class="Transfer-edit">Transfer:</h4><a type="button" href="<?php echo get_the_permalink(get_the_ID());?>" class="btn btn-primary px-4">Edit</a>
 		</div>
 	<!-- second form -->
 <form action="" method="post">
 
 <div class="contact-info-section">
 <input type="hidden" name="varFromLocation" value="<?php echo $FromLocation;?>" class="">
 <input type="hidden" name="varToLocation"   value="<?php echo $ToLocation;?>" class="">
 <input type="hidden" name="dateOutbound"    value="<?php echo $Outbound;?>" class="">
 <input type="hidden" name="returnDate"      value="<?php echo $returnDate;?>" class="">
 <input type="hidden" name="fromPaSSenger"   value="<?php echo $fromPaSSenger;?>" class="">
 <input type="hidden" name="TOPassenger" value="<?php echo $TOPassenger;?>" class="">
 <input type="hidden" name="book_Price" value="<?php echo $book_Price;?>" class="">
 <input type="hidden" name="reserve_Price" value="<?php echo $reserve_Price;?>" class="">
 <input type="hidden" name="tototal_Price" value="<?php echo $TOtotal_Price;?>" class="">
 

                        <ul class="list-style9 no-margin">
                            <li>

                                <div class="row">
                                    <div class="col-md-5 col-5">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <strong class="margin-10px-left text-orange">Origin:</strong>
                                    </div>
                                    <div class="col-md-7 col-7">
                                        <p><?php echo $FromLocation;?></p>
                                    </div>
                                </div>

                            </li>
                            <li>

                                <div class="row">
                                    <div class="col-md-5 col-5">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <strong class="margin-10px-left text-yellow">Destination:</strong>
                                    </div>
                                    <div class="col-md-7 col-7">
                                        <p><?php echo $ToLocation;?></p>
                                    </div>
                                </div>

                            </li>
                            <li>

                                <div class="row">
                                    <div class="col-md-5 col-5">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <strong class="margin-10px-left text-lightred">Pickup date & time:</strong>
                                    </div>
                                    <div class="col-md-7 col-7">
                                        <p><?php echo date('j M Y G:i' ,strtotime($Outbound)); ?></p>
                                    </div>
                                </div>

                            </li>
                            <li>

                                <div class="row">
                                    <div class="col-md-5 col-5">
                                        <i class="fa fa-users" aria-hidden="true"></i>
                                        <strong class="margin-10px-left text-green">Passengers:</strong>
                                    </div>
                                    <div class="col-md-7 col-7">
                                        <p><?php echo $fromPaSSenger;?></p>
                                    </div>
                                </div>

                            </li>
							<?php if(!empty($returnDate)){?>
							  <li>

                                <div class="row">
                                    <div class="col-md-5 col-5">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <strong class="margin-10px-left text-lightred">Return pickup date & time:</strong>
                                    </div>
                                    <div class="col-md-7 col-7">
                                        <p><?php  echo date('j M Y G:i' ,strtotime($returnDate));?></p>
                                    </div>
                                </div>

                            </li>
                            <li>

                                <div class="row">
                                    <div class="col-md-5 col-5">
                                        <i class="fa fa-users" aria-hidden="true"></i>
                                        <strong class="margin-10px-left text-green">Passengers:</strong>
                                    </div>
                                    <div class="col-md-7 col-7">
                                        <p><?php echo $TOPassenger;?></p>
                                    </div>
                                </div>

                            </li>
							<?php  } 	?>
                            <li>

                                <div class="row">
                                    <div class="col-md-5 col-5">
                                       <i class="fa-sharp fa-solid fa-money-bill-1"></i>
                                        <strong class="margin-10px-left xs-margin-four-left text-purple">Price Total:</strong>
                                    </div>
                                    <div class="col-md-7 col-7">
                                        <p><?php echo $book_Price;?></p>
                                    </div>
                                </div>

                            </li>                         
                        </ul>
                    </div>
 
 
 

 	<div class="row details-passenger">
 		<div>
 			<p class="second-label py-2">Lead passangers details:</p>
 		</div>
 		<div class="col-4 second-form-inpu">
          <div class="text-center p-0   my-3 form-user-name">
          	<input type="text" name="customerName" class="form-control rounded-0" placeholder="Name:*" required>
          	<i class="fa-regular fa-user"></i>
          	
          </div>
       </div>
       <div class="col-4 second-form-inpu">
         <div class="text-center p-0  my-3 form-user-name">
         	 <input type="text" name="customersurName" class="form-control rounded-0" placeholder="Surname:*" required>
         	 <i class="fa-solid fa-signature"></i>
         </div>
       </div>
       <div class="col-4 second-form-inpu">
         <div class="text-center p-0  my-3 form-user-name">
         	<input type="email" name="customerEmail" class="form-control rounded-0 " placeholder="Email:*" required>
         	<i class="fa-regular fa-envelope"></i>
         </div>
       </div>
       <div class="col-6 second-form-inpu"> 
       	 <div class="p-0 my-3 form-user-name">
       	 	<select name="dialing_code" class="rounded-0 form-select-1-a-second" aria-label="Default select example" required>
       	 	         <option value="">Dialing Code</option>
       	 							<option data-countryCode="IE" value="353">Ireland (+353)</option>
	<option data-countryCode="UK" value="44">UK (+44)</option>
	<option data-countryCode="US" value="1">USA/Canada (+1)</option>
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
         <i class="fa-solid fa-earth-asia"></i>
       	 </div>
       </div>
       <div class="col-6 second-form-inpu"> 
       	<div class="text-center p-0 my-3 form-user-name">
       		 <input type="number" name="mobile_phone" class="form-control rounded-0"  placeholder="Mobile phone:*" required>
       		 <i class="fa-solid fa-phone-flip"></i>
       	</div>
       </div>
<div class="col-6 second-form-inpu"> 
       	 <div class="p-0 my-3 form-user-name">
       	 	<select name="2mob_dialcode" class="rounded-0 form-select-1-a-second " aria-label="Default select example">
       	 							 <option value="">Dialing Code</option>
                                    <option data-countryCode="IE" value="353">Ireland (+353)</option>
	<option data-countryCode="UK" value="44">UK (+44)</option>
	<option data-countryCode="US" value="1">USA/Canada (+1)</option>
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
         <i class="fa-solid fa-earth-asia"></i>
       	 </div>
       </div>
       <div class="col-6 second-form-inpu"> 
	       	<div class="text-center p-0  my-3 form-user-name">
	       		 <input type="number" name="2moible_number" class="form-control rounded-0" placeholder="2nd Mobile phone:">
	       		 <i class="fa-solid fa-phone-flip"></i>
	       	</div>
       </div>
       <div class="col-12">
            <div class="text-center p-0  form-user-name">
         	<input type="text"  name="luggage_info" class="form-control rounded-0" placeholder="Luggage info:">
         	<i class="fa-solid fa-person-walking-luggage"></i>
         </div>
       </div>
 	</div>

 	<div class="row details-passenger">
       	<div class="col-12 mt-4">
 			<p class="second-label py-2">Do you required child seats?</p>
 		</div>
       	<div class="col-4 second-form-inpu">
       		<div class=" p-0  my-3">
         	<input type="text" name="child_seats" class="form-control rounded-0" placeholder="Infant seats">
         	<p class="text-dark py-2">Babies weight 0-13kg (0-1 years)</p>
         </div>
       	</div>
       	<div class="col-4 second-form-inpu">
       		<div class="p-0  my-3">
         	<input type="text" name="child_seats2" class="form-control rounded-0" placeholder="Child seats">
         	<p class="text-dark py-2">Childs weight 9-18kg (1-4 years)</p>
         </div>
       	</div>
       	<div class="col-4 second-form-inpu">
       		<div class=" p-0  my-3">
         	<input type="text" name="child_seats3" class="form-control rounded-0" placeholder="Booster seats">
         	<p class="text-dark py-2">Childs weight 18-36kg (4+ years)</p>
         </div>
       	</div>
    </div>
    <div class="row row details-passenger">
 			<p class="second-label my-3 py-2">Are you bringing any oversized luggage?</p>
 		   <div class="col-md-12">
 		   	<div>
 		   		<input type="text" name="oversized_luggage" class="form-control rounded-0" placeholder="Ski/Snowboard bags">
 		   	</div>
 		   </div>
 		   <p class="second-label my-3 py-2">Flight and accomodation details:</p>
 		   <div class="col-md-12">
 		   <div class="py-2">
 		   	<input type="text" name="flight_arrival_time" class="form-control rounded-0" placeholder="Outbound flight number, Arrival time, Date">
 		   </div>
 		   </div>
 		   <?php 
 		   if(!empty($returnDate)){?>
 		   <div class="col-md-12">
 		   	<div class="py-2">
 		   		<input type="text" name="flight_Departure_time" class="form-control rounded-0" placeholder="Return flight number, Departure time, Date">
 		   	</div>
 		   </div>
 		   <?php } ?>
 		   <div class="col-md-12">
 		   	<div class="py-2">
 		   		<input type="text" name="hotel_name_address" class="form-control rounded-0" placeholder="Hotel name & address:">
 		   	</div>
 		   </div>
    </div>
    <div class="row details-passenger">
       	<div class="col-12 mt-4">
 			<p class="second-label py-2">Payment via Stripe (Payment link will be sent to Your email):</p>
 		</div>
 			<div class="col-4 second-form-inpu">
       		<div class="p-0  my-3">
         	<input class="" type="radio" name="paymentviastripe" id="flexRadioDefault1" value="100%" checked>
                 <label class="form-check-label " for="flexRadioDefault1">
                        Pay full balance via Stripe

                  </label>
         </div>
       	</div>
       	<div class="col-4 second-form-inpu">
       		<div class=" p-0  my-3">
         	<input class="afterpayhide" type="radio" name="paymentviastripe" id="flexRadioDefault2" value="50%">
                 <label class="form-check-label " for="flexRadioDefault2">
                        Pay 50% via Stripe
                  </label>
         </div>
       	</div>
       	<div class="col-4 second-form-inpu">
       		<div class=" p-0  my-3">
         	<input class="afterpayhide" type="radio" name="paymentviastripe" id="flexRadioDefault3"  value="30%">
                 <label class="form-check-label" for="flexRadioDefault3">
                        Pay 30% via Stripe
                  </label>
         </div>
       	</div>
 	</div>
 	
 	<div class="row details-passenger hideremainpay" style="display:none">
       	<div class="col-12 mt-4">
 			<p class="second-label py-2">How would you like to pay remaining balance: (If you do not make 100% prepayment.)</p>
 		</div>
 			<div class="col-6">
       		<div class="p-0  my-3">
         	<input class="remaining_blance" type="radio" name="remaining_blance" id="flexRadioDefault4" value="We will send payment link 3 days before transfer date." checked >
                 <label class="form-check-label" for="flexRadioDefault4">
                        Online
					    <span>We will send payment link 3 days before transfer date.</span>
                  </label>
         </div>
       	</div>
       	<div class="col-6">
       		<div class=" p-0  my-3">
         	<input class="remaining_blance" type="radio" name="remaining_blance" id="flexRadioDefault5" value="Cash to driver">
                 <label class="form-check-label" for="flexRadioDefault5">
                        Cash to driver
                  </label>
         </div>
       	</div>
 	</div>
<div class="row row details-passenger">
 			<p class="second-label my-3 py-2">Additional note:</p>
 		   <div class="col-md-12">
 		   	<div>
 		   		<textarea type="text" name="additional_note" class="form-control rounded-0" placeholder="Additional note"></textarea>
 		   	</div>
 		   </div>  
 </div>

 	<div class="row details-passenger">
       	<div class="col-12 mt-4">
 			<p class="second-label py-2">Terms & Conditions:</p>
 		</div>
 			<div class="col-6 second-form-inpu">
       		    <div class=" p-0  my-3">
         	        <input class="" type="radio" name="terms_conditions_page" id="flexRadioDefault6" required>
                      <label class="form-check-label" for="flexRadioDefault6">
                        I agree with Terms & Conditions<sup>*</sup><a href="<?php echo get_the_permalink();?>terms-and-conditions/" target="_blank" class="px-2 text-danger">Please read our Terms and Conditions here.</a>
                     </label>
               </div>
       	   </div>
       	   <div class="col-6 second-form-inpu">
       		    <div class="d-flex align-items-center justify-content-between p-0  my-3">
         	     <p> Price for Private transfer:<span class="text-danger"><strong><big> <?php echo $book_Price;?></big></strong></span> 
				 </p><input type="submit" name="ordersubmit" <?php if($book_Price == '€0.00'){ echo "disabled";}?> value="Order Now" class="btn btn-danger">
               </div>
       	   </div>
 	</div>
</form>
      		</div>
      	</div>
</section>
	<?php 	
	} else {
	      
	?>
   <!-- first form  start -->
	<section class="section-bg-images">
         <div class="section-form-main">
            <div class="container-fluid p-0">
                
	<?php
	$successpageurl='';
	if(!empty($_GET['successfull_booking'])){
	$successpageurl= $_GET['successfull_booking'];
	}
	if($successpageurl == 'done'){?>
	<div class="alert alert-success" role="alert">
    Thank you for your order. Your reservation has been booked.
     </div>	
	<?php   } else{?>
                <form class="bookingFrom01" action="<?php echo get_permalink();?>?booking_form=true" method="post" autocomplete="off">
                  <div class="col d-flex justify-content-start  align-items-center form-input">
                     <button type="button" class="form-top-button-return bbactive">Return</button>
                     <button type="button" class="form-top-button-onway">One Way</button>
                  </div>
                  <div class="form-bg-color">
                     <div class="row">
                        <div class="col-md-6 trensfer-form">
                           <div class="bg-white rounded d-flex  align-items-center py-2 mt-2">
                              <div class="col d-flex  align-items-center form-input px-2 border-input">
                                 <?php if($atts['fromlocation']  && $atts['tolocation'] ){?>
                                 <input name="bfromlocation" type="text" class="border-0 px-1 py-2 w-100 fromlocation" id="fromlocations"   placeholder="Transfer from" value="<?php echo $atts['fromlocation']; ?>" required>
                                  <i class="fa-solid fa-location-dot"></i>
                                 <?php }else {?>
                                 <select name="bfromlocation" class="form-select-1-a fromlocation" aria-label="Default select example" id="fromlocations" required>
								 <option value="">Transfer from</option>
                                    <?php foreach($getfromdata as $getfromdatas) { if(!empty($getfromdatas->name)){
                                    ?>
				                      <option data-from-id="<?php echo $getfromdatas->id;?>" value="<?php echo $getfromdatas->name;?>"><?php echo $getfromdatas->name;?></option>
			                           <?php } 
			                           } ?>
                                 </select>
                                 <i class="fa-solid fa-location-dot"></i>
                                 <?php } ?>
                              </div>
                              <div class="col d-flex  align-items-center form-input px-3" id="todatabyajax">
                                  
                                    <?php if($atts['fromlocation']  && $atts['tolocation'] ){?>
                                 <input name="btlocation" type="text" class="border-0 px-1 py-2 w-100 tolocation" id="tolocation"   placeholder="Transfer to" value="<?php echo $atts['tolocation']; ?>" required>
                                  <i class="fa-solid fa-location-dot"></i>
                                 <?php }else {?>
                                 <select autofocus="autofocus" name="btlocation" class="form-select-1-a tolocation" id="tolocation" aria-label="Default select example" required>
                                    <option value="">Transfer to</option>
                                 </select>
                                 <i class="fa-solid fa-location-dot"></i>
                                 <?php } ?>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6 trensfer-form">
                           <div class="bg-white rounded d-flex  align-items-center py-2 mt-2 outbond-one-p">
                              <div class="col d-flex  align-items-center form-input px-2 border-input">
                                 <input name="outbound_date" type="text" class="border-0 px-1 py-2 w-100 outbound-form Outbound1" id="datepicker" data-date="" data-date-format="j M Y g:ia"  placeholder="Pickup Date & Time" required>
                                 <i class="fa-solid fa-calendar-days"></i>
                              </div>
                              <div class="col d-flex  align-items-center form-input px-3">
                              <select autofocus="autofocus" name="fromPassenger"  class="form-select-1-a fromsbblocation fromPassenger" id="fromPassenger" aria-label="Default select example" required>
                                    <option value="">Passengers</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                 </select>
                                 <i class="fa fa-users" aria-hidden="true"></i>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row py-3">
                        <div class="col-md-6 return-fields trensfer-form">
                           <div class="bg-white rounded d-flex  align-items-center py-2 mt-1">
                              <div class="col d-flex  align-items-center form-input px-2 border-input">
                                 <input name="returndate" type="text" class="border-0 px-1 py-2 w-100  outbound-form returnFORM" id="datepicker2" placeholder="Return pickup Date & Time" required>
                                 <i class="fa-solid fa-calendar-days"></i>
                              </div>
                              <div class="col d-flex  align-items-center form-input px-3">
                                 <select name="tOPassenger" class="form-select-1-a tosbblocation" id="Passengersto" aria-label="Default select example" required>
                                    <option value="">Passengers</option>
                                        <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                 </select>
                                 <i class="fa fa-users" aria-hidden="true"></i>
                              </div>
                           </div>
                        </div>
						
						<div class="col-md-6 trensfer-form ">
                           <div class=" d-flex  align-items-center mt-1 promo-code ">
						   
                              <div class="col-md-6 trensfer-form d-flex bg-white rounded  align-items-center form-input px-2  py-2 mt-1 w-100">
                                 <input type="text" name="bpromocode" class="border-0 px-1 py-2 w-100 outbound-form" id="promocodename" placeholder="Enter Promo Code">
                                 <i class="fa-solid fa-tag"></i>
                              </div>
                              
							   
                           </div>
                        </div>
                        <div class="col-md-12 mt-2">
                           <div class="row d-flex justify-content-center  align-items-center">
						    <div class="col-md-6 col-sm-12 mt-3 d-flex justify-content-center  align-items-center form-input">
                                <div class="col-md-6  d-flex justify-content-center  align-items-center form-input px-2 text-center">
                                 <p class="text-white  d-flex justify-content-center  align-items-center price-for-private">  <strong>Price for Private transfer: <span class="text-danger form-price bookprice">€0.00</span> </strong></p>
                              </div>
                             </div>
                              <div class="col mt-3 d-flex justify-content-center  align-items-center form-input">
                                 <input type="submit" name="bfsubmit" class="book-now-button" value="Book Now">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div id="loader" class="loder-img" style="display:none;"><img src="<?php echo plugin_dir_url( __FILE__ ) . '/img/loading.gif'; ?>"> </div>
				  </form>
               <?php } ?>
            </div>
         </div>
          </section>
	
      
<?php }
 ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script type="text/javascript">
$("#datepicker").flatpickr({
    altInput: true,
	time_24hr: true,
	disableMobile: "true",
	minDate: "today",
	enableTime: true,
    dateFormat: "Y-m-d H:i",
    altFormat: "j M Y H:i",
	onChange: function(selectedDates, dateStr, instance) {
	    //alert(dateStr);
      FLATPICKER_RITORNO.set("minDate", dateStr);
    },
    onClose: function(selectedDates, dateStr, instance){
        $("#fromPassenger").addClass("redborder");
        $(".tolocation").removeClass("redborder");

        jQuery('#Passengersto').val('');
         jQuery('#datepicker2').val('');
         jQuery('.returnFORM').val('');
        //jQuery('#tolocation option:selected').prop('disabled', true);
    var curDate1 =$('#datepicker').val();
    
    var fromlocationName= jQuery('#fromlocations').val();
    var tolocationName= jQuery('#tolocation').val();
    var frompassengeR= jQuery('#fromPassenger').val();
    if(frompassengeR){
    jQuery.ajax({
         type : "post",
         dataType : "html",
         url : "<?php echo admin_url('admin-ajax.php'); ?>",
         data : {action: "get_price_by_fromdate", from_name : fromlocationName,to_name : tolocationName,frompass : frompassengeR, fromDate : curDate1},
		 beforeSend: function() {
         $('#loader').show();
          },
         complete: function(){
         $('#loader').hide();
          },
         success:function(response) { 
        $.cookie("price_for_cal", response, { expires: 1, path: '/' });
        jQuery(".bookprice").html(response);  
		 //console.log(response);
        jQuery(".bookprice").html(response);  
         }

      });
    }
    }
});

var FLATPICKER_RITORNO=flatpickr('#datepicker2',{
    altInput: true,
	time_24hr: true,
	disableMobile: "true",
	 minDate: 'today',
	enableTime: true, 
    dateFormat: "Y-m-d H:i",
    altFormat: "j M Y H:i",
	onChange: function(selectedDates, dateStr, instance) {
      //alert('h22222222222222');
    },
	onClose: function(selectedDates, dateStr, instance){
	    $("#fromPassenger").removeClass("redborder");
	    $("#Passengersto").addClass("redborder");
	    $(".returnFORM").removeClass("redborder");
       var fromlocationName= jQuery('#fromlocations').val();
var tolocationName= jQuery('#tolocation').val();
var returnDate= jQuery('#datepicker2').val();
var topassengeR= jQuery('#Passengersto').val();
 if(topassengeR){
   jQuery.ajax({
         type : "post",
         dataType : "html",
         url : "<?php echo admin_url('admin-ajax.php'); ?>",
         data : {action: "get_price_by_todate", from_name : fromlocationName,to_name : tolocationName,return_Date : returnDate,to_passengeR : topassengeR},
		 beforeSend: function() {
         $('#loader').show();
          },
         complete: function(){
         $('#loader').hide();
          },
         success:function(response2) { 
        $.cookie("price_for_cal_final", response2, { expires: 1, path: '/' });
		 //console.log(response2);
        jQuery(".bookprice").html(response2);  
         },
		error: function(errorThrown) {
        console.log(errorThrown);
    },
      });
 }
    }
});
</script>

<script>
jQuery(document).ready( function() {
    jQuery('.tolocation').on('change', function()
{
   // alert('hiiiii');
   $(".Outbound1").addClass("redborder");
   $("#tolocation").removeClass("redborder");
    
});
jQuery('#fromlocations').on('change', function()
{
    setTimeout(function () {
    $('.outbound-form').removeAttr("readonly");
 $.cookie("price_for_cal_final", '', { expires: -1, path: '/' });
 $.cookie("price_for_cal", '', { expires: -1, path: '/' });
    }, 1000);
    jQuery('#Passengersto').val('');
    jQuery('#datepicker2').val('');
    jQuery('.returnFORM').val('');
    jQuery('.Outbound1').val('');
    jQuery('#fromPassenger').val('');
var fromid= $('option:selected', this).attr('data-from-id');
   //alert(fromid);
   jQuery.ajax({
         type : "post",
         dataType : "html",
         url : "<?php echo admin_url('admin-ajax.php'); ?>",
         data : {action: "get_to_location", from_id : fromid},
		 beforeSend: function() {
         $('#loader').show();
          },
         complete: function(){
         $('#loader').hide();
          },
         success:function(response) { 
		 console.log(response);
        jQuery("#todatabyajax").html(response); 
        jQuery(".bookprice").html('€0.00');
         }

      });
});
});

// from ajax
jQuery(document).ready( function() {
    
jQuery('.fromPassenger').on('change', function()
{ 
jQuery('#Passengersto').val('');
jQuery('#datepicker2').val('');
jQuery('.returnFORM').val('');
var fromlocationName= jQuery('#fromlocations').val();
var tolocationName= jQuery('#tolocation').val();
var fromoutbondDate= jQuery('#datepicker').val();
var frompassengeR= jQuery('#fromPassenger').val();
   jQuery.ajax({
         type : "post",
         dataType : "html",
         url : "<?php echo admin_url('admin-ajax.php'); ?>",
         data : {action: "get_booking_price", from_name : fromlocationName,to_name : tolocationName,outbounddate : fromoutbondDate,from_passengeR : frompassengeR},
		 beforeSend: function() {
         $('#loader').show();
          },
         complete: function(){
         $('#loader').hide();
          },
         success:function(response) { 
          $.cookie("price_for_cal", response, { expires: 1, path: '/' });
          $(".returnFORM").addClass("redborder");
          $("#Passengersto").removeClass("redborder");
          $("#fromPassenger").removeClass("redborder");
         jQuery(".bookprice").html(response);  
        var trimStr = $.trim(response);
        if(trimStr == '€0.00'){ $('.book-now-button').prop('disabled', true);  } else{ $('.book-now-button').prop('disabled', false); } 
         },
		error: function(errorThrown) {
        console.log(errorThrown);
    },
      });
});
});

//to ajax
jQuery(document).ready( function() {
jQuery('#Passengersto').on('change', function()
{ 
//var fromlocationName11= $('option:selected', this).attr('data-from-id');
var fromlocationName= jQuery('#fromlocations').val();
var tolocationName= jQuery('#tolocation').val();
var returnDate= jQuery('#datepicker2').val();
var topassengeR= jQuery('#Passengersto').val();
var hiddenfromprice =jQuery('#schedule_book_price').val();
   jQuery.ajax({
         type : "post",
         dataType : "html",
         url : "<?php echo admin_url('admin-ajax.php'); ?>",
         data : {action: "bookingprice_return", from_name : fromlocationName,to_name : tolocationName,return_Date : returnDate,to_passengeR : topassengeR,hidden_fromprice : hiddenfromprice},
		 beforeSend: function() {
         $('#loader').show();
          },
         complete: function(){
         $('#loader').hide();
          },
         success:function(response2) { 
        $.cookie("price_for_cal_final", response2, { expires: 1, path: '/' });
		 //console.log(response2);
        jQuery(".bookprice").html(response2);  
         },
		error: function(errorThrown) {
        console.log(errorThrown);
    },
      });
});
});

//promocode ajax 
jQuery(document).ready( function() {
   // unset cookies after page refress  
 $.cookie("price_for_cal_final", '', { expires: -1, path: '/' });
 $.cookie("price_for_cal", '', { expires: -1, path: '/' });
jQuery('#promocodename').focusout( function()
{ 

     var PromroCode= jQuery('#promocodename').val();
     //alert(PromroCode);
var finaltotalprice= jQuery('#schedule_book_price').val();
var fromlocationName=jQuery('#fromlocations').val();
 var tolocationName= jQuery('#tolocation').val();
var fromoutbondDate= jQuery('#datepicker').val();
   var TORETuRNDate= jQuery('#datepicker2').val();
  var frompassengeR= jQuery('#fromPassenger').val();
var schedulebookprice= jQuery('#schedule_book_price').val();
//if(PromroCode !=''){
  jQuery.ajax({
         type : "post",
         dataType : "html",
         url : "<?php echo admin_url('admin-ajax.php'); ?>",
         data : {action: "get_promo_code", promo_code : PromroCode,total_price : finaltotalprice,from_location : fromlocationName, to_location : tolocationName,fromout_bondDate : fromoutbondDate,from_passengeR : frompassengeR,return_dAte : TORETuRNDate },
		 beforeSend: function() {
         $('#loader').show();
          },
         complete: function(){
         $('#loader').hide();
          },
         success:function(response) { 
		 //console.log(response);
        jQuery(".bookprice").html(response);  
         },
		error: function(errorThrown) {
        console.log(errorThrown);
    },
      });
//}
});
});

$('.form-top-button-onway').on('click', function() {
     $('.returnFORM').attr('required', false); 
    $('#Passengersto').attr('required', false); 
     $('#datepicker2').attr('required', false); 
  $onewayprice=$.cookie("price_for_cal");
$('.form-price.bookprice').html($onewayprice);
//unset coupon and cookies
 $('#promocodename').val('');
$.cookie("price_for_cal_final", '', { expires: -1, path: '/' });
  $('.return-fields').hide();
  $('.form-top-button-onway').addClass('bbactive');
  $('.form-top-button-return').removeClass('bbactive');
    $('#datepicker2').val('');
    $('#Passengersto').val('');
});
$('.form-top-button-return').on('click', function() {
    jQuery('#Passengersto').val('');
    jQuery('.returnFORM').val('');
    
    $('#Passengersto').attr('required', true); 
    $('#datepicker2').attr('required', true); 
    $('.returnFORM').attr('required', true);
$onewayprice=$.cookie("price_for_cal");
$('.form-price.bookprice').html($onewayprice);
//remove coupon
   $('#promocodename').val('');
  $('.return-fields').show();
  $('.form-top-button-return').addClass('bbactive');
  $('.form-top-button-onway').removeClass('bbactive');
 
});

$(document).ready(function() {
    $(".afterpayhide").click(function() {
        $(".hideremainpay").show();
    });
      $("#flexRadioDefault1").click(function() {
        $(".hideremainpay").hide();
    });
});
</script>
<?php
return ob_get_clean();
 }
 ?>