<?php
/*
Plugin Name: Page to page redirect
Plugin URI: https://github.com/klevchenko
Description: This plugin allows you to configure page to page redirects.
Author: Levchenko Konstiantyn
Author URI: https://github.com/klevchenko
Version: 1.0
*/

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

add_action('admin_menu', 'page_to_page_redirect_plugin_add_top_menu');

function page_to_page_redirect_plugin_add_top_menu()
{
    add_menu_page(
        'Page to page redirect',
        'Page to page redirect',
        'manage_options',
        'page-to-page-redirect/admin-page.php'
    );
}

add_action('admin_enqueue_scripts', 'page_to_page_redirect_plugin_js');

function page_to_page_redirect_plugin_js()
{
    // include select2
    wp_enqueue_script('page-to-page-redirect-select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.full.min.js', array('jquery'));
    wp_enqueue_style('page-to-page-redirect-select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css');

    wp_enqueue_style('page-to-page-redirect-css', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('page-to-page-redirect-js', plugin_dir_url(__FILE__) . 'script.js', array('jquery'));

}

add_action('wp_ajax_action_add_update_redirect', 'function_add_update_redirect');

function function_add_update_redirect()
{
    $option_name = 'all_redirects_data';

    $from_id = isset($_POST['from_id']) && !empty($_POST['from_id']) ? $_POST['from_id'] : false;
    $to_id = isset($_POST['to_id']) && !empty($_POST['to_id']) ? $_POST['to_id'] : false;

    $from_id = intval($from_id);
    $to_id = intval($to_id);

    $all_redirects_data = get_redirects_array();

    $all_redirects_data[$from_id] = $to_id;

    update_option($option_name, serialize($all_redirects_data));

    wp_send_json($all_redirects_data, 200);
}

add_action('wp_ajax_action_get_redirects_data', 'function_get_redirects_data');

function function_get_redirects_data()
{
    wp_send_json(get_redirects_array(), 200);
}

add_action('wp_ajax_action_search_posts', 'function_search_posts');

function function_search_posts()
{
    $res_arr = [];

    $search_keyword = $_POST['search_keyword'];
    $search_keyword = esc_attr($search_keyword);

    $the_query = new WP_Query(
        array(
            'posts_per_page' => -1,
            'post_status' => 'publish',
            's' => $search_keyword
        )
    );

    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();

            $res_arr[get_post_type()][] = [
                "id" => get_the_ID(),
                "title" => get_the_title(),
                "type" => get_post_type(),
            ];
        }
        wp_reset_postdata();

        wp_send_json($res_arr, 200);

    } else {
        wp_send_json(0, 200);
    }

}

add_action('template_redirect', 'redirect_user_if_exist');

function redirect_user_if_exist()
{
    global $post;

    $cur_post_ID = $post->ID;
    $all_redirects_data = get_redirects_array();

    if ($all_redirects_data and in_array($cur_post_ID, array_keys($all_redirects_data))) {
        exit(
            wp_redirect(
                get_permalink(
                    $all_redirects_data[$cur_post_ID]
                )
            )
        );
    }
}


add_action('wp_ajax_action_remove_redirect', 'function_remove_redirect');

function function_remove_redirect()
{
    $option_name = 'all_redirects_data';

    $from_id = isset($_POST['from_id']) && !empty($_POST['from_id']) ? $_POST['from_id'] : false;

    $from_id = intval($from_id);

    $all_redirects_data = get_redirects_array();

    unset($all_redirects_data[$from_id]);

    update_option($option_name, serialize($all_redirects_data));

    wp_send_json($all_redirects_data, 200);
}


function get_redirects_array()
{

    $option_name = 'all_redirects_data';

    $all_redirects_data = get_option($option_name);
    $all_redirects_data = unserialize($all_redirects_data);

    if (!is_array($all_redirects_data)) return [];

    return $all_redirects_data;

}

