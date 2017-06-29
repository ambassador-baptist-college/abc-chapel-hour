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

// Register Custom Post Type
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

// Modify the page title
function filter_chapel_hour_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'chapel_hour' ) ) {
          $title = 'Chapel Hour Episodes';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_chapel_hour_page_title' );
