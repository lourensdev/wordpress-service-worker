<?php
/**
 * Plugin Name: DynoCap Worker
 * Plugin URI: https://github.com/lourensdev/wordpress-service-worker
 * Description: Service Worker specifically made for any WordPress site.
 * Version: 1.0
 * Author: Lourens de Villiers
 * Author URI: https://github.com/lourensdev
 */



/**
 * Add service worker initialization scripts
 */
function dynocap_load_serviceworker() {
  wp_enqueue_script('dynocap_script', plugins_url('/js/dynocap-sw-init.js', __FILE__), array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'dynocap_load_serviceworker');



/**
 * Get firectory path to root of WordPress installation
 */
function dynocap_get_wp_config_path()
{
    $base = dirname(__FILE__);
    $path = false;

    if (@file_exists(dirname(dirname($base))."/wp-config.php"))
    {
        $path = dirname(dirname($base));
    }
    else
    if (@file_exists(dirname(dirname(dirname($base)))."/wp-config.php"))
    {
        $path = dirname(dirname(dirname($base)));
    }
    else
        $path = false;

    if ($path != false)
    {
        $path = str_replace("\\", "/", $path);
    }
    return $path;
}



/**
 * Copy defined service worker js file to WordPress root
 */
function dynocap_copy_sw_to_root() {

    $plugin_dir = plugin_dir_path( __FILE__ ) . 'js/serviceworker.js';
    $root_dir = dynocap_get_wp_config_path() . '/serviceworker.js';

    if (!copy($plugin_dir, $root_dir)) {
        echo "failed to copy $plugin_dir to $root_dir...\n";
    }
}



/**
 * Generate manifest.json file and copy to WordPress root
 */
function dynocap_create_manifest_json() {
  $jsonContent = '{
    "name": "'. get_bloginfo('name'). '",
    "short_name": "'. get_bloginfo('name'). '",
    "lang": "en-GB",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#000000"
  }';

  $file = dynocap_get_wp_config_path() . '/manifest.json'; 
  $open = fopen( $file, "w" ); 
  $write = fputs( $open, $jsonContent ); 
  fclose( $open );
}



/**
 * Add link to manifest file in WordPress head
 */
function dynocap_add_manifest_tag() {
    echo '<link rel="manifest" href="/manifest.json">';
}
add_action( 'wp_head', 'dynocap_add_manifest_tag', 10, 1 );



/**
 * Register settings for plugin
 */
function dynocap_register_settings() {
    add_option( 'dynocap_option_name', 'This is my option value.');
    register_setting( 'dynocap_options_group', 'dynocap_option_name', 'dynocap_callback' );
}
add_action( 'admin_init', 'dynocap_register_settings' );

function dynocap_activate() {
    dynocap_copy_sw_to_root();
    dynocap_create_manifest_json();
}

register_activation_hook( __FILE__, 'dynocap_activate' );

/**
 * Clean up files on deactivate
 */
function dynocap_deactivate() {
    $sw_file = dynocap_get_wp_config_path() . '/serviceworker.js';
    $manifest_file = dynocap_get_wp_config_path() . '/manifest.json';

    if (!unlink($sw_file)) {
        echo "unable to remove file $sw_file...\n";
    } else {
        echo "$sw_file successfully removed";
    }

    if (!unlink($manifest_file)) {
        echo "unable to remove file $manifest_file...\n";
    } else {
        echo "$manifest_file successfully removed";
    }
}

register_deactivation_hook( __FILE__, 'dynocap_deactivate' );