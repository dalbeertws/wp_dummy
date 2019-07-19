<?php
/*
 * Plugin Name: Doctors
 * Version: 1.0
 * Plugin URI: https://github.com/monkeysuffrage/WordPress-Plugin-Template
 * Description: Admin Page for Doctors (under tool menu)
 * Author: P Guardiario
 * Author URI: http://pguardiar.io/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author P Guardiario
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-doctors.php' );
require_once( 'includes/class-doctors-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-doctors-admin-api.php' );
require_once( 'includes/lib/class-doctors-post-type.php' );
require_once( 'includes/lib/class-doctors-taxonomy.php' );

add_shortcode('doctor_appointment_slots1', 'doctor_appointment_slots1');

function doctor_appointment_slots1() {
	global $doctorsS3Config;
	$s3 = initS3ForDoctorAdmin();
	$key = $doctorsS3Config['key'];
	$result = $s3->getObject([
		'Bucket' => $doctorsS3Config['bucket'],
		'Key'    => $key
	]);
	// return $result;
	print_r(json_decode('' . $result['Body'], true));
}

/**
 * Returns the main instance of Doctors to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Doctors
 */
function Doctors () {
	$instance = Doctors::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Doctors_Settings::instance( $instance );
	}

	/**
	 * Menu stuff goes up here
	 */

	function doctors_admin() {
		require plugin_dir_path( __FILE__ ) . 'doctors_admin.php';
	}

	function doctors_admin_menu() {
		$wdu_tools_page = add_submenu_page('tools.php', 'Doctors Admin', 'Doctors Admin', 'administrator', 'doctors_admin.php', 'doctors_admin');
	}

	add_action( 'admin_menu', 'doctors_admin_menu' );

	/**
	 * ajax endpoints go here
	 */

	foreach(array(
		'putDoctorScheduleAjax'
	) as $action){
	add_action('wp_ajax_' . $action,  'doctors_' . $action);
	// add_action('wp_ajax_nopriv_' . $action,  'doctors_' . $action);

	}

	function doctors_putDoctorScheduleAjax() {
		header('Content-Type: application/json');
		// $schedule = json_decode($_POST['schedule'], true);
		$schedule = $_POST['schedule'];
		putDoctorSchedule($schedule);
		$c = getDoctorSchedule();
		echo "{}";
		exit;
	}

	/**
	 * S3 functions go here
	 */

	function initS3ForDoctorAdmin(){
		global $doctorsS3Config;
		// Include the SDK using the composer autoloader
		require_once 'vendor/autoload.php';

		$s3 = new Aws\S3\S3Client($doctorsS3Config);
		return $s3;
	}

	function getDoctorSchedule(){
		global $doctorsS3Config;

		$s3 = initS3ForDoctorAdmin();

		$key = $doctorsS3Config['key'];

		$result = $s3->getObject([
			'Bucket' => $doctorsS3Config['bucket'],
			'Key'    => $key
		]);
		// return $result;
		return json_decode('' . $result['Body'], true);
	}

	function putDoctorSchedule($config){
		global $doctorsS3Config;
		$s3 = initS3ForDoctorAdmin();

		$key = $doctorsS3Config['key'];

		$result = $s3->putObject([
			'Bucket' => $doctorsS3Config['bucket'],
			'Key'    => $key,
			'Body'   => json_encode($config)
		]);
	}

	// initial load

	// $config = json_decode(file_get_contents("C:\cygwin\home\User\wordpress\wp-content\plugins\doctors" . '/init.json'), true);
	// $x = putDoctorSchedule($config);

	// $y = getDoctorSchedule();


	return $instance;
}

Doctors();

$doctorsS3Config = [
	'region'  => 'us-east-1',
	'version' => 'latest',
	'credentials' => [
			'key'    => "AKIAIZ3YDT66VQHEY3RQ",
			'secret' => "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",
	],
	'http'    => [
		'verify' => false //'/path/to/my/cert.pem'
	],
	'bucket' => 'ezderm-schedule',
	'key' => 'doctorSchedule.json'
];

function doctor_enqueue_datepicker() {
  // Load the datepicker script (pre-registered in WordPress).
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_script('jquery-validate-min', plugins_url() . '/doctors/js/jquery.validate.min.js', array('jquery'), NULL, true );
  // You need styling for the datepicker. For simplicity I've linked to Google's hosted jQuery UI CSS.
  wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
  wp_enqueue_style( 'jquery-ui' );
	
  wp_enqueue_script( 'bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), NULL, true );
  wp_enqueue_style( 'bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', false, NULL, 'all' );
  
  wp_enqueue_script('plugin_js', plugins_url() . '/doctors/assets/js/frontend.js', array('jquery'), NULL, true );
  wp_enqueue_style( 'plugin_css', plugins_url() . '/doctors/assets/css/frontend.css', false, NULL, 'all' );
  // You need styling for the datepicker. For simplicity I've linked to Google's hosted jQuery UI CSS.
}

add_action( 'wp_enqueue_scripts', 'doctor_enqueue_datepicker');
// shortcode for appointment calendar
add_shortcode('doctor_appointment_calendar', 'doctor_appointment_calendar');
// shortcode for show all appointment slots
add_shortcode('doctor_appointment_slots', 'doctor_appointment_slots');
// register Doctor Widget
add_action( 'widgets_init', function(){
	register_widget( 'Doctor_Widget' );
});
// include files
require_once( 'widget/Doctor_Widget.php' );
require_once( 'shortcode/Doctor_Shortcode.php' );

//new page to display appointment slots
class MyCustomUrlParser {

  private $matched = array();
  /**
   * Run a filter to obtain some custom url settings, compare them to the current url
   * and if a match is found the custom callback is fired, the custom view is loaded
   * and request is stopped.
   * Must run on 'do_parse_request' filter hook.
   */
  public function parse( $result ) {
    if ( current_filter() !== 'do_parse_request' ) {
      return $result;
    }
    $custom_urls = (array) apply_filters( 'my_custom_urls', array() );
    if ( $this->match( $custom_urls ) && $this->run() ) {
      exit(); // stop WordPress workflow
    }
    return $result;
  }

  private function match( Array $urls = array() ) {
    if ( empty( $urls ) ) {
      return FALSE;
    }
    $current = $this->getCurrentUrl();
    $this->matched = array_key_exists( $current, $urls ) ? $urls[$current] : FALSE;
    return ! empty( $this->matched );
  }

  private function run() {
    if (
      is_array( $this->matched )
      && isset( $this->matched['callback'] )
      && is_callable( $this->matched['callback'] )
      && isset( $this->matched['view'] )
      && is_readable( $this->matched['view'] )
    ) {
      $GLOBALS['wp']->send_headers();
      $data = call_user_func( $this->matched['callback'] );
      require_once $this->matched['view'];
      return TRUE;
    }
  }

  private function getCurrentUrl() {
    $home_path = rtrim( parse_url( home_url(), PHP_URL_PATH ), '/' );
    $path = rtrim( substr( add_query_arg( array() ), strlen( $home_path ) ), '/' );
    return ( $path === '' ) ? '/' : $path;
  }
}

// first of all let's set custom url settings
add_filter( 'my_custom_urls', 'set_my_urls' );

function set_my_urls( $urls = array() ) {
  $my_urls = array(
     '/doctor/appointment-slots' => array(
       'callback' => 'select_appointment_page',
       'view'     => plugin_dir_path(__FILE__ ) . '/custompage.php'
     ),
     '/doctor/appointment_book' => array(
       'callback' => 'select_appointment_book',
       'view'     => plugin_dir_path(__FILE__ ) . '/custompage.php'
     ),
  );
  return array_merge( (array) $urls, $my_urls ); 
}

// attach MyCustomUrlParser::parse() method to 'do_parse_request' filter hook
add_filter( 'do_parse_request', array( new MyCustomUrlParser, 'parse' ) );

// include files
require_once( 'widget/Doctor_Widget.php' );
require_once( 'shortcode/Doctor_Shortcode.php' );

function select_appointment_page() {
  if(!isset($_POST['date'])){
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

	$appointments = json_decode($output);

  $doctors = get_doctor_appointments(); 

	$data = array('appointments'=>$appointments, 'dat'=>$_POST['date'], 'doctors'=>$doctors);
	return $data;
}

function get_doctor_appointments() {
  $doctorsS3Config = [
    'region'  => 'us-east-1',
    'version' => 'latest',
    'credentials' => [
        'key'    => "AKIAIZ3YDT66VQHEY3RQ",
        'secret' => "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",
    ],
    'http'    => [
      'verify' => false //'/path/to/my/cert.pem'
    ],
    'bucket' => 'ezderm-schedule',
    'key' => 'doctorSchedule.json'
  ];
  $s3 = initS3ForDoctorAdmin();
  $key = $doctorsS3Config['key'];
  $result = $s3->getObject([
    'Bucket' => $doctorsS3Config['bucket'],
    'Key'    => $key
  ]);
  // return $result;
  return json_decode('' . $result['Body'], true);

}
function select_appointment_book(){
  $status = array('returntype'=>'success','message'=>'Thank you very much for booking appointment slot on '.$_POST['app_date'].' '.$_POST['app_time'].' with Doctor: '.$_POST['doc_name'].' at Clinic: '.$_POST['clinic_name'].'.<br /><br />We will soon contact you regarding your appointment status.<br /><br />Please feel free to contact us at : <br />contact@apb.com');
  echo json_encode($status);    
  exit();
}