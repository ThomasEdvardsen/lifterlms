<?php
/**
 * Admin GradeBook Tables
 *
 * @since   3.2.0
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Table_Questions extends LLMS_Admin_Table {

	/**
	 * Unique ID for the Table
	 * @var  string
	 */
	protected $id = 'questions';

	/**
	 * Retrieve data for the columns
	 * @param    string     $key   the column id / key
	 * @param    mixed      $data  object of achievment data
	 * @return   mixed
	 * @since    3.2.0
	 * @version  3.2.0
	 */
	public function get_data( $key, $data ) {

		switch ( $key ) {

			case 'correct':
				$q = new LLMS_Question( $data['id'] );
				$correct = $q->get_correct_option();
				$value = $correct['option_text'];
			break;

			case 'id':
				$value = $this->get_post_link( $data['id'] );
			break;

			case 'name':
				$value = get_post_meta( $data->achievement_id, '_llms_achievement_title', true );
			break;

			case 'points':
				if ( $data['correct'] ) {
					$value = $data['points'];
				} else {
					$value = '0 <del>' . $data['points'] . '</del>';
				}
			break;

			case 'question':
				$q = new LLMS_Question( $data['id'] );
				$value = apply_filters( 'the_content', $q->post->post_content );
			break;

			case 'selected':
				$q = new LLMS_Question( $data['id'] );
				$options = $q->get_options();
				$value = wp_kses_post( $options[ $data['answer'] ]['option_text'] );
			break;

			default:
				$value = $key;

		}

		return $this->filter_get_data( $value, $key, $data );

	}

	public function set_args() {
		return;
	}

	/**
	 * Define the structure of the table
	 * @return   array
	 * @since    3.2.0
	 * @version  3.2.0
	 */
	protected function set_columns() {
		return array(
			'id' => __( 'ID', 'lifterlms' ),
			'points' => __( 'Points', 'lifterlms' ),
			'question' => __( 'Question', 'lifterlms' ),
			'selected' => __( 'Selected Answer', 'lifterlms' ),
			'correct' => __( 'Correct Answer', 'lifterlms' ),
		);
	}

	public function get_results( $args = array() ) {

		$this->tbody_data = $args['questions'];

	}

}
