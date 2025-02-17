<?php
/**
 * Plugin Name: Cline WordPress Integration
 * Description: Enables REST API endpoints for Cline-generated content integration
 * Version: 1.0
 * Author: Cline
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class ClineWPIntegration {
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_endpoints'));
        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init() {
        // Add JWT authentication support
        if (!class_exists('JWT_AUTH_Public')) {
            add_action('admin_notices', function() {
                echo '<div class="error"><p>Cline WP Integration requires JWT Authentication plugin. Please install and activate it.</p></div>';
            });
        }
    }

    public function register_endpoints() {
        // Register custom CSS endpoint
        register_rest_route('custom-css/v1', '/add', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_custom_css'),
            'permission_callback' => array($this, 'check_permission'),
            'args' => array(
                'css' => array(
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Custom CSS to add to the theme'
                )
            )
        ));
    }

    public function check_permission() {
        return current_user_can('edit_theme_options');
    }

    public function handle_custom_css($request) {
        $css = $request->get_param('css');
        
        // Sanitize CSS
        $css = wp_strip_all_tags($css);
        
        // Save CSS using WordPress Customizer
        $custom_css = wp_get_custom_css();
        $custom_css .= "\n/* Cline Generated Styles */\n" . $css;
        
        $result = wp_update_custom_css_post($custom_css);
        
        if (is_wp_error($result)) {
            return new WP_Error(
                'css_update_failed',
                'Failed to update custom CSS',
                array('status' => 500)
            );
        }
        
        return new WP_REST_Response(array(
            'message' => 'Custom CSS added successfully',
            'status' => 200
        ));
    }
}

// Initialize the plugin
new ClineWPIntegration();

// Add activation hook
register_activation_hook(__FILE__, 'cline_wp_integration_activate');

function cline_wp_integration_activate() {
    // Check if JWT Authentication plugin is active
    if (!class_exists('JWT_AUTH_Public')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('This plugin requires JWT Authentication plugin. Please install and activate it first.');
    }
    
    // Flush rewrite rules for new endpoints
    flush_rewrite_rules();
}
