<?php
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

echo $output;

?>