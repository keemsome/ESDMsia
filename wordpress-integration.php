<?php
/**
 * Cline to WordPress Integration Script
 * 
 * This script handles the transfer of Cline-generated content to WordPress via REST API
 */

class ClineWordPressIntegration {
    private $wp_url;
    private $username;
    private $password;
    private $auth_token;

    public function __construct($wp_url, $username, $password) {
        $this->wp_url = rtrim($wp_url, '/');
        $this->username = $username;
        $this->password = $password;
        $this->auth_token = null;
    }

    /**
     * Authenticate with WordPress
     */
    public function authenticate() {
        $auth_url = $this->wp_url . '/wp-json/jwt-auth/v1/token';
        $response = wp_remote_post($auth_url, array(
            'body' => array(
                'username' => $this->username,
                'password' => $this->password
            )
        ));

        if (is_wp_error($response)) {
            throw new Exception('Authentication failed: ' . $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response));
        $this->auth_token = $body->token;
    }

    /**
     * Process Cline HTML content
     */
    public function processHtml($html_file) {
        $dom = new DOMDocument();
        $dom->loadHTMLFile($html_file);

        // Extract main content sections
        $sections = array();
        foreach ($dom->getElementsByTagName('section') as $section) {
            $sections[] = array(
                'id' => $section->getAttribute('id'),
                'content' => $dom->saveHTML($section),
                'class' => $section->getAttribute('class')
            );
        }

        return $sections;
    }

    /**
     * Process CSS content
     */
    public function processCss($css_file) {
        return file_get_contents($css_file);
    }

    /**
     * Create WordPress page
     */
    public function createPage($title, $content) {
        if (!$this->auth_token) {
            throw new Exception('Not authenticated');
        }

        $api_url = $this->wp_url . '/wp-json/wp/v2/pages';
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->auth_token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'title' => $title,
                'content' => $content,
                'status' => 'publish'
            ))
        ));

        if (is_wp_error($response)) {
            throw new Exception('Failed to create page: ' . $response->get_error_message());
        }

        return json_decode(wp_remote_retrieve_body($response));
    }

    /**
     * Add custom CSS to WordPress
     */
    public function addCustomCss($css) {
        if (!$this->auth_token) {
            throw new Exception('Not authenticated');
        }

        $api_url = $this->wp_url . '/wp-json/custom-css/v1/add';
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->auth_token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'css' => $css
            ))
        ));

        if (is_wp_error($response)) {
            throw new Exception('Failed to add CSS: ' . $response->get_error_message());
        }

        return json_decode(wp_remote_retrieve_body($response));
    }
}

// Usage example:
/*
$integration = new ClineWordPressIntegration(
    'https://your-wordpress-site.com',
    'your-username',
    'your-password'
);

try {
    // Authenticate
    $integration->authenticate();

    // Process HTML and CSS
    $sections = $integration->processHtml('index.html');
    $css = $integration->processCss('styles/main.css');

    // Create page in WordPress
    $page = $integration->createPage('Alliance ESD Malaysia', $sections[0]['content']);
    
    // Add custom CSS
    $integration->addCustomCss($css);

    echo "Content successfully transferred to WordPress!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
*/
