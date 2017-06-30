<?php
/*
 * Plugin Name: ABC Chapel Hour
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-chapel-hour/
 * Description: Chapel Hour CPT
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/ambassador-baptist-college/abc-chapel-hour/
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Chapel Hour CPT
 */
function chapel_hour_post_type() {

    $labels = array(
        'name'                  => 'Chapel Hour Episodes',
        'singular_name'         => 'Chapel Hour Episode',
        'menu_name'             => 'Chapel Hour Episodes',
        'name_admin_bar'        => 'Chapel Hour Episode',
        'archives'              => 'Chapel Hour Archives',
        'parent_item_colon'     => 'Parent Chapel Hour Episode:',
        'all_items'             => 'All Chapel Hour Episodes',
        'add_new_item'          => 'Add New Chapel Hour Episode',
        'add_new'               => 'Add New',
        'new_item'              => 'New Chapel Hour Episode',
        'edit_item'             => 'Edit Chapel Hour Episode',
        'update_item'           => 'Update Chapel Hour Episode',
        'view_item'             => 'View Chapel Hour Episode',
        'search_items'          => 'Search Chapel Hour Episode',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into Chapel Hour Episode',
        'uploaded_to_this_item' => 'Uploaded to this Chapel Hour Episode',
        'items_list'            => 'Chapel Hour Episodes list',
        'items_list_navigation' => 'Chapel Hour Episodes list navigation',
        'filter_items_list'     => 'Filter Chapel Hour Episodes list',
    );
    $rewrite = array(
        'slug'                  => 'resources/chapel-hour-weekly-radio-broadcast/all',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => 'Chapel Hour Episode',
        'description'           => 'Chapel Hour Episodes',
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', ),
        'taxonomies'            => array(),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 30,
        'menu_icon'             => 'dashicons-clock',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'resources/chapel-hour-weekly-radio-broadcast/all',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'chapel_hour', $args );

}
add_action( 'init', 'chapel_hour_post_type', 0 );

/**
 * Modify Chapel Hour Archives title
 * @param  string  $title       WP page title
 * @param  integer [$id         = NULL] WP page ID
 * @return string  modified page title
 */
function filter_chapel_hour_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'chapel_hour' ) ) {
          $title = 'Chapel Hour Episodes';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_chapel_hour_page_title' );

/**
 * Add Chapel Hour meta to post content
 * @param  string $content HTML post content
 * @return string modified HTML post content
 */
function chapel_hour_episode_meta( $content ) {
    $media_file = get_field( 'media_file' );

    if ( 'chapel_hour' == get_post_type() && $media_file && ! is_feed() ) {
        // set attributes
        $audio_attrs  = array(
            'src'       => $media_file,
            'loop'      => 0,
            'autoplay'  => 0,
            'preload'   => 'auto',
        );
        $content = wp_audio_shortcode( $audio_attrs ) . '<p>Download file: <a class="dashicons dashicons-download" href="' . $media_file . '"><span class="screen-reader-text">Download here</span></a></p>' . $content;

        // add speaker name to entry-footer
        add_action( 'custom_footer_meta', 'chapel_hour_footer_meta' );
    }

    return $content;
}
add_filter( 'the_content', 'chapel_hour_episode_meta', 8 );
add_filter( 'the_excerpt', 'chapel_hour_episode_meta', 8 );

/**
 * Print the speaker’s name and episode date
 */
function chapel_hour_footer_meta() {
    if ( get_field( 'speaker_name' ) ) {
        echo '<span class="byline">' . get_field( 'speaker_name' ) . '</span>';
    }
    if ( get_field( 'media_duration' ) ) {
        echo '<span class="byline">Duration: ' . ltrim( get_field( 'media_duration' ), '00:' ) . '</span>';
    }
    echo '<span class="posted-on">' . get_the_date() . '</span>';
}

/**
 * Add shortcode for chapel hour archive
 * @param  array  $atts shortcode attributes
 * @return string HTML string of generated content
 */
function chapel_hour_archive_shortcode( $atts ) {
    $args = shortcode_atts(
        array(),
        $atts
    );
    $shortcode_output = '<section class="chapel-hour-episodes site-main">';

    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'chapel_hour' ),
        'posts_per_page'         => '10',
    );

    // the query
    $chapel_hour_query = new WP_Query( $args );

    // the loop
    if ( $chapel_hour_query->have_posts() ) {
        ob_start();
        while ( $chapel_hour_query->have_posts() ) {
            $chapel_hour_query->the_post();
            get_template_part( 'template-parts/content', 'single' );
        }
        $shortcode_output .= ob_get_clean();
    }

    // Restore original post data
    wp_reset_postdata();
    $shortcode_output .= '
    <p><a href="' . get_post_type_archive_link( 'chapel_hour' ) . '">All Episodes</a></p>
    </section>';

    // return content
    return $shortcode_output;
}
add_shortcode( 'chapel_hour_archive', 'chapel_hour_archive_shortcode' );

/**
 * Register Chapel Hour RSS feed
 */
function register_chapel_hour_rss() {
    add_feed( 'chapel-hour', 'generate_chapel_hour_rss' );
}
add_action( 'init', 'register_chapel_hour_rss' );

/**
 * Generate RSS feed content
 */
function generate_chapel_hour_rss() {
    require_once( 'inc/rss-chapel-hour-podcast.php' );
}

/**
 * Add ACF options page for podcast feed options
 */
function chapel_hour_options() {
    acf_add_options_sub_page( array(
        'page_title'    => 'Podcast Settings',
        'menu_title'    => 'Podcast Settings',
        'parent_slug'   => 'edit.php?post_type=chapel_hour',
    ) );
}
add_action( 'after_setup_theme', 'chapel_hour_options' );
