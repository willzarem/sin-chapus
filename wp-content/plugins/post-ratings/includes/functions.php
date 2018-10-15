<?php
/**
 * Post Ratings pluggable template functions
 *
 * Override any of those functions by copying it to your theme or replace it via plugin
 *
 * @author	Digital Factory
 * @package	Post Ratings
 * @since	3.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Get user rating for a post.
 * 
 * @since	3.0
 * @param    int $post_id     Post ID
 * @return   array            Rating, vote count and bayesian rating
 */
if ( ! function_exists( 'post_ratings_get_rating' ) ) {

	function post_ratings_get_rating( $post_id = 0 ) {
		
		$post_id = (int) ( empty( $post_id ) ? get_the_ID() : $post_id );

		if ( empty( $post_id ) )
			return false;

		$options = Post_Ratings()->get_options();
		extract( $options );

		$rating = (float) get_post_meta( $post_id, 'rating', true );
		$votes = (int) get_post_meta( $post_id, 'votes', true );

		$bayesian_rating = 0;

		if ( $votes != 0 ) {
			$avg_num_votes = ( $num_rated_posts != 0 ) ? ( $num_votes / $num_rated_posts ) : 0;

			$identifiers = array(
				'AV' => $avg_num_votes,
				'MV' => Post_Ratings::MIN_VOTES,
				'MR' => $max_rating,
				'V'	 => $num_votes,
				'v'	 => $votes,
				'R'	 => $avg_rating,
				'r'	 => $rating,
			);

			if ( ! $bayesian_formula )
				$bayesian_formula = $user_formula;

			if ( ! $bayesian_formula )
				$bayesian_formula = 'r';

			$bayesian_formula = strtr( $bayesian_formula, $identifiers );

			// safe eval - only super admins can set their own formula
			$bayesian_rating = (float) @eval( "return ({$bayesian_formula});" );
			$bayesian_rating = 100 * ( $bayesian_rating / $max_rating );
		}

		return apply_filters( 'post_ratings_get_rating', compact( 'rating', 'votes', 'bayesian_rating', 'max_rating' ), $post_id );
	}

}

/**
 * Get top rated posts.
 * 
 * @since	3.0
 * @param	array $args
 * @return	array
 */
if ( ! function_exists( 'post_ratings_get_top_rated' ) ) {

	function post_ratings_get_top_rated( $args = array() ) {
		$args = array_merge(
			array(
			'posts_per_page'	=> 10,
			'order'				=> 'desc',
			'post_types'		=> Post_Ratings()->get_options( 'post_types' )
			), $args
		);

		$args = apply_filters( 'post_ratings_get_top_rated_args', $args );

		// force to use filters
		$args['suppress_filters'] = false;

		// force to use post rating as order
		$args['orderby'] = 'post_rating';

		// force to get all fields
		$args['fields'] = '';
		
		return apply_filters( 'post_ratings_get_top_rated', get_posts( $args ), $args );
		
		
	}

}

/**
 * Display a list of top rated posts.
 * 
 * @since	3.0
 * @param	array $post_id
 * @param	bool $display
 * @return	mixed
 */
if ( ! function_exists( 'post_ratings_top_rated' ) ) {

	function post_ratings_top_rated( $args = array(), $display = true ) {
		$defaults = array(
			'number_of_posts'		 => 5,
			'thumbnail_size'		 => 'thumbnail',
			'post_types'			 => Post_Ratings()->get_options( 'post_types' ),
			'order'					 => 'desc',
			'show_post_rating'		 => true,
			'show_post_score'		 => true,
			'show_post_votes'		 => true,
			'show_post_thumbnail'	 => false,
			'show_post_excerpt'		 => false,
			'no_posts_message'		 => __( 'No posts found', 'post-ratings' )
		);

		$args = apply_filters( 'post_ratings_top_rated_args', wp_parse_args( $args, $defaults ) );

		$args['show_post_rating'] = (bool) $args['show_post_rating'];
		$args['show_post_thumbnail'] = (bool) $args['show_post_thumbnail'];
		$args['show_post_excerpt'] = (bool) $args['show_post_excerpt'];

		$posts = post_ratings_get_top_rated(
			array(
				'posts_per_page' => (isset( $args['number_of_posts'] ) ? (int) $args['number_of_posts'] : $defaults['number_of_posts']),
				'order'			 => (isset( $args['order'] ) ? $args['order'] : $defaults['order']),
				'post_type'		 => (isset( $args['post_types'] ) ? $args['post_types'] : $defaults['post_types'])
			)
		);

		if ( ! empty( $posts ) ) {
			
			ob_start();
			
			echo apply_filters( 'post_ratings_widget_wrapper_start', '<ul class="top-rated-list">' );

			foreach ( $posts as $post ) {
				setup_postdata( $post );
				
				// extract args
				extract( post_ratings_get_rating( $post->ID ) );
				
				// load template
				Post_Ratings()->load_template( 'post-ratings-widget', compact( 'rating', 'votes', 'bayesian_rating', 'max_rating', 'args', 'post' ) );
			}

			wp_reset_postdata();

			echo apply_filters( 'post_ratings_widget_wrapper_end', '</ul>' );
			
			$html = ob_get_contents();
			ob_end_clean();
			
		} else {
			$html = '<p class="no-top-rated-found">' . $args['no_posts_message'] . '</p>';
		}

		$html = apply_filters( 'post_ratings_widget_html', $html, $args );

		if ( $display )
			echo $html;
		else
			return $html;
		
	}

}