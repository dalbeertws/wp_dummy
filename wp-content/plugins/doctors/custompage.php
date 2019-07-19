<?php
get_header();

$nowdate = date('Y-m-d', strtotime($data['dat']));
$doctor_already_booked = array();
foreach ($data['appointments'] as $k=>$v) {
	foreach ($v as $val) {
		$startdatetime = explode(' ',$val->start);
		$newdate = $startdatetime[0];
		if($nowdate == $newdate){
			$doctor_already_booked[$k][] = $startdatetime[1];
		}
	}
}

$doctors = $data['doctors'];

$days_avail = array();
if(isset($doctors) and is_array($doctors)) {
	foreach ($doctors as $k=>$v) {
		if(is_array($v['clinics'])){
			foreach ($v['clinics'] as $key=>$vals) {
				foreach ($vals as $dayno => $hours) {
					if(!in_array($dayno, $days_avail)){
						$days_avail[] = $dayno;
					}
				}
			}
		}
	}
}

$tim = time();
$arr = array();
for ($i=0; $i < 15; $i++) {
	$this_tim = $tim + ($i * 24 * 60 * 60);
	$checkday_no = date('w', $this_tim);
	if(in_array($checkday_no, $days_avail)){
		$arr[] = date('"j-n-Y"', $this_tim);
	}
}

echo '
	<form method="get" id="seldatformdoc" action="'.get_site_url().'/doctor/appointment-slots">
		<input type="hidden" name="date" id="seldatedoc" />
	</form>
	<script type="text/javascript">
	
	var highlight_datesdoc = ['.implode(',', $arr).'];
	 
	jQuery(document).ready(function($){
	 
	 // Initialize datepicker
	 $(".datepicker_doctor").datepicker({
	   firstDay: 0,
	   dayNames: ["S", "M", "T", "W", "T", "F", "S"],
	   dayNamesMin: ["S", "M", "T", "W", "T", "F", "S"],
	   showOtherMonths: true,
	   onSelect: function(dateText, inst) {
	   	var month = inst.selectedMonth+1;
	   	var year = inst.selectedYear;
	   	var day = inst.selectedDay;
	 
	   	// Change format of date
	   	var newdate = day+"-"+month+"-"+year;
	  	if(highlight_datesdoc.indexOf(newdate) != -1){
	  		$("#seldatedoc").val(newdate);
	  		$("#seldatformdoc").submit();
	  	}
       },
	   beforeShowDay: function(date){
	   var month = date.getMonth()+1;
	   var year = date.getFullYear();
	   var day = date.getDate();
	 
	   // Change format of date
	   var newdate = day+"-"+month+"-"+year;
	   
	   // Set tooltip text when mouse over date
	   var tooltip_text = "View appointment slots on " + newdate;

	   // Check date in Array
	  
	   if(highlight_datesdoc.indexOf(newdate) != -1){
	    return [true, "highlight_doc", tooltip_text];
	   }else{
	   	return [true];
	   }



	  }


	 });
	});
	</script>
	<style>
	.highlight_doc a {
		background: '.get_option('wpt_doctor_calenderhighlight').' !important;
		color: #fff !important;
	}
	</style>
	
	';
?>
<script src="https://www.google.com/recaptcha/api.js"></script>
<section id="primary" class="content-area">
	<main id="main" class="site-main appointment-page">
	    <div id="doctor_time_list">
		<div class="container-fluid">
				<?php
					
					$dc=1;
					$clinicdata = $doctors['clinicData'];
					foreach ($doctors as $k=>$v) {
						if($k == 'clinicData')
							continue;
						if($v['disabled']=='true')
							continue;
						echo '<div class="col-md-3">
						<div class="doctor-col">
						<div class="card-head">
						  <img src="'.(!empty($v['avatar']) ? $v['avatar'] : 'https://cdn0.iconfinder.com/data/icons/user-pictures/100/female-512.png').'" class="img-circle img-responsive">
						  <h4>'.$v['nickname'].'</h4>
						</div>
						<div class="card-loc">
						  <h5>Location Color Code</h5>
							<div><span class="color-one"></span><p>'.$clinicdata['53CF3389-6555-43D2-9D17-9890841F5BCA']['name'].'</p></div>
							<div><span class="color-two"></span><p>'.$clinicdata['085e44a4-0a52-45c7-bee6-28ce61a3c42a']['name'].'</p></div>
						</div>
						<div class="datepicker_doctor"></div>
						<span class="date_of_app">
							<p>'.date('l, F j, Y', strtotime($data['dat'])).'</p>
						</span>
						<div class="applist-container">';
						$t=1;
						$apptimes = '';
						$checkday_no = date('w', strtotime($data['dat']));
						$days_avail = array();
						foreach ($v['clinics'] as $key=>$vals) {
							foreach ($vals as $dayno => $hours) {
								if(!in_array($dayno, $days_avail)){
									$days_avail[] = $dayno;
								}
								if($dayno == $checkday_no){
									foreach ($hours['hours'] as $hour) {
										if(!isset($doctor_already_booked[$k]))
											$doctor_already_booked[$k] = array();
										
										if(!in_array($hour['start'], $doctor_already_booked[$k])){
											if(isset($clinicdata[$key])){
												$doctor_name = $clinicdata[$key]['name'];
											}else{
												$doctor_name = $key;
											}
											if(strtoupper($key) == strtoupper($clinicdata['085e44a4-0a52-45c7-bee6-28ce61a3c42a']['name'])){
												$key = '085e44a4-0a52-45c7-bee6-28ce61a3c42a';
											}
											if(strtoupper($key) == strtoupper($clinicdata['53CF3389-6555-43D2-9D17-9890841F5BCA']['name'])){
												$key = '53CF3389-6555-43D2-9D17-9890841F5BCA';
											}
											$gethour = explode(':', $hour['start']);
											if($gethour[0]<12){
												$formatted_time = $hour['start'].' AM';
											}elseif($gethour[0]==12){
												$formatted_time = $hour['start'].' PM';
											}else{
												$gethour[0] = $gethour[0] - 12;
												$formatted_time = ($gethour[0]<10 ? '0'.$gethour[0] : $gethour[0]).':'.$gethour[1].' PM';
											}
											$apptimes .= '<li class="clinic-'.$key.'"><input class="appointment_btn" onclick="book_appointment('.$dc.','.$t.')" type="button" name="app_time" id="booking_time_'.$dc.$t.'" value="'.$formatted_time.'" />
											<input type="hidden" id="booking_clinic_'.$dc.$t.'" value="'.$doctor_name.'" />
											</li>';	
								    		$t++;
										}
									}
								}
							}
						}

						if(empty($apptimes)){
							echo '<p>No appointment available</p>';
						}else{
							echo '
							<form id="" name="" action="'.get_permalink().'" method="post">
								<input type="hidden" id="doc_name_'.$dc.'" value="'.$v['nickname'].'"/>
								<input type="hidden" id="app_date_'.$dc.'" value="'.date('l, F j, Y', strtotime($data['dat'])).'"/>
								<ul class="appointment-time">'.$apptimes.'</ul>
							</form>';
						}
						echo '</div></div></div>';
					$dc++;
					}
				?>
		</div>
		</div>
		<div id="booking_success_msg" style="display: none;">
			<div class="container">
				<div class="row centered-form">
					<div class="col-md-8 col-md-offset-2">
						<div class="alert alert-success"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="booking_container" style="display: none;">
		<div class="container">
			<div class="row centered-form">
				<div class="col-md-8 col-md-offset-2">
					<form action="<?=get_site_url()?>/doctor/appointment_book" method="post" role="form" id="booking_form">
					<input type="hidden" name="doc_name" id="doc_name" value=""/>
					<input type="hidden" name="app_date" id="app_date" value=""/>
					<input type="hidden" name="app_time" id="app_time" value=""/>
					<input type="hidden" name="clinic_name" id="clinic_name" value=""/>
							<div class="row">
								<div class="col-sm-12 display_doctor_name"></div>
							</div>
							<div class="row">
								<div class="col-sm-12 display_clinic_name"></div>
							</div>
							<div class="row">
								<div class="col-sm-12 display_datetime"></div>
							</div>
							
							<p class="app-title">To complete your reservation please provide the following information:</p>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										<label class="app-title">First Name</label>
										<input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" required="">
									</div>
								</div>
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										<label class="app-title">Last Name</label>
										<input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										<label class="app-title">Phone Number</label>
										<input type="text" name="phone" id="phone" class="form-control" placeholder="Phone*" required="">
									</div>
								</div>
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										<label class="app-title">Email</label>
										<input type="email" name="email" id="email" class="form-control" placeholder="Email*" required="">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12 col-sm-4 col-md-5">
									<label class="app-title">DOB</label>
									<div class="col-md-3"><div class="row"><div class="form-group">
											<input type="text" name="dob_mm" class="form-control" placeholder="MM">
										</div></div>
										
									</div>
									<div class="col-md-4"><div class="">
										<div class="form-group">
											<input type="text" name="dob_dd" class="form-control" placeholder="DD">
										</div>
									</div></div>
									<div class="col-md-5"><div class="row">
										<div class="form-group">
											<input type="text" name="dob_yy" class="form-control" placeholder="YYYY" />
										</div>
									</div></div>
									<div id="doberror"></div>
								</div>
								<div class="col-xs-12 col-sm-4 col-md-3">
									<div class="form-group">
										<label class="app-title">Gender</label>
										<div class="radio-block" id="gendererror">
										<label class="radio-inline">
										  <input type="radio" name="gender" id="Male" value="Male"><small> Male</small>
										</label>
										<label class="radio-inline">
										  <input type="radio" name="gender" id="Female" value="Female"><small> Female</small></label>
									</div></div>
								</div>
								<div class="col-xs-12 col-sm-4 col-md-4">
									<div class="form-group">
										<label class="app-title">Insurance</label>
										<select name="insurance" class="form-control">
											<option value="">Select Insurance</option>
										  <option value="Yes">Yes</option>
										  <option value="No">No</option>
										</select>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-8">
									<!--<span class="inline-title">Answer <span id="captchaOperation"></span></span>
									<input type="number" name="avoid_spam" id="avoid_spam" class="form-control">-->
									<div class="g-recaptcha" data-sitekey="6Lf34KgUAAAAAGsgN9n9hiIAOD12NENJvseRkbA6" data-callback="recaptchaCallback"></div>
									<input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">
								</div>
							</div>
							<p class="app-title"><strong>Mandatory:</strong> The reasons for the visit in detail:</p>
							<div class="row">
							
								<div class="col-md-12">
									<div class="form-group">
										<textarea rows="5" name="reasons_for_visit" required=""></textarea>
									</div>
								</div>
								
							</div>
							<input type="submit" value="Submit" class="btn btn-info btn-custom" name="submit"> &nbsp; 
							<input type="button" class="btn btn-default btn-cancel" onclick="cancel_appointment()" value="Cancel">
					</form>
				</div>
			</div>
		</div>
		</div>
	</main><!-- #main -->
</section><!-- #primary -->
<?php
get_footer();
