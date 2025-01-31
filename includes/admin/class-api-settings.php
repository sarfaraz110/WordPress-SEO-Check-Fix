<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_API_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_api_settings_page'));
        add_action('admin_init', array($this, 'register_api_settings'));
    }

    public function add_api_settings_page() {
        add_submenu_page(
            'sums-seo-dashboard', // Parent menu slug
            'API Settings',       // Page title
            'API Settings',       // Menu title
            'manage_options',     // Capability
            'sums-api-settings',  // Menu slug
            array($this, 'render_api_settings_page') // Callback function
        );
    }

    public function register_api_settings() {
        register_setting('sums_api_settings_group', 'sums_moz_api_key');
        register_setting('sums_api_settings_group', 'sums_semrush_api_key');
        register_setting('sums_api_settings_group', 'sums_ahrefs_api_key');
        register_setting('sums_api_settings_group', 'sums_openai_api_key', array($this, 'sanitize_api_key'));
        register_setting('sums_api_settings_group', 'sums_openai_model');

        add_settings_section(
            'sums_api_settings_section',
            __('Third-Party API Settings', 'sums-solution'),
            array($this, 'render_api_settings_section'),
            'sums-api-settings'
        );

        add_settings_field(
            'sums_moz_api_key',
            __('Moz API Key', 'sums-solution'),
            array($this, 'render_moz_api_key_field'),
            'sums-api-settings',
            'sums_api_settings_section'
        );

        add_settings_field(
            'sums_semrush_api_key',
            __('SEMrush API Key', 'sums-solution'),
            array($this, 'render_semrush_api_key_field'),
            'sums-api-settings',
            'sums_api_settings_section'
        );

        add_settings_field(
            'sums_ahrefs_api_key',
            __('Ahrefs API Key', 'sums-solution'),
            array($this, 'render_ahrefs_api_key_field'),
            'sums-api-settings',
            'sums_api_settings_section'
        );

        add_settings_section(
            'sums_api_section',
            __('API Settings', 'sums-solution'),
            array($this, 'render_api_section'),
            'sums-api-settings'
        );

        add_settings_field(
            'sums_openai_api_key',
            __('OpenAI API Key', 'sums-solution'),
            array($this, 'render_openai_api_key_field'),
            'sums-api-settings',
            'sums_api_section'
        );

        add_settings_field(
            'sums_openai_model',
            __('OpenAI Model', 'sums-solution'),
            array($this, 'render_openai_model_field'),
            'sums-api-settings',
            'sums_api_section'
        );
    }

    public function sanitize_api_key($api_key) {
        return $this->encrypt_api_key($api_key);
    }

    public function render_api_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('API Settings', 'sums-solution'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('sums_api_settings_group');
                do_settings_sections('sums-api-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_api_settings_section() {
        echo '<p>' . esc_html__('Enter your API keys for third-party integrations. If an API key is not provided, the plugin will still work but with limited functionality.', 'sums-solution') . '</p>';
    }

    public function render_moz_api_key_field() {
        $moz_api_key = $this->decrypt_api_key(get_option('sums_moz_api_key', ''));
        echo '<input type="text" name="sums_moz_api_key" value="' . esc_attr($moz_api_key) . '" class="regular-text">';
    }

    public function render_semrush_api_key_field() {
        $semrush_api_key = $this->decrypt_api_key(get_option('sums_semrush_api_key', ''));
        echo '<input type="text" name="sums_semrush_api_key" value="' . esc_attr($semrush_api_key) . '" class="regular-text">';
    }

    public function render_ahrefs_api_key_field() {
        $ahrefs_api_key = $this->decrypt_api_key(get_option('sums_ahrefs_api_key', ''));
        echo '<input type="text" name="sums_ahrefs_api_key" value="' . esc_attr($ahrefs_api_key) . '" class="regular-text">';
    }

    public function render_api_section() {
        echo '<p>' . esc_html__('Configure API settings for the plugin.', 'sums-solution') . '</p>';
    }

    public function render_openai_api_key_field() {
        $api_key = $this->decrypt_api_key(get_option('sums_openai_api_key', ''));
        echo '<input type="password" name="sums_openai_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__('Your OpenAI API key is stored securely.', 'sums-solution') . '</p>';
    }

    public function render_openai_model_field() {
        $selected_model = get_option('sums_openai_model', 'gpt-4');
        $models = array(
            'gpt-4'            => 'GPT-4',
            'gpt-4-mini'       => 'GPT-4 Mini',
            'gpt-4-realtime'   => 'GPT-4 Realtime',
            'gpt-4-audio'      => 'GPT-4 Audio',
            'gpt-3.5-turbo'    => 'GPT-3.5 Turbo',
            'dall-e'           => 'DALL·E',
            'whisper'          => 'Whisper',
            'embeddings'       => 'Embeddings',
            'moderation'       => 'Moderation',
        );

        echo '<select name="sums_openai_model" id="sums_openai_model">';
        foreach ($models as $value => $label) {
            echo '<option value="' . esc_attr($value) . '"' . selected($selected_model, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    private function encrypt_api_key($api_key) {
        if (empty($api_key)) {
            return '';
        }
        return base64_encode(openssl_encrypt($api_key, 'AES-128-CBC', wp_salt(), 0, substr(wp_salt(), 0, 16)));
    }

    private function decrypt_api_key($encrypted_api_key) {
    if (empty($encrypted_api_key)) {
        return '';
    }
    $decrypted = openssl_decrypt(base64_decode($encrypted_api_key), 'AES-128-CBC', wp_salt(), 0, substr(wp_salt(), 0, 16));
    error_log('Decrypted API Key: ' . $decrypted);  // Add this line for debugging
    return $decrypted;
}

}

new Sums_API_Settings();