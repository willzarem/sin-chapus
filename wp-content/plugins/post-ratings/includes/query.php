<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Post_Ratings_Query class.
 * 
 * @since 3.0
 */
class Post_Ratings_Query {

	public function __construct() {
		// actions
		add_action( 'pre_get_posts', array( $this, 'extend_pre_query' ), 1 );

		// filters
		add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );
		add_filter( 'posts_fields', array( $this, 'posts_fields' ), 10, 2 );
	}

	/**
	 * Extend query with post_rating orderby parameter.
	 * 
	 * @param	object	$query
	 */
	public function extend_pre_query( $query ) {
		if ( isset( $query->query_vars['orderby'] ) && $query->query_vars['orderby'] === 'post_rating' )
			$query->post_ratings = true;
	}

	/**
	 * Modify the db query to use post_ratings parameter.
	 * 
	 * @global	object	$wpdb
	 * @param	string	$join
	 * @param	object	$query
	 * @return	string
	 */
	public function posts_join( $join, $query ) {
		// is it sorted by post views?
		if ( ( isset( $query->post_ratings ) && $query->post_ratings ) || apply_filters( 'post_ratings_extend_post_object', false, $query ) === true ) {
			global $wpdb;
			
			$join .= "
				LEFT JOIN(
					SELECT DISTINCT post_id,
					(SELECT CAST(meta_value AS DECIMAL(10)) FROM {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = meta.post_id AND meta_key ='votes') AS votes,
					(SELECT CAST(meta_value AS DECIMAL(10,2)) FROM {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = meta.post_id AND meta_key ='rating') AS rating
				FROM {$wpdb->postmeta} meta )
				AS ratingmeta ON {$wpdb->posts}.ID = ratingmeta.post_id";
		}
		return $join;
	}

	/**
	 * Order posts by post rating.
	 * 
	 * @global	object	$wpdb
	 * @param	string	$orderby
	 * @param	object	$query
	 * @return	string
	 */
	public function posts_orderby( $orderby, $query ) {
		// is it sorted by post views?
		if ( ( isset( $query->post_ratings ) && $query->post_ratings ) ) {
			global $wpdb;

			$order = $query->get( 'order' );
			$orderby = 'post_rating ' . $order . ', ' . $wpdb->prefix . 'posts.ID ' . $order;
		}

		return $orderby;
	}

	/**
	 * Return post rating in queried post objects.
	 * 
	 * @param	string	$fields
	 * @param	object	$query
	 * @return	string
	 */
	public function posts_fields( $fields, $query ) {

		if ( ( ! isset( $query->query['fields'] ) || $query->query['fields'] === '' ) && ( ( isset( $query->post_ratings ) && $query->post_ratings ) ) ) {
			
			$options = Post_Ratings()->get_options();
			extract( $options );
			
			// averge votes per post
			$avg_num_votes = ($num_rated_posts != 0) ? ($num_votes / $num_rated_posts) : 0;

			if ( empty( $bayesian_formula ) )
				$bayesian_formula = $user_formula;

			if ( ! $bayesian_formula )
				$bayesian_formula = 'r';

			$identifiers = array(
				'AV' => $avg_num_votes,
				'MV' => Post_Ratings::MIN_VOTES,
				'MR' => $max_rating,
				'V'	 => $num_votes,
				'v'	 => 'votes',
				'R'	 => $avg_rating,
				'r'	 => 'rating',
			);

			$bayesian_formula = strtr( $bayesian_formula, $identifiers );
			
			$fields = $fields . ", {$bayesian_formula} AS post_rating";
			
		}

		return $fields;
	}

}

new Post_Ratings_Query();