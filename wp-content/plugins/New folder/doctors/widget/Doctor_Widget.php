<?php
class Doctor_Widget extends WP_Widget {
	// class constructor
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'doctor_widget',
			'description' => 'A plugin for Doctors frontend',
		);
		parent::__construct( 'doctor_widget', 'Doctor Appointment', $widget_ops );
	}
	
	// output the widget content on the front-end
	public function widget( $args, $instance ) {
		echo do_shortcode( '[doctor_appointment_calendar]' );
	}

	// output the option form field in admin Widgets screen
	public function form( $instance ) {}

	// save options
	public function update( $new_instance, $old_instance ) {}
}