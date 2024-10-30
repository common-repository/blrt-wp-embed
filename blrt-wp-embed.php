<?php
/**
* Plugin Name: Blrt WP Embed
* Plugin URI: http://www.blrt.com/wordpress-plugin
* Description: Enable Blrts and Blrt Galleries in your pages and posts - just like YouTube videos.
* Version: 1.6.9
* Author: Blrt
* Author URI: http://www.blrt.com
* License: GPL2
*/

/* Copyright 2016  Blrt Operations Pty Ltd  (email : support@blrt.com)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2, as 
   published by the Free Software Foundation.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
defined( 'ABSPATH' ) or die( 'No direct access to this file' );

define('BLRT_WP_EMBED_ASSETS_VERSION', '1.6');
define('BLRT_WP_EMBED_VERSION', '1.6.9');
define('CDN_URL', 'https://s3.amazonaws.com/blrt-gallery/'.BLRT_WP_EMBED_ASSETS_VERSION);

class BlrtWPEmbed {
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'wpa_add_menu' ));
        add_action( 'wp_before_admin_bar_render', array($this, 'blrt_admin_bar'));
        add_action( 'admin_init', array( $this, 'blrt_settings_init') );
        
        add_action( 'wp_enqueue_scripts', array( $this, 'assets') );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets') );
        add_action( 'wp_footer', array($this, 'fallback_assets'), 500);
        add_filter( 'style_loader_tag', array( $this, 'fix_style_tags'), 10, 2);
        add_filter( 'script_loader_tag', array( $this, 'fix_script_tags'), 10, 2);
        
        register_activation_hook( __FILE__, array( $this, 'wpa_install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'wpa_uninstall' ) );
        add_action( 'plugins_loaded', array( $this, 'wpa_upgrade') );
        $this->blrtwpembed_table_gallery_version = '1.2'; //add version number for the table in case we need to update the structure of table later
    }
    
    public function init() {
        global $wp_version;
        
        if (!wp_script_is('open-sans', 'registered')) {
            wp_register_style( 'open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,300,400,600&subset=latin,latin-ext', [], null);
        }
        
        if (defined('BLRT_WP_EMBED_DEV_ENV') && BLRT_WP_EMBED_DEV_ENV === true) {
            wp_register_script('owlcarousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js', ['jquery'], null, true);
            wp_register_script('ua-parser', 'https://cdnjs.cloudflare.com/ajax/libs/UAParser.js/0.7.12/ua-parser.min.js', ['jquery'], null, true);
            wp_register_script('jquery-areyousure', plugins_url('dist/js/jquery.are-you-sure.min.js', __FILE__), ['jquery'], '1.9.0', true);
            wp_register_script('blrt-wp-embed', plugins_url('dist/js/admin.min.js', __FILE__), ['jquery', 'jquery-ui-sortable', 'jquery-areyousure'], BLRT_WP_EMBED_VERSION, true);
            wp_register_script('blrt-gallery', plugins_url('dist/js/main.min.js', __FILE__), ['jquery', 'owlcarousel', 'ua-parser'], BLRT_WP_EMBED_ASSETS_VERSION, true);
            wp_register_script('blrt-oembed', 'https://e.blrt.com/js/oembed.js', [], null, true);
            
            wp_register_style('owlcarousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css', [], null);
            wp_register_style('blrt-wp-embed', plugins_url('dist/css/admin.min.css',  __FILE__), [], BLRT_WP_EMBED_VERSION);
            wp_register_style('blrt-wp-embed-global', plugins_url('dist/css/admin-global.min.css',  __FILE__), [], BLRT_WP_EMBED_VERSION);
            wp_register_style('blrt-gallery', plugins_url('dist/css/main.min.css',  __FILE__), [], BLRT_WP_EMBED_ASSETS_VERSION);
            wp_register_style('blrt-web', plugins_url('dist/css/blrt-web.min.css',  __FILE__), [], BLRT_WP_EMBED_ASSETS_VERSION);
            wp_register_style('blrt-snippet', plugins_url('dist/css/snippet.min.css',  __FILE__), [], BLRT_WP_EMBED_ASSETS_VERSION);
        } else {
            wp_register_script('owlcarousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js', ['jquery'], null, true);
            wp_register_script('ua-parser', 'https://cdnjs.cloudflare.com/ajax/libs/UAParser.js/0.7.12/ua-parser.min.js', ['jquery'], null, true);
            wp_register_script('jquery-areyousure', plugins_url('dist/js/jquery.are-you-sure.min.js', __FILE__), ['jquery'], '1.9.0', true);
            wp_register_script('blrt-wp-embed', plugins_url('dist/js/admin.min.js', __FILE__), ['jquery', 'jquery-ui-sortable', 'jquery-areyousure'], BLRT_WP_EMBED_VERSION, true);
            wp_register_script('blrt-gallery', CDN_URL.'/main.min.js', ['jquery', 'owlcarousel', 'ua-parser'], null, true);
            wp_register_script('blrt-oembed', 'https://e.blrt.com/js/oembed.js', [], null, true);
            
            wp_register_style('owlcarousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css', [], null);
            wp_register_style('blrt-wp-embed', plugins_url('dist/css/admin.min.css',  __FILE__), [], BLRT_WP_EMBED_VERSION);
            wp_register_style('blrt-wp-embed-global', plugins_url('dist/css/admin-global.min.css',  __FILE__), [], BLRT_WP_EMBED_VERSION);
            wp_register_style('blrt-gallery', CDN_URL.'/main.min.css', [], null);
            wp_register_style('blrt-web', CDN_URL.'/blrt-web.min.css', [], null);
            wp_register_style('blrt-snippet', CDN_URL.'/snippet.min.css', [], null);
        }
        
        include_once dirname( __FILE__ ).'/includes/shortcodes.php';
        $this->add_oembed_providers();
        $this->setup_tinymce_plugin();
    }
    
    function menu_highlight() {
    	global $parent_file, $submenu_file;
    	if ($parent_file === 'blrt' && isset($_GET['action']) && $_GET['action'] === 'edit') {
    	    $submenu_file = 'blrt-galleries';
    	}
    }
    
    function fix_script_tags($html, $handle) {
        if (in_array($handle, ['blrt-oembed']) && strpos($html, 'async') === false) {
            $html = preg_replace('/ src=/', ' async=\'async\' src=', $html);
        }
        return $html;
    }
    
    function fix_style_tags($html, $handle) {
        if (in_array($handle, ['blrt-gallery', 'blrt-web', 'blrt-wp-embed', 'owlcarousel']) && strpos($html, 'crossorigin') === false) {
            $html = preg_replace('/ \/>/', ' crossorigin=\'anonymous\' />', $html);
        }
        return $html;
    }
    
    function assets() {
        // Non-admin pages:
        // When admin-bar is showing:
        if (is_admin_bar_showing()) {
            wp_enqueue_style('blrt-wp-embed-global');
        }
    }
    
    function admin_assets($hook) {
        /* Possible values for $hook:
            blrt_page_blrt-galleries
            blrt_page_blrt-add-gallery
            blrt_page_blrt-web
            blrt_page_blrt-settings */
        // All admin pages:
        wp_enqueue_style('blrt-wp-embed-global');
        // Blrt admin pages:
        if (substr($hook, 0, 4) === "blrt") { 
            wp_enqueue_script('jquery');
            wp_enqueue_style('blrt-wp-embed');
            wp_enqueue_script('blrt-wp-embed');
            add_action( 'admin_head', array( $this, 'menu_highlight') );
            // Blrt add/edit gallery page:
            if ('blrt_page_blrt-add-gallery' === $hook) {
                remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('jquery-touch-punch');
                wp_enqueue_script('jquery-areyousure');
            }
        }
    }
    
    function fallback_assets() {
        echo "<script type='text/javascript' class='hidden' id='blrt-wp-embed-fallbacks'>\n";
        if (wp_script_is( 'jquery', 'enqueued' )) {
            echo "(window.jQuery && jQuery.noConflict()) || document.write('<script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js\"><\/script>');\n";
        }
        if (wp_script_is( 'owlcarousel', 'enqueued' )) {
            echo "(jQuery(window).owlCarousel) || document.write('<script src=\"".plugins_url('dist/js/owl.carousel.min.js'.'?ver='.'2.2.1', __FILE__)."\"><\/script>');\n";
        }
        if (wp_script_is( 'ua-parser', 'enqueued' )) {
            echo "(window.UAParser) || document.write('<script src=\"".plugins_url('dist/js/ua-parser.min.js?ver=0.7.12', __FILE__)."\"><\/script>');\n";
        }
        if (wp_script_is( 'blrt-gallery', 'enqueued' )) {
            echo "(window.blrt_gallery_js_loaded) || document.write('<script src=\"".plugins_url('dist/js/main.min.js?ver='.BLRT_WP_EMBED_ASSETS_VERSION, __FILE__)."\"><\/script>');\n";
        }
        echo "(function($) {
            $(window).load(function() {
                $.each(document.styleSheets, function(i,sheet){";
        if (wp_style_is( 'owlcarousel', 'enqueued' )) {
            echo "  if (sheet.ownerNode.id == 'owlcarousel') {
                        try {
                            var rules = sheet.rules ? sheet.rules : sheet.cssRules;
                        } catch(e) {
                            return;
                        }
                        if (rules.length == 0) {
                            //$('<link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css\" \/>').appendTo('body');
                            $('<link rel=\"stylesheet\" type=\"text/css\" href=\"".plugins_url('dist/css/owl.carousel.min.css'.'?ver='.'2.2.1',  __FILE__)."\" \/>').appendTo('body');
                        }
                        return;
                    }";
        }
        if (wp_style_is( 'blrt-gallery', 'enqueued' )) {
            echo "  if (sheet.ownerNode.id == 'blrt-gallery') {
                        try {
                            var rules = sheet.rules ? sheet.rules : sheet.cssRules;
                        } catch(e) {
                            return;
                        }
                        if (rules.length == 0) {
                            //$('<link rel=\"stylesheet\" type=\"text/css\" href=\"".CDN_URL.'/main.min.css'."\" \/>').appendTo('body');
                            $('<link rel=\"stylesheet\" type=\"text/css\" href=\"".plugins_url('dist/css/main.min.css'.'?ver='.BLRT_WP_EMBED_ASSETS_VERSION,  __FILE__)."\" \/>').appendTo('body');
                        }
                        return;
                    }";
        }
        if (wp_style_is( 'blrt-web', 'enqueued' )) {
            echo "  if (sheet.ownerNode.id == 'blrt-web') {
                        try {
                            var rules = sheet.rules ? sheet.rules : sheet.cssRules;
                        } catch(e) {
                            return;
                        }
                        if (rules.length == 0) {
                            //$('<link rel=\"stylesheet\" type=\"text/css\" href=\"".CDN_URL.'/blrt-web.min.css'."\" \/>').appendTo('body');
                            $('<link rel=\"stylesheet\" type=\"text/css\" href=\"".plugins_url('dist/css/blrt-web.min.css'.'?ver='.BLRT_WP_EMBED_ASSETS_VERSION,  __FILE__)."\" \/>').appendTo('body');
                        }
                        return;
                    }";
        }
        echo "      return;
                });
            });
        })(jQuery);\n";
        //'<script>(typeof jQuery.ui.sortable !== "undefined" || document.write(\'<script src="'.plugins_url('js/jquery-ui.min.js?v=1.12.1', __FILE__).'"><\/script>\')</script>';
        echo "</script>";
    }
    
    function wpa_add_menu() {
        add_menu_page( 'Blrt', 'Blrt' , 'nosuchcapability', 'blrt', null, 'none', '25.1');
        add_submenu_page( 'blrt', 'Blrt galleries', 'Blrt Galleries', 'edit_pages', 'blrt-galleries', array(
                              $this,
                             'show_all'
                            ));
        add_submenu_page( 'blrt', 'Blrt gallery', 'Add New Gallery', 'edit_pages', 'blrt-add-gallery', array(
                              $this,
                             'add_new'
                            ));
        add_submenu_page( 'blrt', 'Blrt Web', 'Blrt Web', 'read', 'blrt-web', array(
                              $this,
                             'web'
                            ));
        add_submenu_page( 'blrt', 'Blrt settings', 'Settings', 'manage_options', 'blrt-settings', array(
                              $this,
                             'setting'
                            ));
    }
    
    function blrt_admin_bar() {
    	global $wp_admin_bar;
    	$options = get_option( 'blrt_web_settings' );
    	if (is_array($options) && $options['blrt_web_toolbar_enabled']) {
        	$args = array(
        		'id'    => 'blrt-web',
        		'title' => '<span class="ab-icon"></span>',
        		'href'  => '/wp-admin/admin.php?page=blrt-web'
        	);
        	$wp_admin_bar->add_node( $args );
    	}
    }
    
    function blrt_settings_init(  ) { 
    	register_setting( 'blrt_settings', 'blrt_web_settings' );
    	add_settings_section(
    		'blrt_web_settings_section', 
    		__( 'Blrt Web', 'blrt' ), 
    		array($this, 'blrt_web_settings_section_callback'), 
    		'blrt_settings'
    	);
    	add_settings_field( 
    		'blrt_web_toolbar_enabled', 
    		__( 'Show the Blrt Web icon in the WordPress toolbar', 'blrt' ), 
    		array($this, 'blrt_web_toolbar_enabled_render'), 
    		'blrt_settings', 
    		'blrt_web_settings_section' 
    	);
    }
    
    function blrt_web_settings_section_callback(  ) { 
    	//echo __( 'Blrt Web', 'blrt' );
    }
    
    function blrt_web_toolbar_enabled_render(  ) { 
    	$options = get_option( 'blrt_web_settings' );
    	?>
    	<input type='checkbox' name='blrt_web_settings[blrt_web_toolbar_enabled]' <?php is_array($options) && $options['blrt_web_toolbar_enabled'] ? checked( $options['blrt_web_toolbar_enabled'], 1 ) : ''; ?> value='1'>
    	<?php
    }
    
    function show_all(){
        include dirname( __FILE__ ).'/includes/blrt-galleries.php';
    }
    
    function add_new(){
        include dirname( __FILE__ ).'/includes/blrt-add-gallery.php';
    }
    
    function setting(){
        include dirname( __FILE__ ).'/includes/blrt-settings.php';
    }
    
    function web(){
        include dirname( __FILE__ ).'/includes/blrt-web.php';
    }
    
    private function add_oembed_providers() {
        $convertible_servers = $this->get_convertible_servers();
        $convertible_short_servers = $this->get_convertible_short_servers();
        $oembed_server = $this->get_oembed_server();
        
        if(!($convertible_servers && $convertible_short_servers && $oembed_server)) return false;
        
        $preg_convertible_servers = '(' . implode( '|', array_map( 'preg_quote', $convertible_servers ) ) . ')';
        $preg_convertible_short_servers = '(' . implode( '|', array_map( 'preg_quote', $convertible_short_servers ) ) . ')';
        wp_oembed_add_provider( "#https?://$preg_convertible_servers/(embed/?/)?(conv/.*?/)?blrt/.*#i", "https://$oembed_server/oembed", true );
        wp_oembed_add_provider( "#https?://$preg_convertible_short_servers/.*#i", "https://$oembed_server/oembed", true );
        return true;
    }
    
    private function get_convertible_servers() {
        return apply_filters( 'blrt_wp_embed_convertible_servers' , array(
            'e.blrt.com',
            'm.blrt.co'
        ) );
    }
    
    function wpa_install() {//intialise the table blrtwpembed
       global $wpdb;

       $table_name = $wpdb->prefix . "blrtwpembed"; 
       
       $charset_collate = $wpdb->get_charset_collate();
       
       $sql = "CREATE TABLE " . $table_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          name tinytext NOT NULL,
          title tinytext,
          url text DEFAULT '' NOT NULL,
          UNIQUE KEY id (id)
        ) " . $charset_collate . ";";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        add_option( 'blrtwpembed_table_gallery_version', $this->blrtwpembed_table_gallery_version );
        add_option( 'blrtwpembed_table_previous_versions', '' );
        add_option( 'blrt_web_settings', array('blrt_web_toolbar_enabled' => 1) );
    }
    
    function wpa_uninstall(){
        
    }
    
    function upgrade_success() {
    	$class = 'notice notice-success is-dismissible';
    	$message = 'The Blrt WP Embed database was upgraded to accomodate new features!';
    	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
    }
    
    function upgrade_failure() {
    	$class = 'notice notice-error';
    	$message = 'The Blrt WP Embed database failed to upgrade. Please report this error to hi@blrt.com';
    	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
    }
    
    function wpa_upgrade(){
        if (get_option('blrtwpembed_table_gallery_version') == $this->blrtwpembed_table_gallery_version) {
            return;
        }
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $table_name = $wpdb->prefix . "blrtwpembed";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE " . $table_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          name tinytext NOT NULL,
          title tinytext,
          url text DEFAULT '' NOT NULL,
          UNIQUE KEY id (id)
        ) " . $charset_collate . ";";
        dbDelta( $sql );
        if (get_option('blrtwpembed_table_gallery_version') == false || (get_option('blrtwpembed_upgraded_from_jal') == false && get_option('jal_db_version') == '1.0') ) {
            delete_option( 'jal_db_version');
            update_option( 'blrtwpembed_upgraded_from_jal', 1 );
            if (get_option('blrtwpembed_table_previous_versions') == '') {
                update_option( 'blrtwpembed_table_previous_versions', '1.0');
            } else {
                update_option( 'blrtwpembed_table_previous_versions', get_option('blrtwpembed_table_previous_versions').',1.0' );
            }
        } else if (get_option('blrtwpembed_table_gallery_version') == '1.1') {
            if (get_option('blrtwpembed_table_previous_versions') == '') {
                update_option( 'blrtwpembed_table_previous_versions', '1.1');
            } else {
                update_option( 'blrtwpembed_table_previous_versions', get_option('blrtwpembed_table_previous_versions').',1.1' );
            }
        }
        add_action( 'admin_notices', array($this, 'upgrade_success') );
        update_option( 'blrtwpembed_table_gallery_version', $this->blrtwpembed_table_gallery_version );
    }

    /**
    * Check if the current user can edit Posts or Pages, and is using the Visual Editor
    * If so, add some filters so we can register our plugin
    */
    function setup_tinymce_plugin() {

        // Check if the logged in WordPress User can edit Posts or Pages
        // If not, don't register our TinyMCE plugin
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }

        // Check if the logged in WordPress User has the Visual Editor enabled
        // If not, don't register our TinyMCE plugin
        if ( get_user_option( 'rich_editing' ) !== 'true' ) {
            return;
        }

        // Setup some filters
        add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ) );
        add_filter( 'mce_buttons', array( &$this, 'add_tinymce_toolbar_button' ) );

    }

    /**
     * Adds a TinyMCE plugin compatible JS file to the TinyMCE / Visual Editor instance
     *
     * @param array $plugin_array Array of registered TinyMCE Plugins
     * @return array Modified array of registered TinyMCE Plugins
     */
    function add_tinymce_plugin( $plugin_array ) {

        $plugin_array['custom_class'] = plugin_dir_url( __FILE__ ) . 'dist/js/tinymce.min.js';
        return $plugin_array;

    }

    /**
     * Adds a button to the TinyMCE / Visual Editor which the user can click
     * to insert a custom CSS class.
     *
     * @param array $buttons Array of registered TinyMCE Buttons
     * @return array Modified array of registered TinyMCE Buttons
     */
    function add_tinymce_toolbar_button( $buttons ) {

        array_push( $buttons, 'custom_class' );
        return $buttons;

    }

    private function get_convertible_short_servers() {
        return apply_filters( 'blrt_wp_embed_convertible_short_servers' , array(
            'r.blrt.com'
        ) );
    }
    
    private function get_oembed_server() {
        return apply_filters( 'blrt_wp_embed_oembed_server', 'e.blrt.com' );
    }
    
    
}

$blrtwp_embed = new BlrtWPEmbed();
