<?php
/**
 * Plugin Name: CJ Authorship
 * Plugin URI: http://sites.uci.edu/cwalsh/
 * Description: Add custom author information to a post, including multiple authors.
 * Version: 0.0.1
 * Author: Christopher J. Walsh
 * Author URI: http://sites.uci.edu/cwalsh/
 * Text Domain: cjauthorship
 * License: GPL3
 */

defined('ABSPATH') or die("No script kiddies please!");

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__));

@define('CJ_AUTHORSHIP_VERSION_OPTION', 'cj_authorship_version');

require_once 'inc/cj_authorship_handler.php';

// plugin install
register_activation_hook(__FILE__, 'cj_authorship_install');

add_action('init', array(new \CJ_Authorship\CJ_Authorship_Handler(), 'init'));

add_action('plugins_loaded', 'cj_authorship_check');

// FUNCTIONS

/**
 * this will run plugin update checks
 */
function cj_authorship_check() {
    if(get_site_option(CJ_AUTHORSHIP_VERSION_OPTION) != \CJ_Authorship\CJ_Authorship_Handler::VERSION) {
        cj_authorship_install();
    }
}

/**
 * install plugin
 * @global object $wpdb;
 */
function cj_authorship_install() {
    global $wpdb;
    
    $tableName = $wpdb->prefix . \CJ_Authorship\CJ_Authorship_Handler::TABLE_SUFFIX;
    $charsetCollate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $tableName ("
            . "id INT(11) NOT NULL AUTO_INCREMENT,"
            . "post_id INT(11) NOT NULL,"
            . "ordinal SMALLINT(2) NOT NULL DEFAULT 0,"
            . "fullname VARCHAR(255),"
            . "description TEXT,"
            . "UNIQUE KEY id (id)) $charsetCollate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    
    add_option(CJ_AUTHORSHIP_VERSION_OPTION, CJ_Authorship\CJ_Authorship_Handler::VERSION);
    
    cj_authorship_install_options();
}

function cj_authorship_install_options() {
    global $wpdb;
    
    $tableName = $wpdb->prefix . \CJ_Authorship\CJ_Authorship_Handler::TABLE_OPTIONS_SUFFIX;
    $charsetCollate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $tableName ("
            . "id INT(11) NOT NULL AUTO_INCREMENT,"
            . "post_id INT(11) NOT NULL,"
            . "option_key VARCHAR(255) NOT NULL,"
            . "option_value TEXT,"
            . "UNIQUE KEY id (id)) $charsetCollate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}