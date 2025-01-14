<?php
/**
 * Public Class
 * Handles shortcodes functionality of plugin * 
 * @package Blog Designer Pack
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Wpbdp_Public {

	function __construct() {

		// Ajax call to update option
		add_action( 'wp_ajax_bdp_get_more_post', array($this, 'bdp_get_more_post') );
		add_action( 'wp_ajax_nopriv_bdp_get_more_post', array($this, 'bdp_get_more_post') );
	}

	/**
	 * Get more Blog post througn ajax
	 *
	 * @since 1.0.0
	 */
	function bdp_get_more_post() {

		// Taking some defaults
		$result = array();

		if( ! empty( $_POST['shrt_param'] ) ) {

			global $post;

			// phpcs:ignore WordPress.Security.NonceVerification
			$atts = $_POST['shrt_param']; // WPCS: input var okay, CSRF ok.

			$shortcode_designs 	= bdp_post_masonry_designs();
			$msonry_effects 	= bdp_post_masonry_effects();
			$design 			= ($atts['design'] && (array_key_exists(trim($atts['design']), $shortcode_designs))) ? trim( $atts['design'] ) 	: 'design-1';
			$order 				= ( strtolower( $atts['order'] ) == 'asc' ) 	? 'ASC' 					: 'DESC';
			$orderby 			= ! empty( $atts['orderby'] )					? $atts['orderby']			: 'post_date';
			$posts_per_page		= ! empty( $atts['posts_per_page'] ) 			? $atts['posts_per_page']	: 10;
			$grid 				= ! empty( $atts['grid'] )						? $atts['grid'] 			: 2;
			$cat 				= ! empty( $atts['cat'] )						? $atts['cat']				: '';
			$paged				= ! empty( $_POST['paged'] )					? intval( $_POST['paged'] ) : 1;

			$words_limit 		= ! empty( $atts['words_limit'] ) 				? $atts['words_limit'] 		: 20;
			$media_size 		= ! empty( $atts['media_size'] )				? $atts['media_size'] 		: 'large'; //thumbnail, medium, large, full
			$showCategory 		= ( $atts['showCategory'] == 'true')			? 'true' 					: 'false';
			$showDate 			= ( $atts['showDate'] == 'true' ) 				? 'true' 					: 'false';
			$showAuthor 		= ( $atts['showAuthor'] == 'false')				? 'false'					: 'true';
			$show_comments 		= ( $atts['show_comments'] == 'false' ) 		? 'false'					: 'true';
			$showContent 		= ( $atts['showContent'] == 'true' ) 			? 'true' 					: 'false';
			$show_tags 			= ( $atts['show_tags'] == 'false' ) 			? 'false'					: 'true';
			$showreadmore 		= ( $atts['showreadmore'] == 'true' ) 			? 'true'					: 'false';

			$design_file 		= BDP_DIR . "/templates/masonry/{$design}.php";

			$args = array(
					'post_type'				=> BDP_POST_TYPE,
					'post_status'			=> array('publish'),
					'orderby'				=> $orderby,
					'order'					=> $order,
					'posts_per_page'		=> $posts_per_page,
					'paged'					=> $paged,
					'ignore_sticky_posts'	=> true,
				);

			// Category Parameter
			if( $cat != "" ) {
				$args['tax_query'] = array(
										array( 
											'taxonomy' 	=> BDP_CAT,
											'terms' 	=> $cat,
											'field' 	=> ( isset($cat[0]) && is_numeric($cat[0]) ) ? 'term_id' : 'slug',
										));
			}

			$blog_posts = new WP_Query( $args );

			ob_start();

			if ( $blog_posts->have_posts() ) {

				while ( $blog_posts->have_posts() ) : $blog_posts->the_post();

					$blog_links 			= array();
					$post_link 				= bdp_get_post_link( $post->ID );
					$bdp_author 			= get_the_author();
					$post_featured_image 	= bdp_get_post_featured_image( $post->ID, $media_size );						
					$terms 					= get_the_terms( $post->ID, BDP_CAT );
					$tags					= get_the_tag_list(' ',', ');
					$comments				= get_comments_number( $post->ID );
					$reply					= ($comments <= 1) ? esc_html__('Reply', 'blog-designer-pack') : esc_html__('Replies', 'blog-designer-pack');

					if( $terms ) {
						foreach ( $terms as $term ) {
							$term_link = get_term_link( $term );
							$blog_links[] = '<a href="' . esc_url( $term_link ) . '">'.$term->name.'</a>';
						}
					}
					$cate_name = join( " ", $blog_links );

					// Include design HTML file
					include( $design_file );

				endwhile; // End while loop
			}
			
			$data = ob_get_clean();
					
			$result['success'] 	= 1;
			$result['data'] 	= $data;
				
		} else {
			$result['success'] 	= 0;
		}

		wp_send_json($result);
	}
}

$bdp_public = new Wpbdp_Public();