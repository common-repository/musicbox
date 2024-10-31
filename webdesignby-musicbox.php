<?php

/*
Plugin Name: Musicbox
Plugin URI: http://www.webdesignby.com/wordpress/plugins/musicbox
Description: Add custom playlist widgets including preview clips from iTunes to your WordPress website.
Author: webdesignby.com
Version: 2.0.1
Author URI: http://www.webdesignby.com/
Text Domain: webdesignby-musicbox
*/

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$path = \plugin_dir_path( __FILE__ );
$base_url = \plugin_dir_url( __FILE__ );
require_once($path . 'config.php');

global $wpdb;
$model = new \Webdesignby\MusicboxModel($wpdb);


$config = array(
    'plugin_key' => "webdesignby_musicbox",
    'plugin_slug' => "webdesignby-musicbox",
    'plugin_path' => $path,
    'base_url' => $base_url,
    'actions' => array(
                        'admin_enqueue_scripts',
                        'init',
                        'admin_head',
                    ),
    'model' => $model,
);

$plugin = $webdesignby_musicbox = new \Webdesignby\MusicboxPlugin($config);

/*
 *  Create an Options Page in wp_admin settings
 */
$options_page_config = array(
                        'menu_title' => 'Musicbox Settings',
                        'page_title' => 'Musicbox Settings',
                        'page_slug' => $plugin->plugin_slug, // slug of settings page
                        'option_key' => $plugin->plugin_key, // prefix such as 'my_plugin'_height, 'my_plugin'_width
                        'plugin_key' => $plugin->plugin_key,
                        'plugin_path' => $plugin->plugin_path,
                        'capabilities' => 'manage_options',
                    );


// $options_page = $webdesignby_musicbox->createOptionsPage($options_page_config);

/*
 *  Create a custom wp-admin menu
 */
$admin_menu_config = array(
                        'menu_title' => __('Musicbox', $plugin->plugin_slug),
                        'page_title' => __('Musicbox Admin', $plugin->plugin_slug),
                        'page_slug' =>  $plugin->plugin_slug, // slug of settings page
                        'option_key' => $plugin->plugin_key, // prefix such as 'my_plugin'_height, 'my_plugin'_width
                        'plugin_path' => $plugin->plugin_path,
                        'capabilities' => 'manage_options',
                        'template' => 'admin/musicbox'
                    );

$webdesignby_musicbox->createAdminMenu( $admin_menu_config );


$admin_submenu_config = array(
                        'menu_title' => __('Add Musicbox', $plugin->plugin_slug),
                        'page_title' => __('Edit Musicbox', $plugin->plugin_slug),
                        'page_slug' =>  $plugin->plugin_slug. "-edit", // slug of settings page
                        'parent' => $plugin->plugin_slug,
                        'option_key' => $plugin->plugin_key, // prefix such as 'my_plugin'_height, 'my_plugin'_width
                        'plugin_path' => $plugin->plugin_path,
                        'capabilities' => 'manage_options',
                        'template' => 'admin/musicbox-edit'
                    );

$webdesignby_musicbox->createAdminMenu( $admin_submenu_config );

/* May be implemented in future
$admin_submenu_config = array(
                        'menu_title' => __('Musicbox Settings', \Webdesignby\Registry::$plugin_slug),
                        'page_title' => __('Musicbox Settings', \Webdesignby\Registry::$plugin_slug),
                        'page_slug' =>  \Webdesignby\Registry::$plugin_slug . "-settings", // slug of settings page
                        'parent' => \Webdesignby\Registry::$plugin_slug,
                        'option_key' => \Webdesignby\Registry::$plugin_key, // prefix such as 'my_plugin'_height, 'my_plugin'_width
                        'plugin_path' => \Webdesignby\Registry::$plugin_path,
                        'capabilities' => 'manage_options',
                        'template' => 'admin/musicbox-settings'
                    );

$webdesignby_musicbox->createAdminMenu( $admin_submenu_config );

 */



\register_activation_hook( __FILE__, array($plugin, 'install'));
\register_uninstall_hook( __FILE__, array("\Webdesignby\MusicboxPlugin", 'uninstall'));

include("webdesignby-musicbox-widget.php");