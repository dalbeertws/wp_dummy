<?php
function doctor_appointment_calendar() {
	/*$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://b26jaio9i2.execute-api.us-east-1.amazonaws.com/dev/GetAppointments");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'x-api-key: YOTK5MQQQA7tKoYGsmdYP51fzQj77qzb4IqTMYHn'
	));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	$output = curl_exec($ch);
	curl_close($ch);
	$arrs = json_decode($output);

	$arr = array();
	foreach ($arrs as $k=>$v) {
		foreach ($v as $val) {
			$newdate = date('"j-n-Y"', strtotime($val->start));
			if(!in_array($newdate, $arr)){
				$arr[] = $newdate;
			}
		}
	}
	*/
	$doctors = get_doctor_appointments();
	if(isset($doctors) and is_array($doctors)) {
		$days_avail = array();
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
	for ($i=0; $i < 90; $i++) {
		$this_tim = $tim + ($i * 24 * 60 * 60);
		$checkday_no = date('w', $this_tim);
		if(in_array($checkday_no, $days_avail)){
			$arr[] = date('"j-n-Y"', $this_tim);
		}
	}
	

	return '
	<div id="datepicker"></div>
	<form method="post" id="seldatform" action="'.get_site_url().'/doctor/appointment-slots">
		<input type="hidden" name="date" id="seldate" />
	</form>
	<script type="text/javascript">
	
	var highlight_dates = ['.implode(',', $arr).'];
	 
	jQuery(document).ready(function($){
	 
	 // Initialize datepicker
	 $("#datepicker").datepicker({
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
	  	if(highlight_dates.indexOf(newdate) != -1){
	  		$("#seldate").val(newdate);
	  		$("#seldatform").submit();
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
	  
	   if(highlight_dates.indexOf(newdate) != -1){
	    return [true, "highlight", tooltip_text];
	   }else{
	   	return [true];
	   }



	  }


	 });
	});
	</script>
    


	<style>
	.highlight a {
		background: #000 !important;
		color: #fff !important;
	}
	</style>
	';
}

function doctor_appointment_slots() {
	if(!isset($_GET['date'])){
		wp_redirect('/');
	}
	$ch = curl_init();
	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, "https://b26jaio9i2.execute-api.us-east-1.amazonaws.com/dev/GetAppointments");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'x-api-key: YOTK5MQQQA7tKoYGsmdYP51fzQj77qzb4IqTMYHn'
	));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	// grab URL and pass it to the browser
	$output = curl_exec($ch);
	// close cURL resource, and free up system resources
	curl_close($ch);

	$arrs = json_decode($output);

	$arr = array();
	foreach ($arrs as $k=>$v) {
		foreach ($v as $val) {
			$newdate = date('j-n-Y', strtotime($val->start));
			if($newdate == $_GET['date']){
				$arr[$val->clinic][] = $val;
			}
		}
	}

	$out = '<h2>'.$_GET['date'].'</h2><table class="table table-bordered" style="width: 100%"><tbody><tr>';
	foreach ($arr as $k=>$v) {
		$out .= '<td style="vertical-align: top"><strong>'.$k.'</strong><br /><br />';
		foreach ($v as $val) {
			$out .= date('h:ia', strtotime($val->start)).'<hr />';	
		}
		$out .= '</td>';
	}
	$out .= '</tr></tbody></table>';
	return $out;
}