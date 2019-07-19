function book_appointment(dc,t) {
	var doc_name = jQuery("#doc_name_" + dc).val();
	var app_date = jQuery("#app_date_" + dc).val();
	var app_time = jQuery("#booking_time_" + dc + t).val();
	var clinic_name = jQuery("#booking_clinic_" + dc + t).val();
	jQuery("#doctor_time_list").hide();
	jQuery("#booking_container").show();
	jQuery("#doc_name").val(doc_name);
	jQuery("#app_date").val(app_date);
	jQuery("#app_time").val(app_time);	
	jQuery("#clinic_name").val(clinic_name);

	jQuery(".display_doctor_name").html('<strong>Doctor:</strong> '+doc_name);
	jQuery(".display_datetime").html('<strong>Date & Time:</strong> '+app_date+' '+app_time);
	jQuery(".display_clinic_name").html('<strong>Clinic:</strong> '+clinic_name);

	jQuery('html, body').animate({
        scrollTop: jQuery("#booking_container").offset().top
    }, 0);
}

function randomNumber(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
};

function recaptchaCallback() {
  jQuery('#hiddenRecaptcha').valid();
}

jQuery( document ).ready(function($) {

	jQuery.validator.addMethod("validateans", function(value, element) {
		var items = $('#captchaOperation').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
        return value == sum;
	}, "Please enter correct answer");


	$('#captchaOperation').html([randomNumber(1, 100), '+', randomNumber(1, 200), '='].join(' '));

	$('#dob').datepicker({
		dateFormat: "dd-mm-yy",
		changeMonth: true,
		changeYear: true,
	});


	$("#booking_form").validate({
		ignore: ":hidden:not(#hiddenRecaptcha)",
		rules: {
			first_name: {
				required:true,
			},
			last_name: {
				required:true,
			},
			phone: {
				required:true,
				number: true,
			},
			email: {
			    required:true,
				email: true,
			},
			dob: {
				required:true,
			},
			gender: {
				required:true,
			},
			insurance: {
				required:true,
			},
			reasons_for_visit: {
				required:true,
			},
			/*avoid_spam: {
				required: true,
				validateans: true
			},*/
			"hiddenRecaptcha": {
				required: function() {
					if(grecaptcha.getResponse() == '') {
						return true;
					} else { return false; }
				}
			},
		},
		messages: {
			first_name: {
				required:"Please enter first name",
			},
			last_name: {
				required:"Please enter last name",
			},
			phone: {
				required:"Please enter phone number",
				number: "Enter only number",
			},
			email: {
			    required:"Please enter email address",
				email: "Invalid email address",
			},
			dob: {
				required:"Please select dob",
			},
			gender: {
				required:"Please select gender",
			},
			insurance: {
				required:"Please select insurance",
			},
			reasons_for_visit: {
				required:"Please enter reason for visit",
			},
			/*avoid_spam: {
				required: "Please enter correct answer",
				validateans: "Please enter correct answer",
			},*/
			"hiddenRecaptcha": {
				required: "Captcha is required",
			},
		},
		submitHandler: function(form) {
	  		var form_values = $("#booking_form").serialize();
			$.ajax({
				url: '<?=get_site_url()?>/doctor/appointment_book',
				type: "POST",
				data: form_values,
				dataType: 'json',
				success: function(res){
					
			        if(res.returntype == 'success'){
			           $('#booking_container').hide();
			           $('#booking_success_msg .alert-success').html(res.message);
			           $('#booking_success_msg').show();
			           jQuery('html, body').animate({
					        scrollTop: jQuery("#booking_success_msg").offset().top
					    }, 0);
			        }else{
			           alert(res.error);
			        }
					
				}
			})
		}
	});
});