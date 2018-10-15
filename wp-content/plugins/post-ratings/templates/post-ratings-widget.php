<?php
/**
 * @template	Post Ratings Widget
 * @since		3.0
 * @author		Digital Factory, http://www.dfactory.eu/
 * @license		MIT License, http://opensource.org/licenses/MIT
 */

/*
  This is the HTML template for the ratings widget content.
  You can override it by creating your own template with the same name, inside your theme / child theme folder.

  Available variables inside this template:
  $rating           - real, rating of the current post
  $votes            - integer, number of votes the post has
  $bayesian_rating  - real, weighted rating (score)
  $max_rating       - integer, maximum possible rating
  $args				- widget arguments
  $post				- widget loop post object
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // exit if accessed directly

// local variable, we will make this the title of the html block
$current_rating = apply_filters( 'post_ratings_current_rating', sprintf( '%.2F / %d', $rating, $max_rating ), $rating, $max_rating );
// extra classes
$classes = apply_filters( 'post_ratings_widget_classes', array() );
?>

<li id="post-<?php echo $post->ID; ?>" <?php post_class( $classes, $post->ID ); ?>>

	<?php if ( $args['show_post_thumbnail'] && has_post_thumbnail( $post->ID ) ) : ?>
		<span class="post-thumbnail"><?php echo get_the_post_thumbnail( $post->ID, $args['thumbnail_size'] ); ?></span>
	<?php endif; ?>
		
	<a class="post-title" href="<?php echo get_permalink( $post->ID ); ?>" title="<?php the_title_attribute( array( 'post' => $post->ID ) ); ?>"><?php echo get_the_title( $post->ID ); ?></a>
	
	<?php if ( $args['show_post_date'] ) : ?>
		<span class="post-date"><?php echo get_the_date( '', $post->ID ); ?></span>
	<?php endif; ?>
	
	<?php if ( $args['show_post_rating'] )  : ?>
		<span class="post-rating"><?php echo number_format_i18n( $rating, 2 ); ?></span>
	<?php endif; ?>
		
	<?php if ( $args['show_post_max_rating'] )  : ?>
		<span class="post-max_rating">/ <?php echo number_format_i18n( $max_rating ); ?></span>
	<?php endif; ?>
	
	<?php if ( $args['show_post_score'] )  : ?>
		<span class="post-score">(<?php echo number_format_i18n( $bayesian_rating, 0 ); ?>%)</span>
	<?php endif; ?>
		
	<?php if ( $args['show_post_votes'] )  : ?>
		<span class="post-votes">- <?php printf( _n( '%1$s vote', '%1$s votes', $votes, 'post-ratings' ), $votes ); ?></span>
	<?php endif; ?>
		
	<?php if ( $args['show_post_excerpt'] )  : ?>
		<div class="post-excerpt"><?php the_excerpt(); ?></div>		
	<?php endif; ?>

</li>