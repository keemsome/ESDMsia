<?php
/**
 * Example script demonstrating how to use the Cline WordPress Integration
 * with the Alliance ESD Malaysia website content
 */

require_once 'wordpress-integration.php';

// Replace these with your WordPress site details
$wp_url = 'https://your-wordpress-site.com';
$username = 'your-username';
$password = 'your-password';

// Initialize the integration
$integration = new ClineWordPressIntegration($wp_url, $username, $password);

try {
    echo "Starting content transfer to WordPress...\n";

    // Authenticate with WordPress
    echo "Authenticating...\n";
    $integration->authenticate();

    // Process the HTML content
    echo "Processing HTML content...\n";
    $sections = $integration->processHtml('../index.html');

    // Process the CSS
    echo "Processing CSS...\n";
    $css = $integration->processCss('../styles/main.css');

    // Create the main page
    echo "Creating main page...\n";
    $page = $integration->createPage('Alliance ESD Malaysia', implode("\n", array_map(function($section) {
        return $section['content'];
    }, $sections)));

    // Add the custom CSS
    echo "Adding custom CSS...\n";
    $integration->addCustomCss($css);

    echo "\nContent successfully transferred to WordPress!\n";
    echo "Page ID: " . $page->id . "\n";
    echo "Page URL: " . $page->link . "\n";

} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    exit(1);
}

// Success message with next steps
echo "\nNext steps:\n";
echo "1. Log in to your WordPress admin panel\n";
echo "2. Visit the newly created page and verify the content\n";
echo "3. Check the Customizer to ensure CSS was properly applied\n";
echo "4. Make any necessary adjustments to layout or styling\n";
