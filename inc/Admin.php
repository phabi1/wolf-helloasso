<?php

namespace Wolf\HelloAsso;

class Admin
{

    public function setup()
    {
        add_action('wolf_admin_menu', [$this, 'registerMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function registerMenu()
    {
        add_submenu_page(
            'wolf',
            'HelloAsso',
            'HelloAsso',
            'manage_options',
            'wolf_helloasso',
            [$this, 'renderSettingsPage']
        );
    }

    public function registerSettings()
    {
        register_setting('wolf_helloasso_settings', 'wolf_helloasso_organization_slug');
        register_setting('wolf_helloasso_settings', 'wolf_helloasso_credentials');

        add_settings_section(
            'wolf_helloasso_api_section',
            'API Credentials',
            [$this, 'renderApiSectionText'],
            'wolf_helloasso_settings'
        );

        add_settings_field(
            'wolf_helloasso_api_key',
            'API Key',
            [$this, 'renderApiKeyField'],
            'wolf_helloasso_settings',
            'wolf_helloasso_api_section'
        );

        add_settings_field(
            'wolf_helloasso_api_secret',
            'API Secret',
            [$this, 'renderApiSecretField'],
            'wolf_helloasso_settings',
            'wolf_helloasso_api_section'
        );

        add_settings_section(
            'wolf_helloasso_organization_section',
            'Organization',
            [$this, 'renderOrganizationSectionText'],
            'wolf_helloasso_settings'
        );

        add_settings_field(
            'wolf_helloasso_organization_slug',
            'Organization Slug',
            [$this, 'renderOrganizationSlugField'],
            'wolf_helloasso_settings',
            'wolf_helloasso_organization_section'
        );


    }

    public function renderApiSectionText()
    {
        echo '<p>Enter your HelloAsso API credentials below.</p>';
    }

    public function renderOrganizationSectionText()
    {
        echo '<p>Enter your HelloAsso organization slug (the part after helloasso.com/ in your URL).</p>';
    }

    public function renderApiKeyField()
    {
        $options = get_option('wolf_helloasso_credentials', []);
        $apiKey = $options['api_key'] ?? '';
        echo '<input type="text" name="wolf_helloasso_credentials[api_key]" value="' . esc_attr($apiKey) . '" class="regular-text">';
    }

    public function renderApiSecretField()
    {
        $options = get_option('wolf_helloasso_credentials', []);
        $apiSecret = $options['api_secret'] ?? '';
        echo '<input type="text" name="wolf_helloasso_credentials[api_secret]" value="' . esc_attr($apiSecret) . '" class="regular-text">';
    }

    public function renderOrganizationSlugField()
    {
        $organizationSlug = get_option('wolf_helloasso_organization_slug', '');
        echo '<input type="text" name="wolf_helloasso_organization_slug" value="' . esc_attr($organizationSlug) . '" class="regular-text">';
    }

    public function renderSettingsPage()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // add error/update messages

        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated'])) {
            // add settings saved message with the class of "updated"
            add_settings_error('wporg_messages', 'wporg_message', __('Settings Saved', 'wporg'), 'updated');
        }

        // show error/update messages
        settings_errors('wporg_messages');
        ?>
        <div class="wrap">
            <h1>HelloAsso Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('wolf_helloasso_settings'); ?>
                <?php do_settings_sections('wolf_helloasso_settings'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}