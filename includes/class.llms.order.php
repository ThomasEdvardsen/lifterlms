<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Order class
*
* Manages Ordering process.
*
* @version 1.0
* @author codeBOX
* @project lifterLMS
*/
class LLMS_Order {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();

		}

		return self::$_instance;
	}

	public function get_order() {

	}

	public function process_order($order) {
		global $wpdb;


		if (isset($order) ) {
			$order = $order;
		}
		
		elseif ( LLMS()->session->get( 'llms_order', array() ) ) {
			$order = LLMS()->session->get( 'llms_order', array() );
		}
		
		else {
			return false;
		}
		

		$order_exists = $wpdb->get_results("SELECT user_id, product_id, order_completed 
			FROM " . $wpdb->prefix ."lifterlms_order
			WHERE user_id = " . $order->user_id . " AND product_id = " . $order->product_id);

		if ( ! $order_exists ) {

			$result = $wpdb->insert( $wpdb->prefix .'lifterlms_order', array( 
				'user_id' 			=> $order->user_id,  
				'created_date' 		=> current_time('mysql'),
				'order_completed' 	=> $order->order_completed,
				'product_id' 		=> $order->product_id, 

			) );

		}
	}

	public function update_order($order) {
		global $wpdb;


		if (isset($order) ) {
			$order = $order;
		}
		
		elseif ( LLMS()->session->get( 'llms_order', array() ) ) {
			$order = LLMS()->session->get( 'llms_order', array() );
		}
		
		else {
			return false;
		}

		$order_data = apply_filters( 'lifterlms_new_order', array(
			'post_type' 	=> 'order',
			'post_title' 	=> sprintf( __( 'Order &ndash; %s', 'lifterlms' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'lifterlms' ) ) ),
			'post_status' 	=> 'publish',
			'ping_status'	=> 'closed',
			'post_author' 	=> 1,
			'post_password'	=> uniqid( 'order_' )
		) );

		$order_post_id = wp_insert_post( $order_data, true );

		$result = $wpdb->update( $wpdb->prefix .'lifterlms_order', 
			array( 
				'completed_date' 	=> current_time('mysql'),
				'order_completed' 	=> 'yes',
				'order_post_id'		=> $order_post_id,
			),
			array( 
				'user_id' 			=> $order->user_id, 
				'product_id' 		=> $order->product_id, 
			)
		);
	
		//Assign user to the purchased course post
		//update_user_meta($order->user_id,'_llms_student', $order->product_id);

		// Add order metadata to the order post
		update_post_meta($order_post_id,'_llms_user_id', $order->user_id);
		update_post_meta($order_post_id,'_llms_payment_method', $order->payment_method);
		update_post_meta($order_post_id,'_llms_product_title', $order->product_title);
		update_post_meta($order_post_id,'_llms_order_total', $order->total);
		update_post_meta($order_post_id,'_llms_product_sku', $order->product_sku);
		update_post_meta($order_post_id,'_llms_order_currency', $order->currency);
		update_post_meta($order_post_id,'_llms_order_product_id', $order->product_id);
		update_post_meta($order_post_id,'_llms_order_date', current_time('mysql'));
		update_post_meta($order_post_id,'_llms_order_type', $order->payment_option);

		if ($order->payment_option == 'recurring') {
			update_post_meta($order_post_id,'_llms_order_recurring_price', $order->product_price);
			update_post_meta($order_post_id,'_llms_order_first_payment', $order->first_payment);
			update_post_meta($order_post_id,'_llms_order_billing_period', $order->billing_period);
			update_post_meta($order_post_id,'_llms_order_billing_freq', $order->billing_cycle);
			update_post_meta($order_post_id,'_llms_order_billing_freq', $order->billing_freq);
			update_post_meta($order_post_id,'_llms_order_billing_start_date', $order->billing_start_date);
		}
		
		
		$post_obj = get_post($order->product_id);

		//enroll user in course
		if ($post_obj->post_type == 'course') {								
			$user_metadatas = array(
				'_start_date' => 'yes',
				'_status' => 'Enrolled',
			);

			foreach ($user_metadatas as $key => $value) {
				$update_user_postmeta = $wpdb->insert( $wpdb->prefix .'lifterlms_user_postmeta', 
					array( 
						'user_id' 			=> $order->user_id,
						'post_id' 			=> $order->product_id,
						'meta_key'			=> $key,
						'meta_value'		=> $value,
						'updated_date'		=> current_time('mysql'),
					)
				);
			}
		}

		//add membership level to user
		if ($post_obj->post_type == 'llms_membership') {	
			$membership_levels = get_user_meta($order->user_id, '_llms_restricted_levels', true);
			if (! empty($membership_levels)) {
				array_push($membership_levels, $order->product_id);
			}
			else {
				$membership_levels = array();
				array_push($membership_levels, $order->product_id);
			}
			
			update_usermeta( $order->user_id, '_llms_restricted_levels', $membership_levels );
		}

	return $order_post_id;
	}


}