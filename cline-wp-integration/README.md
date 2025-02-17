# Cline WordPress Integration

This integration allows you to transfer Cline-generated content directly to WordPress using the WordPress REST API.

## Prerequisites

1. A WordPress site with:
   - REST API enabled
   - [JWT Authentication plugin](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/) installed and configured

## Installation

1. Install the WordPress Plugin:
   - Copy the `cline-wp-integration` folder to your WordPress plugins directory (`wp-content/plugins/`)
   - Activate the plugin through WordPress admin panel

2. Configure JWT Authentication:
   - Add the following to your `wp-config.php`:
     ```php
     define('JWT_AUTH_SECRET_KEY', 'your-secret-key-here');
     define('JWT_AUTH_CORS_ENABLE', true);
     ```

## Usage

1. Configure your WordPress credentials in the integration script:
   ```php
   $integration = new ClineWordPressIntegration(
       'https://your-wordpress-site.com',
       'your-username',
       'your-password'
   );
   ```

2. Run the integration script:
   ```php
   try {
       // Authenticate
       $integration->authenticate();

       // Process HTML and CSS
       $sections = $integration->processHtml('index.html');
       $css = $integration->processCss('styles/main.css');

       // Create page in WordPress
       $page = $integration->createPage('Your Page Title', $sections[0]['content']);
       
       // Add custom CSS
       $integration->addCustomCss($css);

       echo "Content successfully transferred to WordPress!\n";
   } catch (Exception $e) {
       echo "Error: " . $e->getMessage() . "\n";
   }
   ```

## Features

1. Content Transfer:
   - Converts Cline HTML sections to WordPress pages
   - Preserves section IDs and classes
   - Maintains content structure

2. Style Integration:
   - Automatically adds custom CSS to WordPress theme
   - Preserves all styling from Cline-generated content

3. Security:
   - Uses JWT authentication for secure API access
   - Validates and sanitizes all content before transfer
   - Requires proper WordPress permissions

## Troubleshooting

1. Authentication Issues:
   - Verify your WordPress credentials
   - Ensure JWT Authentication plugin is properly configured
   - Check WordPress user has sufficient permissions

2. Content Transfer Issues:
   - Verify WordPress REST API is enabled
   - Check file paths for HTML and CSS files
   - Ensure content follows WordPress formatting guidelines

## Support

For issues and feature requests, please create an issue in the repository or contact support.

## License

This integration is released under the MIT License. See the LICENSE file for details.
