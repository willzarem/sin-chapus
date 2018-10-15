<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Post_Ratings_Widget class.
 * 
 * @since	3.0
 */
class Post_Ratings_Widget extends WP_Widget {

	private $post_ratings_options;
	private $post_ratings_defaults;
	private $post_ratings_order;
	private $post_ratings_orderby;
	private $post_ratings_post_types;
	private $post_ratings_image_sizes;

	public function __construct() {
		
		parent::__construct(
			'Post_Ratings_Widget', __( 'Top Rated Posts', 'post-ratings' ), 
			array(
				'description'	=> __( 'Displays a list of the top rated posts.', 'post-ratings' ),
				'classname'		=> 'widget_post_ratings'
			)
		);
		
		$this->post_ratings_post_types = Post_Ratings()->get_options( 'post_types' ) != false ? Post_Ratings()->get_options( 'post_types' ) : array();

		$this->post_ratings_defaults = array(
			'title'					 => __( 'Top Rated Posts', 'post-ratings' ),
			'number_of_posts'		 => 5,
			'thumbnail_size'		 => 'thumbnail',
			'post_types'			 => $this->post_ratings_post_types,
			'order'					 => 'desc',
			'show_post_rating'		 => true,
			'show_post_max_rating'	 => true,
			'show_post_score'		 => true,
			'show_post_votes'		 => true,
			'show_post_date'		 => false,
			'show_post_thumbnail'	 => false,
			'show_post_excerpt'		 => false,
			'no_posts_message'		 => __( 'No posts found', 'post-ratings' )
		);

		$this->post_ratings_order = array(
			'asc'	 => __( 'Ascending', 'post-ratings' ),
			'desc'	 => __( 'Descending', 'post-ratings' )
		);

		$this->post_ratings_image_sizes = array_merge( array( 'full' ), get_intermediate_image_sizes() );

		// sort image sizes by name, ascending
		sort( $this->post_ratings_image_sizes, SORT_STRING );
	}

	/**
	 * Display widget function.
	 * 
	 * @since	3.0
	 * @param	array	$args
	 * @param	array	$instance
	 * @return	mixed	$html
	 */
	public function widget( $args, $instance ) {
		$instance['title'] = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$html = $args['before_widget'] . ( ! empty( $instance['title'] ) ? $args['before_title'] . $instance['title'] . $args['after_title'] : '');
		$html .= post_ratings_top_rated( $instance, false );
		$html .= $args['after_widget'];

		echo $html;
	}

	/**
	 * Admin widget function.
	 * 
	 * @since	3.0
	 * @param	array	$instance
	 * @return	mixed
	 */
	public function form( $instance ) {
		$html = '
		<p>
			<label for="' . $this->get_field_id( 'title' ) . '">' . __( 'Title', 'post-ratings' ) . ':</label>
			<input id="' . $this->get_field_id( 'title' ) . '" class="widefat" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( isset( $instance['title'] ) ? $instance['title'] : $this->post_ratings_defaults['title'] ) . '" />
		</p>
		<p>
			<label>' . __( 'Post Types', 'post-ratings' ) . ':</label><br />';

		foreach ( $this->post_ratings_post_types as $post_type  ) {
			$post_type_obj = get_post_type_object( $post_type );
			
			$html .= '
				<input id="' . $this->get_field_id( 'post_types' ) . '-' . $post_type . '" type="checkbox" name="' . $this->get_field_name( 'post_types' ) . '[]" value="' . $post_type . '" ' . checked( ( ! isset( $instance['post_types'] ) ? true : in_array( $post_type, $instance['post_types'], true ) ), true, false ) . '><label for="' . $this->get_field_id( 'post_types' ) . '-' . $post_type . '">' . esc_html( $post_type_obj->labels->singular_name ) . '</label>';
		}

		$show_post_thumbnail = isset( $instance['show_post_thumbnail'] ) ? $instance['show_post_thumbnail'] : $this->post_ratings_defaults['show_post_thumbnail'];

		$html .= '
			</select>
		</p>
		<p>
			<label for="' . $this->get_field_id( 'number_of_posts' ) . '">' . __( 'Number of posts to show', 'post-ratings' ) . ':</label>
			<input id="' . $this->get_field_id( 'number_of_posts' ) . '" name="' . $this->get_field_name( 'number_of_posts' ) . '" type="number" size="1" value="' . esc_attr( isset( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : $this->post_ratings_defaults['number_of_posts'] ) . '" />
		</p>
		<p>
			<label for="' . $this->get_field_id( 'no_posts_message' ) . '">' . __( 'No posts message', 'post-ratings' ) . ':</label>
			<input id="' . $this->get_field_id( 'no_posts_message' ) . '" class="widefat" type="text" name="' . $this->get_field_name( 'no_posts_message' ) . '" value="' . esc_attr( isset( $instance['no_posts_message'] ) ? $instance['no_posts_message'] : $this->post_ratings_defaults['no_posts_message'] ) . '" />
		</p>
		<p>
			<label for="' . $this->get_field_id( 'order' ) . '">' . __( 'Order', 'post-ratings' ) . ':</label>
			<select id="' . $this->get_field_id( 'order' ) . '" name="' . $this->get_field_name( 'order' ) . '">';

		foreach ( $this->post_ratings_order as $id => $order ) {
			$html .= '
				<option value="' . esc_attr( $id ) . '" ' . selected( $id, ( isset( $instance['order'] ) ? $instance['order'] : $this->post_ratings_defaults['order'] ), false ) . '>' . $order . '</option>';
		}

		$html .= '
			</select>
		</p>
		<p>
			<input id="' . $this->get_field_id( 'show_post_rating' ) . '" type="checkbox" name="' . $this->get_field_name( 'show_post_rating' ) . '" ' . checked( true, (isset( $instance['show_post_rating'] ) ? $instance['show_post_rating'] : $this->post_ratings_defaults['show_post_rating'] ), false ) . ' /> <label for="' . $this->get_field_id( 'show_post_rating' ) . '">' . __( 'Display post rating?', 'post-ratings' ) . '</label>
			<br />
			<input id="' . $this->get_field_id( 'show_post_max_rating' ) . '" type="checkbox" name="' . $this->get_field_name( 'show_post_max_rating' ) . '" ' . checked( true, (isset( $instance['show_post_max_rating'] ) ? $instance['show_post_max_rating'] : $this->post_ratings_defaults['show_post_max_rating'] ), false ) . ' /> <label for="' . $this->get_field_id( 'show_post_max_rating' ) . '">' . __( 'Display max rating?', 'post-ratings' ) . '</label>
			<br />
			<input id="' . $this->get_field_id( 'show_post_score' ) . '" type="checkbox" name="' . $this->get_field_name( 'show_post_score' ) . '" ' . checked( true, (isset( $instance['show_post_score'] ) ? $instance['show_post_score'] : $this->post_ratings_defaults['show_post_score'] ), false ) . ' /> <label for="' . $this->get_field_id( 'show_post_score' ) . '">' . __( 'Display post score?', 'post-ratings' ) . '</label>
			<br />
			<input id="' . $this->get_field_id( 'show_post_votes' ) . '" type="checkbox" name="' . $this->get_field_name( 'show_post_votes' ) . '" ' . checked( true, (isset( $instance['show_post_votes'] ) ? $instance['show_post_votes'] : $this->post_ratings_defaults['show_post_votes'] ), false ) . ' /> <label for="' . $this->get_field_id( 'show_post_votes' ) . '">' . __( 'Display post votes?', 'post-ratings' ) . '</label>
			<br />
			<input id="' . $this->get_field_id( 'show_post_date' ) . '" type="checkbox" name="' . $this->get_field_name( 'show_post_date' ) . '" ' . checked( true, (isset( $instance['show_post_date'] ) ? $instance['show_post_date'] : $this->post_ratings_defaults['show_post_date'] ), false ) . ' /> <label for="' . $this->get_field_id( 'show_post_date' ) . '">' . __( 'Display post date?', 'post-ratings' ) . '</label>
			<br />
			<input id="' . $this->get_field_id( 'show_post_excerpt' ) . '" type="checkbox" name="' . $this->get_field_name( 'show_post_excerpt' ) . '" ' . checked( true, (isset( $instance['show_post_excerpt'] ) ? $instance['show_post_excerpt'] : $this->post_ratings_defaults['show_post_excerpt'] ), false ) . ' /> <label for="' . $this->get_field_id( 'show_post_excerpt' ) . '">' . __( 'Display post excerpt?', 'post-ratings' ) . '</label>
			<br />
			<input id="' . $this->get_field_id( 'show_post_thumbnail' ) . '" class="em-show-event-thumbnail" type="checkbox" name="' . $this->get_field_name( 'show_post_thumbnail' ) . '" ' . checked( true, $show_post_thumbnail, false ) . ' /> <label for="' . $this->get_field_id( 'show_post_thumbnail' ) . '">' . __( 'Display post thumbnail?', 'post-ratings' ) . '</label>
		</p>
		<p>
			<label for="' . $this->get_field_id( 'thumbnail_size' ) . '">' . __( 'Thumbnail size', 'post-ratings' ) . ':</label>
			<select id="' . $this->get_field_id( 'thumbnail_size' ) . '" name="' . $this->get_field_name( 'thumbnail_size' ) . '">';

		$size_type = isset( $instance['thumbnail_size'] ) ? $instance['thumbnail_size'] : $this->post_ratings_defaults['thumbnail_size'];

		foreach ( $this->post_ratings_image_sizes as $size ) {
			$html .= '
				<option value="' . esc_attr( $size ) . '" ' . selected( $size, $size_type, false ) . '>' . $size . '</option>';
		}

		$html .= '
			</select>
		</p>';

		echo $html;
	}

	/**
	 * Save widget function.
	 * 
	 * @since	3.0
	 * @param	array	$new_instance
	 * @param	array	$old_instance
	 */
	public function update( $new_instance, $old_instance ) {
		// number of posts
		$old_instance['number_of_posts'] = (int) (isset( $new_instance['number_of_posts'] ) ? $new_instance['number_of_posts'] : $this->post_ratings_defaults['number_of_posts']);

		// order
		$old_instance['order'] = isset( $new_instance['order'] ) && in_array( $new_instance['order'], array_keys( $this->post_ratings_order ), true ) ? $new_instance['order'] : $this->post_ratings_defaults['order'];

		// thumbnail size
		$old_instance['thumbnail_size'] = isset( $new_instance['thumbnail_size'] ) && in_array( $new_instance['thumbnail_size'], $this->post_ratings_image_sizes, true ) ? $new_instance['thumbnail_size'] : $this->post_ratings_defaults['thumbnail_size'];

		// booleans
		$old_instance['show_post_rating'] = isset( $new_instance['show_post_rating'] );
		$old_instance['show_post_max_rating'] = isset( $new_instance['show_post_max_rating'] );
		$old_instance['show_post_score'] = isset( $new_instance['show_post_score'] );
		$old_instance['show_post_votes'] = isset( $new_instance['show_post_votes'] );
		$old_instance['show_post_date'] = isset( $new_instance['show_post_date'] );
		$old_instance['show_post_thumbnail'] = isset( $new_instance['show_post_thumbnail'] );
		$old_instance['show_post_excerpt'] = isset( $new_instance['show_post_excerpt'] );
		
		// texts
		$old_instance['title'] = sanitize_text_field( isset( $new_instance['title'] ) ? $new_instance['title'] : $this->post_ratings_defaults['title'] );
		$old_instance['no_posts_message'] = sanitize_text_field( isset( $new_instance['no_posts_message'] ) ? $new_instance['no_posts_message'] : $this->post_ratings_defaults['no_posts_message'] );

		// post types
		if ( isset( $new_instance['post_types'] ) ) {
			$post_types = array();

			foreach ( $new_instance['post_types'] as $post_type ) {

					$post_types[] = $post_type;
			}

			$old_instance['post_types'] = array_unique( $post_types );
		} else
			$old_instance['post_types'] = array( 'post' );

		return $old_instance;
	}

}
