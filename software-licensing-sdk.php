<?php

/*
Plugin Name: Fluent Plugin Updater Example
Description: Plugin to demonstrate Fluent Plugin Updater functionality.
Plugin URI:   https://fluentcart.com
Version: 1.0.0
Author:       FluentCart
Author URI:   https://fluentcart.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  fluent-plugin-updater-example
Domain Path:  /language
*/

if (defined('FLUENT_PLUGIN_UPDATER_EXAMPLE_PLUGIN_PATH')) {
    return;
}

add_action('init', function () {

    if (!class_exists('\FluentUpdater\FluentLicensing')) {
        require_once plugin_dir_path(__FILE__) . 'updater/FluentLicensing.php';
        require_once plugin_dir_path(__FILE__) . 'updater/LicenseSettings.php';
    }

    $fluentLicensing = (new \YourPluginNameSpace\FluentLicensing())->register([
        'version'           => '1.0.0', // Current version of your plugin
        'item_id'           => 7560872, // Product ID from FluentCart
        'basename'          => plugin_basename(__FILE__), // Plugin basename (e.g., 'your-plugin/your-plugin.php')
        'api_url'           => 'https://myawesomecompany.com/', // Your store URL
        'plugin_title'      => 'Awesome Plugin', // Your plugin title

        // optional params
        'purchase_url'      => 'https://myawesomecompany.com/awesome-plugin/', // OPTIONAL: Purchase URL of your product
        'activate_url'      => admin_url('tools.php?page=fluent-plugin-updater-example-manage-license'), // OPTIONAL: provide the license manage page URL in your plugin
        'show_check_update' => true, // OPTIONAL: if you want to show the check update link in the plugins list page
        'settings_key'      => '__my_awesom_plugin_license_config', // OPTIONAL: the option key where the license data will be stored. Default: __{plugin_slug}_sl_info
    ]);

    // getting license
    $licenseMessage = $fluentLicensing->getLicenseMessages(); // this will return license message array if license is not activated or expired
    if ($licenseMessage) {
        // Showing this message in admin notice
        add_action('admin_notices', function () use ($licenseMessage) {
            $class = 'notice notice-error fc_message';
            $message = $licenseMessage['message'];
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), wp_kses_post($message));
        });
    }

    // Adding license settings page, you can skip this part if you want to create your own license settings page
    (new \YourPluginNameSpace\LicenseSettings())
        ->register($fluentLicensing)
        ->setConfig([
            'menu_title'      => 'Awesome License',
            'title'           => 'License Settings',
            'license_key'     => 'License Key',
            'purchase_url'    => 'https://yourstore.com/pricing', // your product purchase URL
            'account_url'     => 'https://yourstore.com/account', // your store account URL
            'plugin_name'     => 'Awesome Addon',

            // optional configs
            'action_renderer' => 'my_awesome_plugin_license_view', // optional, if you want to render content with your own with do_action('fluent_licenseing_render_my_awesome_plugin_license_view')
        ])
        ->addPage([
            'type'        => 'submenu', // menu, options, submenu
            'parent_slug' => 'tools.php' // required if type is submenu
        ]);

});
