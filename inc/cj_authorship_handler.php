<?php
namespace CJ_Authorship;

class CJ_Authorship_Handler {
    const VERSION = '0.0.1';
    const TABLE_SUFFIX = 'cj_authorship_authors';
    const METABOX_KEY = 'cj_authorship';
    const TABLE_OPTIONS_SUFFIX = 'cj_authorship_options';
    const OPTION_DISPLAY_AUTHOR = 'display_cj_author';
    
    public $pluginDir;
    
    private static $instance;
    
    public function __construct() {
        $this->pluginDir = dirname(dirname(plugin_basename(__FILE__)));
    }
    
    public function init() {
        add_action('wp_ajax_cj_authorship_add', array($this, 'add'));
        add_action('wp_ajax_cj_authorship_get_all_by_post', array($this, 'getAllByPost'));
        add_action('wp_ajax_cj_authorship_update', array($this, 'update'));
        add_action('wp_ajax_cj_authorship_delete', array($this, 'delete'));
        add_action('wp_ajax_cj_authorship_reorder', array($this, 'reorder'));
        add_action('wp_ajax_cj_authorship_is_displayed', array($this, 'isDisplayed'));
        add_action('wp_ajax_cj_authorship_display', array($this, 'display'));
        
        add_action('add_meta_boxes', array($this, 'metabox'), 11, 2);
        
        add_action('save_post', array($this, 'save'));
        
        self::$instance = $this;
    }
    
    public static function getInstance() {
        return self::$instance;
    }
    
    public function display() {
        global $wpdb;
        
        $postId = $_POST['post_id'];
        $isDisplayed = ($_POST['is_displayed'] == 'yes') ? 1 : 0;
        
        $tableName = $wpdb->prefix.self::TABLE_OPTIONS_SUFFIX;
        $post = $wpdb->get_row("SELECT a.* FROM $tableName AS a WHERE a.post_id = '$postId' LIMIT 0,1");
        
        if(empty($post)) {
            $wpdb->insert($tableName, array(
                'post_id' => $postId,
                'option_key' => self::OPTION_DISPLAY_AUTHOR,
                'option_value' => strval($isDisplayed)
            ), array('%d', '%s', '%s'));
        } else {
            $wpdb->update($tableName, array(
                'option_value' => strval($isDisplayed)
            ), array('post_id' => $postId), array('%s'), array('%d'));
        }
        
        if(function_exists('json_encode')) {
            echo json_encode($_POST);
        }
        
        die();
    }
    
    public function reorder() {
        global $wpdb;
        
        $postId = $_POST['post_id'];
        $authorId = $_POST['author_id'];
        $ordinal = $_POST['order'];
        
        $wpdb->update($wpdb->prefix.self::TABLE_SUFFIX, array('ordinal' => $ordinal), array('id' => $authorId), array('%d'), array('%d'));
        
        if(function_exists('json_encode')) {
            echo json_encode($_POST);
        }
        
        die();
    }
    
    public function delete() {
        global $wpdb;
        
        $postId = $_POST['post_id'];
        $authorId = $_POST['author_id'];
        
        $wpdb->delete($wpdb->prefix.self::TABLE_SUFFIX, array('id' => $authorId), array('%d'));
        
        if(function_exists('json_encode')) {
            echo json_encode($_POST);
        }
        
        die();
    }
    
    public function update() {
        global $wpdb;
        
        $postId = $_POST['post_id'];
        $authorId = $_POST['author_id'];
        $authorName = $_POST['author_name'];
        $authorDesc = $_POST['desc'];
        
        $wpdb->update($wpdb->prefix.self::TABLE_SUFFIX, array(
            'fullname' => $authorName,
            'description' => $authorDesc
        ), array(
            'id' => $authorId
        ), array(
            '%s',
            '%s'
        ), array('%d'));
        
        if(function_exists('json_encode')) {
            echo json_encode($_POST);
        }
        
        die();
    }
    
    public function getAllByPost() {
        global $wpdb;
        
        $post_id = $_GET['post_id'];
        
        $authors = $this->getAuthors($post_id);
        
        $html = '<!-- list items for authorship metabox -->';
        ob_start();
        include 'templates/admin_author_list.php';
        $html .= ob_get_clean();
        
        echo $html;
        
        die();
    }
    
    public function add() {
        global $wpdb;
        
        $post_id = $_POST['post_id'];
        $author_name = $_POST['author_name'];
        $desc = $_POST['desc'];
        
        $wpdb->insert($wpdb->prefix.self::TABLE_SUFFIX, array(
            'post_id' => $post_id,
            'fullname' => $author_name,
            'description' => $desc
        ), array(
            '%d',
            '%s',
            '%s'
        ));
        
        if(function_exists('json_encode')) {
            echo json_encode($_POST);
        }
        
        die();
    }
    
    public function metabox($post_type, $post) {
        $postFormat = get_post_format();
        
        if($post_type == 'post') {
            add_meta_box(self::METABOX_KEY, 'Author(s)', array($this, 'displayMetabox'), $post_type);
        }
    }
    
    public function displayMetabox($post) {
        wp_enqueue_script('authorship-admin', plugins_url() . '/' . $this->pluginDir . '/js/admin.js', array('jquery'));
        
        $authors = $this->getAuthors($post->ID);
        $isDisplayed = $this->isDisplayed($post->ID);
        
        $html = '<!-- begin admin panel for authorship metabox -->';
        ob_start();
        include 'templates/admin_authorship.php';
        $html .= ob_get_clean();
        
        echo $html;
    }
    
    public function save($post_id) {
        if(wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return $post_id;
        }
    }
    
    public function getAuthors($post_id) {
        global $wpdb;
        
        $tableName = $wpdb->prefix.self::TABLE_SUFFIX;
        $authors = $wpdb->get_results(""
                . "SELECT a.id, a.post_id, a.ordinal, a.fullname, a.description "
                . "FROM $tableName AS a "
                . "WHERE a.post_id = '$post_id' "
                . "ORDER BY a.ordinal "
                . "ASC");
        
        return $authors;
    }
    
    public function isDisplayed($postId) {
        global $wpdb;
        
        $keyName = self::OPTION_DISPLAY_AUTHOR;
        
        $tableName = $wpdb->prefix.self::TABLE_OPTIONS_SUFFIX;
        $option = $wpdb->get_col(""
                . "SELECT a.id "
                . "FROM $tableName AS a "
                . "WHERE a.post_id = '$postId' "
                . "AND a.option_key = '$keyName' "
                . "AND a.option_value = '1' "
                . "LIMIT 0,1");
        
        return (empty($option)) ? false : true;
    }
}