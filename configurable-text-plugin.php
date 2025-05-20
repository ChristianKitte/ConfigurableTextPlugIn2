<?php
/*
Plugin Name: Configurable Text Plugin
Plugin URI: https://example.com/configurable-text-plugin
Description: A simple plugin that outputs configurable text via customizable shortcode with detailed font customization, text alignment, and 3D rotation effects (X, Y, Z axis). Features a modern Bootstrap-based interface.
Version: 1.3
Author: ckitte
Author URI: http://URI_Of_The_Plugin_Author
License: GPL2
Text Domain: configurable-text-plugin
Domain Path: /languages
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load plugin text domain
function ctp_load_textdomain()
{
    load_plugin_textdomain('configurable-text-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'ctp_load_textdomain');

// Add settings page to the WordPress admin menu
function ctp_add_settings_page()
{
    add_options_page(
        __('Configurable Text Settings', 'configurable-text-plugin'),
        __('Configurable Text', 'configurable-text-plugin'),
        'manage_options',
        'configurable-text-settings',
        'ctp_render_settings_page'
    );
}

add_action('admin_menu', 'ctp_add_settings_page');

// Add CSS and JS for admin settings page
function ctp_admin_styles()
{
    $screen = get_current_screen();
    if ($screen && $screen->id === 'settings_page_configurable-text-settings') {
        // Add Bootstrap CSS and JS from CDN
        ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <style>
            /* Custom styles to integrate Bootstrap with WordPress admin */
            #wpbody-content {
                padding-right: 20px;
            }

            .wrap {
                width: 100%;
            }

            .ctp-instance {
                margin-bottom: 20px;
            }

            .card {
                max-width: 100%;
            }

            .ctp-instance .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .ctp-instance h3 {
                margin: 0;
                font-size: 1.2rem;
            }

            .ctp-field {
                margin-bottom: 1rem;
            }

            .ctp-field label {
                font-weight: 500;
                margin-bottom: 0.25rem;
                display: block;
            }

            .ctp-field .form-text {
                font-size: 0.875rem;
                color: #6c757d;
            }

            .ctp-remove-instance {
                color: #dc3545;
                text-decoration: none;
            }

            .ctp-remove-instance:hover {
                color: #b02a37;
                text-decoration: underline;
            }

            .ctp-add-new {
                margin: 20px 0;
            }

            /* Fix for WordPress admin compatibility */
            .form-control, .form-select {
                max-width: 100%;
                display: block;
                width: 100%;
            }

            /* Override WordPress admin styles */
            #wpbody-content .wrap h1 {
                margin-bottom: 1.5rem;
            }

            /* Make the form more compact */
            .card-body {
                padding: 1rem;
            }

            /* Responsive grid for form fields */
            @media (min-width: 768px) {
                .ctp-form-row {
                    display: flex;
                    flex-wrap: wrap;
                    margin-right: -0.5rem;
                    margin-left: -0.5rem;
                }

                .ctp-form-col {
                    flex: 0 0 50%;
                    max-width: 50%;
                    padding-right: 0.5rem;
                    padding-left: 0.5rem;
                }
            }
        </style>
        <?php
    }
}

add_action('admin_head', 'ctp_admin_styles');

// Add CSS for rotation effect
function ctp_frontend_styles()
{
    $instances = get_option('ctp_instances', array());
    if (empty($instances)) return;

    echo '<style>
    .configurable-text {
        width: 100%;
        margin: 0 auto;
        display: block;
    }

    .ctp-align-left {
        text-align: left;
    }

    .ctp-align-center {
        text-align: center;
    }

    .ctp-align-right {
        text-align: right;
    }';
    foreach ($instances as $id => $instance) {
        $rotation_speed = isset($instance['rotation_speed']) ? intval($instance['rotation_speed']) : 0;
        if ($rotation_speed > 0) {
            $rotation_axis = isset($instance['rotation_axis']) ? $instance['rotation_axis'] : 'x';
            $rotate_function = 'rotate' . strtoupper($rotation_axis);

            echo '@keyframes ctp-rotate-' . esc_attr($id) . ' {
                from { transform: ' . $rotate_function . '(0deg); }
                to { transform: ' . $rotate_function . '(360deg); }
            }
            .ctp-text-' . esc_attr($id) . ' {
                animation: ctp-rotate-' . esc_attr($id) . ' ' . (60 / $rotation_speed) . 's linear infinite;
            }';
        }
    }
    echo '</style>';
}

add_action('wp_head', 'ctp_frontend_styles');

// Render the settings page
function ctp_render_settings_page()
{
    $instances = get_option('ctp_instances', array());
    $success_message = '';

    // Handle save single instance
    if (isset($_POST['ctp_save_instance']) && check_admin_referer('ctp_instance_nonce', 'ctp_instance_nonce')) {
        $id = sanitize_text_field($_POST['instance_id']);

        if (isset($_POST['ctp_instance']) && is_array($_POST['ctp_instance'])) {
            $instance = $_POST['ctp_instance'][$id];

            $shortcode_name = sanitize_title($instance['shortcode_name']);
            if (empty($shortcode_name)) {
                $shortcode_name = 'configurable_text_' . $id;
            }

            $instances[$id] = array(
                'name' => sanitize_text_field($instance['name']),
                'text' => isset($instance['render_html']) && $instance['render_html'] ? 
                    wp_kses_post($instance['text']) : 
                    (isset($instance['preserve_formatting']) && $instance['preserve_formatting'] ? 
                        sanitize_textarea_field($instance['text']) : 
                        sanitize_text_field($instance['text'])),
                'shortcode_name' => $shortcode_name,
                'rotation_speed' => intval($instance['rotation_speed']),
                'rotation_axis' => sanitize_text_field($instance['rotation_axis']),
                'text_align' => sanitize_text_field($instance['text_align']),
                'font_family' => sanitize_text_field($instance['font_family']),
                'font_size' => sanitize_text_field($instance['font_size']),
                'font_weight' => sanitize_text_field($instance['font_weight']),
                'font_style' => sanitize_text_field($instance['font_style']),
                'text_color' => sanitize_text_field($instance['text_color']),
                'line_height' => sanitize_text_field($instance['line_height']),
                'preserve_formatting' => isset($instance['preserve_formatting']) ? 1 : 0,
                'render_html' => isset($instance['render_html']) ? 1 : 0
            );

            update_option('ctp_instances', $instances);
            $success_message = sprintf(esc_html__('Instance "%s" saved.', 'configurable-text-plugin'), $instance['name']);

            // Re-register shortcodes
            ctp_register_shortcodes();
        }
    }

    // Handle delete single instance
    if (isset($_POST['ctp_delete_instance']) && check_admin_referer('ctp_instance_nonce', 'ctp_instance_nonce')) {
        $id = sanitize_text_field($_POST['instance_id']);

        if (isset($instances[$id])) {
            $instance_name = $instances[$id]['name'];
            unset($instances[$id]);
            update_option('ctp_instances', $instances);
            $success_message = sprintf(esc_html__('Instance "%s" deleted.', 'configurable-text-plugin'), $instance_name);

            // Re-register shortcodes
            ctp_register_shortcodes();
        }
    }

    // Handle save all instances (legacy support)
    if (isset($_POST['ctp_save_instances']) && check_admin_referer('ctp_save_instances_nonce', 'ctp_nonce')) {
        $instances = array();

        if (isset($_POST['ctp_instance']) && is_array($_POST['ctp_instance'])) {
            foreach ($_POST['ctp_instance'] as $id => $instance) {
                $shortcode_name = sanitize_title($instance['shortcode_name']);
                if (empty($shortcode_name)) {
                    $shortcode_name = 'configurable_text_' . $id;
                }

                $instances[$id] = array(
                    'name' => sanitize_text_field($instance['name']),
                    'text' => isset($instance['render_html']) && $instance['render_html'] ? 
                        wp_kses_post($instance['text']) : 
                        (isset($instance['preserve_formatting']) && $instance['preserve_formatting'] ? 
                            sanitize_textarea_field($instance['text']) : 
                            sanitize_text_field($instance['text'])),
                    'shortcode_name' => $shortcode_name,
                    'rotation_speed' => intval($instance['rotation_speed']),
                    'rotation_axis' => sanitize_text_field($instance['rotation_axis']),
                    'text_align' => sanitize_text_field($instance['text_align']),
                    'font_family' => sanitize_text_field($instance['font_family']),
                    'font_size' => sanitize_text_field($instance['font_size']),
                    'font_weight' => sanitize_text_field($instance['font_weight']),
                    'font_style' => sanitize_text_field($instance['font_style']),
                    'text_color' => sanitize_text_field($instance['text_color']),
                    'line_height' => sanitize_text_field($instance['line_height']),
                    'preserve_formatting' => isset($instance['preserve_formatting']) ? 1 : 0,
                    'render_html' => isset($instance['render_html']) ? 1 : 0
                );
            }
        }

        update_option('ctp_instances', $instances);
        $success_message = esc_html__('All settings saved.', 'configurable-text-plugin');

        // Re-register shortcodes
        ctp_register_shortcodes();
    }

    // Display success message if any (only for non-AJAX requests)
    if (!empty($success_message) && !wp_doing_ajax()) {
        echo '<div class="notice notice-success is-dismissible"><p>' . $success_message . '</p></div>';
    }

    $instances = get_option('ctp_instances', array());

    // If no instances exist, create a default one
    if (empty($instances)) {
        $default_text = get_option('ctp_text_option', __('Default text', 'configurable-text-plugin'));
        $instances = array(
            '1' => array(
                'name' => __('Default Instance', 'configurable-text-plugin'),
                'text' => $default_text,
                'shortcode_name' => 'configurable_text',
                'rotation_speed' => 0,
                'rotation_axis' => 'x',
                'text_align' => 'left',
                'font_family' => 'Arial, sans-serif',
                'font_size' => '16px',
                'font_weight' => 'normal',
                'font_style' => 'normal',
                'text_color' => '#000000',
                'line_height' => '1.5',
                'preserve_formatting' => 0,
                'render_html' => 0
            )
        );
        update_option('ctp_instances', $instances);
    }

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <div class="ctp-add-new my-4">
            <button type="button" class="btn btn-primary"
                    id="ctp-add-instance">
                <i class="dashicons dashicons-plus-alt" style="vertical-align: text-bottom;"></i>
                <?php esc_html_e('Add New Text Instance', 'configurable-text-plugin'); ?>
            </button>
            <div class="form-text mt-2">
                <?php esc_html_e('After adding a new instance, remember to save it using the "Save Instance" button.', 'configurable-text-plugin'); ?>
            </div>
        </div>

        <!-- Tab navigation -->
        <ul class="nav nav-tabs mb-3" id="ctp-instance-tabs" role="tablist">
            <?php $first_tab = true; ?>
            <?php foreach ($instances as $id => $instance) : ?>
                <?php 
                $instance_name = isset($instance['name']) && !empty($instance['name']) ? $instance['name'] : sprintf(esc_html__('Text Instance #%s', 'configurable-text-plugin'), esc_html($id));
                ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo $first_tab ? 'active' : ''; ?>" 
                            id="tab-<?php echo esc_attr($id); ?>-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#tab-<?php echo esc_attr($id); ?>" 
                            type="button" 
                            role="tab" 
                            aria-controls="tab-<?php echo esc_attr($id); ?>" 
                            aria-selected="<?php echo $first_tab ? 'true' : 'false'; ?>">
                        <?php echo esc_html($instance_name); ?>
                    </button>
                </li>
                <?php $first_tab = false; ?>
            <?php endforeach; ?>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="ctp-instances">
            <?php $first_tab = true; ?>
            <?php foreach ($instances as $id => $instance) : ?>
                <div class="tab-pane fade <?php echo $first_tab ? 'show active' : ''; ?>" 
                     id="tab-<?php echo esc_attr($id); ?>" 
                     role="tabpanel" 
                     aria-labelledby="tab-<?php echo esc_attr($id); ?>-tab">

                    <div class="ctp-instance card mb-4" id="ctp-instance-<?php echo esc_attr($id); ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-end mb-3">
                                <form method="post" action="" class="needs-validation" novalidate>
                                    <?php wp_nonce_field('ctp_instance_nonce', 'ctp_instance_nonce'); ?>
                                    <input type="hidden" name="instance_id" value="<?php echo esc_attr($id); ?>">
                                    <button type="submit" name="ctp_save_instance" class="btn btn-sm btn-success me-2" id="save-instance-<?php echo esc_attr($id); ?>">
                                        <i class="dashicons dashicons-saved" style="vertical-align: text-bottom;"></i>
                                        <?php esc_html_e('Save Instance', 'configurable-text-plugin'); ?>
                                    </button>
                                </form>
                                <form method="post" action="" class="d-inline">
                                    <?php wp_nonce_field('ctp_instance_nonce', 'ctp_instance_nonce'); ?>
                                    <input type="hidden" name="instance_id" value="<?php echo esc_attr($id); ?>">
                                    <button type="submit" name="ctp_delete_instance" class="btn btn-sm btn-outline-danger">
                                        <?php esc_html_e('Delete Instance', 'configurable-text-plugin'); ?>
                                    </button>
                                </form>
                            </div>
                        <form method="post" action="" class="needs-validation" novalidate>
                            <?php wp_nonce_field('ctp_instance_nonce', 'ctp_instance_nonce'); ?>
                            <input type="hidden" name="instance_id" value="<?php echo esc_attr($id); ?>">

                            <div class="ctp-field mb-3">
                                <label for="ctp-instance-<?php echo esc_attr($id); ?>-name" class="form-label"><?php esc_html_e('Instance Name', 'configurable-text-plugin'); ?></label>
                                <input type="text" id="ctp-instance-<?php echo esc_attr($id); ?>-name"
                                       class="form-control"
                                       name="ctp_instance[<?php echo esc_attr($id); ?>][name]"
                                       value="<?php echo esc_attr(isset($instance['name']) ? $instance['name'] : sprintf(__('Instance %s', 'configurable-text-plugin'), $id)); ?>" />
                                <div class="form-text"><?php esc_html_e('Enter a name to identify this instance in the backend.', 'configurable-text-plugin'); ?></div>
                            </div>

                            <div class="ctp-field mb-4">
                                <label for="ctp-instance-<?php echo esc_attr($id); ?>-text" class="form-label"><?php esc_html_e('Text to Display', 'configurable-text-plugin'); ?></label>
                                <textarea id="ctp-instance-<?php echo esc_attr($id); ?>-text"
                                          class="form-control"
                                          name="ctp_instance[<?php echo esc_attr($id); ?>][text]"
                                          rows="3"><?php echo esc_textarea($instance['text']); ?></textarea>
                                <div class="form-text"><?php esc_html_e('Enter the text you want to display with this shortcode.', 'configurable-text-plugin'); ?></div>
                            </div>

                            <div class="ctp-form-row mb-4">
                                <div class="ctp-form-col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="ctp-instance-<?php echo esc_attr($id); ?>-preserve-formatting" 
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][preserve_formatting]" 
                                               value="1" <?php checked(isset($instance['preserve_formatting']) && $instance['preserve_formatting'], true); ?>>
                                        <label class="form-check-label" for="ctp-instance-<?php echo esc_attr($id); ?>-preserve-formatting">
                                            <?php esc_html_e('Preserve Formatting', 'configurable-text-plugin'); ?>
                                        </label>
                                        <div class="form-text"><?php esc_html_e('Keep line breaks and spacing exactly as entered.', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                                <div class="ctp-form-col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="ctp-instance-<?php echo esc_attr($id); ?>-render-html" 
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][render_html]" 
                                               value="1" <?php checked(isset($instance['render_html']) && $instance['render_html'], true); ?>>
                                        <label class="form-check-label" for="ctp-instance-<?php echo esc_attr($id); ?>-render-html">
                                            <?php esc_html_e('Render HTML Content', 'configurable-text-plugin'); ?>
                                        </label>
                                        <div class="form-text"><?php esc_html_e('Allow HTML tags in the text (use with caution).', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="ctp-form-row">
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-shortcode" class="form-label"><?php esc_html_e('Shortcode Name', 'configurable-text-plugin'); ?></label>
                                        <input type="text" id="ctp-instance-<?php echo esc_attr($id); ?>-shortcode"
                                               class="form-control"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][shortcode_name]"
                                               value="<?php echo esc_attr($instance['shortcode_name']); ?>"/>
                                        <div class="form-text">
                                            <?php esc_html_e('Customize the shortcode name (letters, numbers, and underscores only).', 'configurable-text-plugin'); ?>
                                            <br>
                                            <span class="badge bg-secondary mt-1"><?php echo sprintf(esc_html__('Current shortcode: [%s]', 'configurable-text-plugin'), esc_html($instance['shortcode_name'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-text-align" class="form-label"><?php esc_html_e('Text Alignment', 'configurable-text-plugin'); ?></label>
                                        <select id="ctp-instance-<?php echo esc_attr($id); ?>-text-align"
                                               class="form-select"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][text_align]">
                                            <option value="left" <?php selected(isset($instance['text_align']) ? $instance['text_align'] : 'left', 'left'); ?>><?php esc_html_e('Left', 'configurable-text-plugin'); ?></option>
                                            <option value="center" <?php selected(isset($instance['text_align']) ? $instance['text_align'] : 'left', 'center'); ?>><?php esc_html_e('Center', 'configurable-text-plugin'); ?></option>
                                            <option value="right" <?php selected(isset($instance['text_align']) ? $instance['text_align'] : 'left', 'right'); ?>><?php esc_html_e('Right', 'configurable-text-plugin'); ?></option>
                                        </select>
                                        <div class="form-text"><?php esc_html_e('Choose the text alignment (left, center, or right).', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="ctp-form-row">
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-rotation" class="form-label"><?php esc_html_e('Rotation Speed (RPM)', 'configurable-text-plugin'); ?></label>
                                        <input type="number" id="ctp-instance-<?php echo esc_attr($id); ?>-rotation"
                                               class="form-control"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][rotation_speed]"
                                               value="<?php echo esc_attr($instance['rotation_speed']); ?>" min="0" step="1"/>
                                        <div class="form-text"><?php esc_html_e('Set the rotation speed in rotations per minute (0 = no rotation).', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-rotation-axis" class="form-label"><?php esc_html_e('Rotation Axis', 'configurable-text-plugin'); ?></label>
                                        <select id="ctp-instance-<?php echo esc_attr($id); ?>-rotation-axis"
                                               class="form-select"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][rotation_axis]">
                                            <option value="x" <?php selected(isset($instance['rotation_axis']) ? $instance['rotation_axis'] : 'x', 'x'); ?>><?php esc_html_e('X-Axis', 'configurable-text-plugin'); ?></option>
                                            <option value="y" <?php selected(isset($instance['rotation_axis']) ? $instance['rotation_axis'] : 'x', 'y'); ?>><?php esc_html_e('Y-Axis', 'configurable-text-plugin'); ?></option>
                                            <option value="z" <?php selected(isset($instance['rotation_axis']) ? $instance['rotation_axis'] : 'x', 'z'); ?>><?php esc_html_e('Z-Axis', 'configurable-text-plugin'); ?></option>
                                        </select>
                                        <div class="form-text"><?php esc_html_e('Choose the axis around which the text will rotate.', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-3">
                                <h5 class="border-bottom pb-2"><?php esc_html_e('Font Settings', 'configurable-text-plugin'); ?></h5>
                            </div>

                            <div class="ctp-form-row">
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-font-family" class="form-label"><?php esc_html_e('Font Family', 'configurable-text-plugin'); ?></label>
                                        <select id="ctp-instance-<?php echo esc_attr($id); ?>-font-family"
                                               class="form-select"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][font_family]">
                                            <option value="Arial, sans-serif" <?php selected(isset($instance['font_family']) ? $instance['font_family'] : 'Arial, sans-serif', 'Arial, sans-serif'); ?>><?php esc_html_e('Arial', 'configurable-text-plugin'); ?></option>
                                            <option value="Helvetica, sans-serif" <?php selected(isset($instance['font_family']) ? $instance['font_family'] : 'Arial, sans-serif', 'Helvetica, sans-serif'); ?>><?php esc_html_e('Helvetica', 'configurable-text-plugin'); ?></option>
                                            <option value="Georgia, serif" <?php selected(isset($instance['font_family']) ? $instance['font_family'] : 'Arial, sans-serif', 'Georgia, serif'); ?>><?php esc_html_e('Georgia', 'configurable-text-plugin'); ?></option>
                                            <option value="Times New Roman, serif" <?php selected(isset($instance['font_family']) ? $instance['font_family'] : 'Arial, sans-serif', 'Times New Roman, serif'); ?>><?php esc_html_e('Times New Roman', 'configurable-text-plugin'); ?></option>
                                            <option value="Courier New, monospace" <?php selected(isset($instance['font_family']) ? $instance['font_family'] : 'Arial, sans-serif', 'Courier New, monospace'); ?>><?php esc_html_e('Courier New', 'configurable-text-plugin'); ?></option>
                                            <option value="Verdana, sans-serif" <?php selected(isset($instance['font_family']) ? $instance['font_family'] : 'Arial, sans-serif', 'Verdana, sans-serif'); ?>><?php esc_html_e('Verdana', 'configurable-text-plugin'); ?></option>
                                            <option value="Tahoma, sans-serif" <?php selected(isset($instance['font_family']) ? $instance['font_family'] : 'Arial, sans-serif', 'Tahoma, sans-serif'); ?>><?php esc_html_e('Tahoma', 'configurable-text-plugin'); ?></option>
                                            <option value="Trebuchet MS, sans-serif" <?php selected(isset($instance['font_family']) ? $instance['font_family'] : 'Arial, sans-serif', 'Trebuchet MS, sans-serif'); ?>><?php esc_html_e('Trebuchet MS', 'configurable-text-plugin'); ?></option>
                                        </select>
                                        <div class="form-text"><?php esc_html_e('Select the font family for the text.', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-font-size" class="form-label"><?php esc_html_e('Font Size', 'configurable-text-plugin'); ?></label>
                                        <input type="text" id="ctp-instance-<?php echo esc_attr($id); ?>-font-size"
                                               class="form-control"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][font_size]"
                                               value="<?php echo esc_attr(isset($instance['font_size']) ? $instance['font_size'] : '16px'); ?>" />
                                        <div class="form-text"><?php esc_html_e('Enter the font size with unit (e.g., 16px, 1.2em, 90%).', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="ctp-form-row">
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-font-weight" class="form-label"><?php esc_html_e('Font Weight', 'configurable-text-plugin'); ?></label>
                                        <select id="ctp-instance-<?php echo esc_attr($id); ?>-font-weight"
                                               class="form-select"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][font_weight]">
                                            <option value="normal" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', 'normal'); ?>><?php esc_html_e('Normal', 'configurable-text-plugin'); ?></option>
                                            <option value="bold" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', 'bold'); ?>><?php esc_html_e('Bold', 'configurable-text-plugin'); ?></option>
                                            <option value="lighter" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', 'lighter'); ?>><?php esc_html_e('Lighter', 'configurable-text-plugin'); ?></option>
                                            <option value="bolder" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', 'bolder'); ?>><?php esc_html_e('Bolder', 'configurable-text-plugin'); ?></option>
                                            <option value="100" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '100'); ?>><?php esc_html_e('100', 'configurable-text-plugin'); ?></option>
                                            <option value="200" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '200'); ?>><?php esc_html_e('200', 'configurable-text-plugin'); ?></option>
                                            <option value="300" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '300'); ?>><?php esc_html_e('300', 'configurable-text-plugin'); ?></option>
                                            <option value="400" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '400'); ?>><?php esc_html_e('400 (Normal)', 'configurable-text-plugin'); ?></option>
                                            <option value="500" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '500'); ?>><?php esc_html_e('500', 'configurable-text-plugin'); ?></option>
                                            <option value="600" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '600'); ?>><?php esc_html_e('600', 'configurable-text-plugin'); ?></option>
                                            <option value="700" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '700'); ?>><?php esc_html_e('700 (Bold)', 'configurable-text-plugin'); ?></option>
                                            <option value="800" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '800'); ?>><?php esc_html_e('800', 'configurable-text-plugin'); ?></option>
                                            <option value="900" <?php selected(isset($instance['font_weight']) ? $instance['font_weight'] : 'normal', '900'); ?>><?php esc_html_e('900', 'configurable-text-plugin'); ?></option>
                                        </select>
                                        <div class="form-text"><?php esc_html_e('Select the font weight for the text.', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-font-style" class="form-label"><?php esc_html_e('Font Style', 'configurable-text-plugin'); ?></label>
                                        <select id="ctp-instance-<?php echo esc_attr($id); ?>-font-style"
                                               class="form-select"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][font_style]">
                                            <option value="normal" <?php selected(isset($instance['font_style']) ? $instance['font_style'] : 'normal', 'normal'); ?>><?php esc_html_e('Normal', 'configurable-text-plugin'); ?></option>
                                            <option value="italic" <?php selected(isset($instance['font_style']) ? $instance['font_style'] : 'normal', 'italic'); ?>><?php esc_html_e('Italic', 'configurable-text-plugin'); ?></option>
                                            <option value="oblique" <?php selected(isset($instance['font_style']) ? $instance['font_style'] : 'normal', 'oblique'); ?>><?php esc_html_e('Oblique', 'configurable-text-plugin'); ?></option>
                                        </select>
                                        <div class="form-text"><?php esc_html_e('Select the font style for the text.', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="ctp-form-row">
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-text-color" class="form-label"><?php esc_html_e('Text Color', 'configurable-text-plugin'); ?></label>
                                        <input type="color" id="ctp-instance-<?php echo esc_attr($id); ?>-text-color"
                                               class="form-control form-control-color"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][text_color]"
                                               value="<?php echo esc_attr(isset($instance['text_color']) ? $instance['text_color'] : '#000000'); ?>" />
                                        <div class="form-text"><?php esc_html_e('Select the color for the text.', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                                <div class="ctp-form-col">
                                    <div class="ctp-field mb-3">
                                        <label for="ctp-instance-<?php echo esc_attr($id); ?>-line-height" class="form-label"><?php esc_html_e('Line Height', 'configurable-text-plugin'); ?></label>
                                        <input type="text" id="ctp-instance-<?php echo esc_attr($id); ?>-line-height"
                                               class="form-control"
                                               name="ctp_instance[<?php echo esc_attr($id); ?>][line_height]"
                                               value="<?php echo esc_attr(isset($instance['line_height']) ? $instance['line_height'] : '1.5'); ?>" />
                                        <div class="form-text"><?php esc_html_e('Enter the line height (e.g., 1.5, 2, 150%).', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                </div>
                <?php $first_tab = false; ?>
                <?php endforeach; ?>
            </div>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // Track form changes and highlight save button
            function setupFormChangeTracking(formSelector) {
                const forms = $(formSelector);

                forms.each(function() {
                    const form = $(this);
                    const saveButton = form.find('button[name="ctp_save_instance"]');
                    const formId = form.find('input[name="instance_id"]').val();

                    // Store initial form values
                    let initialValues = form.serialize();
                    form.data('initial-values', initialValues);

                    // Add change event listener to all form inputs
                    form.find('input, textarea, select').on('input change', function() {
                        const currentValues = form.serialize();
                        const storedInitialValues = form.data('initial-values') || initialValues;

                        // Compare current values with initial values
                        if (currentValues !== storedInitialValues) {
                            // Highlight save button
                            saveButton.addClass('btn-warning').removeClass('btn-success');
                        } else {
                            // Remove highlight
                            saveButton.addClass('btn-success').removeClass('btn-warning');
                        }
                    });

                    // Reset tracking after save
                    form.on('submit', function() {
                        setTimeout(function() {
                            saveButton.addClass('btn-success').removeClass('btn-warning');
                        }, 100);
                    });
                });
            }

            // Setup form change tracking for existing forms
            setupFormChangeTracking('.ctp-instance form.needs-validation');

            // Setup form change tracking for new forms (event delegation)
            $(document).on('DOMNodeInserted', '.ctp-instance', function() {
                const newForm = $(this).find('form.needs-validation');
                if (newForm.length && !newForm.data('change-tracking-setup')) {
                    setupFormChangeTracking(newForm);
                    newForm.data('change-tracking-setup', true);
                }
            });

            // Function to show success message
            function showSuccessMessage(message) {
                // Create success message element
                var successMessage = $('<div class="notice notice-success is-dismissible"><p>' + message + '</p></div>');

                // Remove any existing success messages
                $('.notice').remove();

                // Add the success message at the top of the page
                $('.wrap > h1').after(successMessage);

                // Add dismiss button
                successMessage.append('<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>');

                // Handle dismiss button click
                successMessage.find('.notice-dismiss').on('click', function() {
                    successMessage.fadeOut(300, function() { $(this).remove(); });
                });

                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    successMessage.fadeOut(300, function() { $(this).remove(); });
                }, 5000);
            }

            // Handle save form submission via AJAX using event delegation
            $(document).on('submit', '.ctp-instance form', function(e) {
                // Only intercept save instance forms, not delete forms
                if ($(this).find('button[name="ctp_save_instance"]').length > 0) {
                    e.preventDefault();

                    var form = $(this);
                    var instanceId = form.find('input[name="instance_id"]').val();

                    // Get the main form with all the instance data (the second form in the instance)
                    var dataForm = $('#ctp-instance-' + instanceId).find('form.needs-validation').eq(1);

                    // Combine data from both forms
                    var formData = form.serialize() + '&' + dataForm.serialize();

                    // Add action for WordPress AJAX
                    formData += '&action=ctp_save_instance';

                    // Add nonce with the correct parameter name
                    var nonce = form.find('input[name="ctp_instance_nonce"]').val();
                    formData += '&nonce=' + nonce;

                    // Send AJAX request
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                showSuccessMessage(response.data.message);

                                // Update the tab label with the new instance name
                                $('#tab-' + response.data.instance_id + '-tab').text(response.data.instance_name);

                                // Reset form change tracking
                                const form = $('#ctp-instance-' + response.data.instance_id).find('form.needs-validation');
                                const saveButton = form.find('button[name="ctp_save_instance"]');
                                saveButton.addClass('btn-success').removeClass('btn-warning');

                                // Update initial form values for change detection
                                setTimeout(function() {
                                    const initialValues = form.serialize();
                                    form.data('initial-values', initialValues);
                                }, 200);
                            } else {
                                showSuccessMessage(response.data.message);
                                console.error('Error saving instance:', response);
                            }
                        },
                        error: function(xhr, status, error) {
                            showSuccessMessage('<?php echo esc_js(__('An error occurred while saving.', 'configurable-text-plugin')); ?>');
                            console.error('AJAX Error:', status, error);
                            console.log('Response:', xhr.responseText);
                            console.log('Status Code:', xhr.status);
                            console.log('Form Data:', formData);

                            // Try to parse the response if possible
                            try {
                                var jsonResponse = JSON.parse(xhr.responseText);
                                console.log('Parsed Response:', jsonResponse);
                            } catch (e) {
                                console.log('Could not parse response as JSON');
                            }
                        }
                    });
                }
            });

            // Handle delete form submission via AJAX using event delegation
            $(document).on('submit', '.ctp-instance .d-flex form', function(e) {
                // Only intercept delete instance forms
                if ($(this).find('button[name="ctp_delete_instance"]').length > 0) {
                    e.preventDefault();

                    // Confirm deletion
                    if (!confirm('<?php echo esc_js(__('Are you sure you want to delete this instance?', 'configurable-text-plugin')); ?>')) {
                        return false;
                    }

                    var form = $(this);
                    var formData = form.serialize();
                    var instanceId = form.find('input[name="instance_id"]').val();

                    // Add action for WordPress AJAX
                    formData += '&action=ctp_delete_instance';

                    // Add nonce with the correct parameter name
                    var nonce = form.find('input[name="ctp_instance_nonce"]').val();
                    formData += '&nonce=' + nonce;

                    // Send AJAX request
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                showSuccessMessage(response.data.message);

                                // Remove the tab and tab content
                                $('#tab-' + response.data.deleted_id + '-tab').parent().remove();
                                $('#tab-' + response.data.deleted_id).remove();

                                // Activate the first tab if available
                                if ($('#ctp-instance-tabs .nav-link').length > 0) {
                                    $('#ctp-instance-tabs .nav-link:first').tab('show');
                                }
                            } else {
                                showSuccessMessage(response.data.message);
                                console.error('Error deleting instance:', response);
                            }
                        },
                        error: function(xhr, status, error) {
                            showSuccessMessage('<?php echo esc_js(__('An error occurred while deleting.', 'configurable-text-plugin')); ?>');
                            console.error('AJAX Error:', status, error);
                            console.log('Response:', xhr.responseText);
                            console.log('Status Code:', xhr.status);
                            console.log('Form Data:', formData);

                            // Try to parse the response if possible
                            try {
                                var jsonResponse = JSON.parse(xhr.responseText);
                                console.log('Parsed Response:', jsonResponse);
                            } catch (e) {
                                console.log('Could not parse response as JSON');
                            }
                        }
                    });
                }
            });

            // Add new instance
            $('#ctp-add-instance').on('click', function () {
                var instanceCount = $('.ctp-instance').length;
                var newId = instanceCount + 1;

                // Find an unused ID
                while ($('#ctp-instance-' + newId).length > 0) {
                    newId++;
                }

                // Create new tab
                var tabTemplate = `
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="tab-${newId}-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#tab-${newId}" 
                            type="button" 
                            role="tab" 
                            aria-controls="tab-${newId}" 
                            aria-selected="false">
                        <?php echo esc_html__('Instance', 'configurable-text-plugin'); ?> ${newId}
                    </button>
                </li>
                `;

                $('#ctp-instance-tabs').append(tabTemplate);

                // Create tab content
                var template = `
                <div class="tab-pane fade" 
                     id="tab-${newId}" 
                     role="tabpanel" 
                     aria-labelledby="tab-${newId}-tab">
                <div class="ctp-instance card mb-4" id="ctp-instance-${newId}">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <form method="post" action="" class="needs-validation" novalidate>
                                <?php wp_nonce_field('ctp_instance_nonce', 'ctp_instance_nonce'); ?>
                                <input type="hidden" name="instance_id" value="${newId}">
                                <button type="submit" name="ctp_save_instance" class="btn btn-sm btn-success me-2" id="save-instance-${newId}">
                                    <i class="dashicons dashicons-saved" style="vertical-align: text-bottom;"></i>
                                    <?php esc_html_e('Save Instance', 'configurable-text-plugin'); ?>
                                </button>
                            </form>
                            <form method="post" action="" class="d-inline">
                                <?php wp_nonce_field('ctp_instance_nonce', 'ctp_instance_nonce'); ?>
                                <input type="hidden" name="instance_id" value="${newId}">
                                <button type="submit" name="ctp_delete_instance" class="btn btn-sm btn-outline-danger">
                                    <?php esc_html_e('Delete Instance', 'configurable-text-plugin'); ?>
                                </button>
                            </form>
                        </div>
                        <form method="post" action="" class="needs-validation" novalidate>
                            <?php wp_nonce_field('ctp_instance_nonce', 'ctp_instance_nonce'); ?>
                            <input type="hidden" name="instance_id" value="${newId}">

                            <div class="ctp-field mb-3">
                                <label for="ctp-instance-${newId}-name" class="form-label"><?php esc_html_e('Instance Name', 'configurable-text-plugin'); ?></label>
                                <input type="text" id="ctp-instance-${newId}-name" class="form-control" name="ctp_instance[${newId}][name]" value="<?php echo esc_html__('Instance', 'configurable-text-plugin'); ?> ${newId}" />
                                <div class="form-text"><?php esc_html_e('Enter a name to identify this instance in the backend.', 'configurable-text-plugin'); ?></div>
                            </div>

                            <div class="ctp-field mb-4">
                                <label for="ctp-instance-${newId}-text" class="form-label"><?php esc_html_e('Text to Display', 'configurable-text-plugin'); ?></label>
                                <textarea id="ctp-instance-${newId}-text" class="form-control" name="ctp_instance[${newId}][text]" rows="3"><?php echo esc_html__('Default text', 'configurable-text-plugin'); ?></textarea>
                                <div class="form-text"><?php esc_html_e('Enter the text you want to display with this shortcode.', 'configurable-text-plugin'); ?></div>
                            </div>

                            <div class="ctp-form-row mb-4">
                                <div class="ctp-form-col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="ctp-instance-${newId}-preserve-formatting" 
                                               name="ctp_instance[${newId}][preserve_formatting]" 
                                               value="1">
                                        <label class="form-check-label" for="ctp-instance-${newId}-preserve-formatting">
                                            <?php esc_html_e('Preserve Formatting', 'configurable-text-plugin'); ?>
                                        </label>
                                        <div class="form-text"><?php esc_html_e('Keep line breaks and spacing exactly as entered.', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                                <div class="ctp-form-col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="ctp-instance-${newId}-render-html" 
                                               name="ctp_instance[${newId}][render_html]" 
                                               value="1">
                                        <label class="form-check-label" for="ctp-instance-${newId}-render-html">
                                            <?php esc_html_e('Render HTML Content', 'configurable-text-plugin'); ?>
                                        </label>
                                        <div class="form-text"><?php esc_html_e('Allow HTML tags in the text (use with caution).', 'configurable-text-plugin'); ?></div>
                                    </div>
                                </div>
                            </div>

                        <div class="ctp-form-row">
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-shortcode" class="form-label"><?php esc_html_e('Shortcode Name', 'configurable-text-plugin'); ?></label>
                                    <input type="text" id="ctp-instance-${newId}-shortcode" class="form-control" name="ctp_instance[${newId}][shortcode_name]" value="configurable_text_${newId}" />
                                    <div class="form-text">
                                        <?php esc_html_e('Customize the shortcode name (letters, numbers, and underscores only).', 'configurable-text-plugin'); ?>
                                        <br>
                                        <span class="badge bg-secondary mt-1"><?php esc_html_e('Current shortcode: [configurable_text_', 'configurable-text-plugin'); ?>${newId}]</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-text-align" class="form-label"><?php esc_html_e('Text Alignment', 'configurable-text-plugin'); ?></label>
                                    <select id="ctp-instance-${newId}-text-align" class="form-select" name="ctp_instance[${newId}][text_align]">
                                        <option value="left"><?php esc_html_e('Left', 'configurable-text-plugin'); ?></option>
                                        <option value="center"><?php esc_html_e('Center', 'configurable-text-plugin'); ?></option>
                                        <option value="right"><?php esc_html_e('Right', 'configurable-text-plugin'); ?></option>
                                    </select>
                                    <div class="form-text"><?php esc_html_e('Choose the text alignment (left, center, or right).', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="ctp-form-row">
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-rotation" class="form-label"><?php esc_html_e('Rotation Speed (RPM)', 'configurable-text-plugin'); ?></label>
                                    <input type="number" id="ctp-instance-${newId}-rotation" class="form-control" name="ctp_instance[${newId}][rotation_speed]" value="0" min="0" step="1" />
                                    <div class="form-text"><?php esc_html_e('Set the rotation speed in rotations per minute (0 = no rotation).', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-rotation-axis" class="form-label"><?php esc_html_e('Rotation Axis', 'configurable-text-plugin'); ?></label>
                                    <select id="ctp-instance-${newId}-rotation-axis" class="form-select" name="ctp_instance[${newId}][rotation_axis]">
                                        <option value="x" selected><?php esc_html_e('X-Axis', 'configurable-text-plugin'); ?></option>
                                        <option value="y"><?php esc_html_e('Y-Axis', 'configurable-text-plugin'); ?></option>
                                        <option value="z"><?php esc_html_e('Z-Axis', 'configurable-text-plugin'); ?></option>
                                    </select>
                                    <div class="form-text"><?php esc_html_e('Choose the axis around which the text will rotate.', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 mb-3">
                            <h5 class="border-bottom pb-2"><?php esc_html_e('Font Settings', 'configurable-text-plugin'); ?></h5>
                        </div>

                        <div class="ctp-form-row">
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-font-family" class="form-label"><?php esc_html_e('Font Family', 'configurable-text-plugin'); ?></label>
                                    <select id="ctp-instance-${newId}-font-family" class="form-select" name="ctp_instance[${newId}][font_family]">
                                        <option value="Arial, sans-serif"><?php esc_html_e('Arial', 'configurable-text-plugin'); ?></option>
                                        <option value="Helvetica, sans-serif"><?php esc_html_e('Helvetica', 'configurable-text-plugin'); ?></option>
                                        <option value="Georgia, serif"><?php esc_html_e('Georgia', 'configurable-text-plugin'); ?></option>
                                        <option value="Times New Roman, serif"><?php esc_html_e('Times New Roman', 'configurable-text-plugin'); ?></option>
                                        <option value="Courier New, monospace"><?php esc_html_e('Courier New', 'configurable-text-plugin'); ?></option>
                                        <option value="Verdana, sans-serif"><?php esc_html_e('Verdana', 'configurable-text-plugin'); ?></option>
                                        <option value="Tahoma, sans-serif"><?php esc_html_e('Tahoma', 'configurable-text-plugin'); ?></option>
                                        <option value="Trebuchet MS, sans-serif"><?php esc_html_e('Trebuchet MS', 'configurable-text-plugin'); ?></option>
                                    </select>
                                    <div class="form-text"><?php esc_html_e('Select the font family for the text.', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-font-size" class="form-label"><?php esc_html_e('Font Size', 'configurable-text-plugin'); ?></label>
                                    <input type="text" id="ctp-instance-${newId}-font-size" class="form-control" name="ctp_instance[${newId}][font_size]" value="16px" />
                                    <div class="form-text"><?php esc_html_e('Enter the font size with unit (e.g., 16px, 1.2em, 90%).', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="ctp-form-row">
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-font-weight" class="form-label"><?php esc_html_e('Font Weight', 'configurable-text-plugin'); ?></label>
                                    <select id="ctp-instance-${newId}-font-weight" class="form-select" name="ctp_instance[${newId}][font_weight]">
                                        <option value="normal"><?php esc_html_e('Normal', 'configurable-text-plugin'); ?></option>
                                        <option value="bold"><?php esc_html_e('Bold', 'configurable-text-plugin'); ?></option>
                                        <option value="lighter"><?php esc_html_e('Lighter', 'configurable-text-plugin'); ?></option>
                                        <option value="bolder"><?php esc_html_e('Bolder', 'configurable-text-plugin'); ?></option>
                                        <option value="100"><?php esc_html_e('100', 'configurable-text-plugin'); ?></option>
                                        <option value="200"><?php esc_html_e('200', 'configurable-text-plugin'); ?></option>
                                        <option value="300"><?php esc_html_e('300', 'configurable-text-plugin'); ?></option>
                                        <option value="400"><?php esc_html_e('400 (Normal)', 'configurable-text-plugin'); ?></option>
                                        <option value="500"><?php esc_html_e('500', 'configurable-text-plugin'); ?></option>
                                        <option value="600"><?php esc_html_e('600', 'configurable-text-plugin'); ?></option>
                                        <option value="700"><?php esc_html_e('700 (Bold)', 'configurable-text-plugin'); ?></option>
                                        <option value="800"><?php esc_html_e('800', 'configurable-text-plugin'); ?></option>
                                        <option value="900"><?php esc_html_e('900', 'configurable-text-plugin'); ?></option>
                                    </select>
                                    <div class="form-text"><?php esc_html_e('Select the font weight for the text.', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-font-style" class="form-label"><?php esc_html_e('Font Style', 'configurable-text-plugin'); ?></label>
                                    <select id="ctp-instance-${newId}-font-style" class="form-select" name="ctp_instance[${newId}][font_style]">
                                        <option value="normal"><?php esc_html_e('Normal', 'configurable-text-plugin'); ?></option>
                                        <option value="italic"><?php esc_html_e('Italic', 'configurable-text-plugin'); ?></option>
                                        <option value="oblique"><?php esc_html_e('Oblique', 'configurable-text-plugin'); ?></option>
                                    </select>
                                    <div class="form-text"><?php esc_html_e('Select the font style for the text.', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="ctp-form-row">
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-text-color" class="form-label"><?php esc_html_e('Text Color', 'configurable-text-plugin'); ?></label>
                                    <input type="color" id="ctp-instance-${newId}-text-color" class="form-control form-control-color" name="ctp_instance[${newId}][text_color]" value="#000000" />
                                    <div class="form-text"><?php esc_html_e('Select the color for the text.', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                            <div class="ctp-form-col">
                                <div class="ctp-field mb-3">
                                    <label for="ctp-instance-${newId}-line-height" class="form-label"><?php esc_html_e('Line Height', 'configurable-text-plugin'); ?></label>
                                    <input type="text" id="ctp-instance-${newId}-line-height" class="form-control" name="ctp_instance[${newId}][line_height]" value="1.5" />
                                    <div class="form-text"><?php esc_html_e('Enter the line height (e.g., 1.5, 2, 150%).', 'configurable-text-plugin'); ?></div>
                                </div>
                            </div>
                        </div>

                        </form>
                    </div>
                </div>
            `;

                $('#ctp-instances').append(template);

                // Activate the new tab
                $(`#tab-${newId}-tab`).tab('show');

                // Initialize form change tracking for the new form
                const newForm = $(`#ctp-instance-${newId}`).find('form.needs-validation');
                setupFormChangeTracking(newForm);
            });
        });
    </script>
    <?php
}

// Register all shortcodes based on saved instances
function ctp_register_shortcodes()
{
    // Remove any existing shortcodes (in case of updates)
    $instances = get_option('ctp_instances', array());
    foreach ($instances as $id => $instance) {
        if (isset($instance['shortcode_name']) && !empty($instance['shortcode_name'])) {
            remove_shortcode($instance['shortcode_name']);
        }
    }

    // Register shortcodes for each instance
    foreach ($instances as $id => $instance) {
        if (isset($instance['shortcode_name']) && !empty($instance['shortcode_name'])) {
            add_shortcode($instance['shortcode_name'], function () use ($id, $instance) {
                $classes = array('configurable-text');

                // Add rotation class if needed
                if (isset($instance['rotation_speed']) && $instance['rotation_speed'] > 0) {
                    $classes[] = 'ctp-text-' . esc_attr($id);
                }

                // Add alignment class
                $text_align = isset($instance['text_align']) ? $instance['text_align'] : 'left';
                $classes[] = 'ctp-align-' . esc_attr($text_align);

                // Build inline style for font settings
                $style = '';

                // Font family
                if (isset($instance['font_family']) && !empty($instance['font_family'])) {
                    $style .= 'font-family: ' . esc_attr($instance['font_family']) . '; ';
                }

                // Font size
                if (isset($instance['font_size']) && !empty($instance['font_size'])) {
                    $style .= 'font-size: ' . esc_attr($instance['font_size']) . '; ';
                }

                // Font weight
                if (isset($instance['font_weight']) && !empty($instance['font_weight'])) {
                    $style .= 'font-weight: ' . esc_attr($instance['font_weight']) . '; ';
                }

                // Font style
                if (isset($instance['font_style']) && !empty($instance['font_style'])) {
                    $style .= 'font-style: ' . esc_attr($instance['font_style']) . '; ';
                }

                // Text color
                if (isset($instance['text_color']) && !empty($instance['text_color'])) {
                    $style .= 'color: ' . esc_attr($instance['text_color']) . '; ';
                }

                // Line height
                if (isset($instance['line_height']) && !empty($instance['line_height'])) {
                    $style .= 'line-height: ' . esc_attr($instance['line_height']) . '; ';
                }

                $style_attr = !empty($style) ? ' style="' . $style . '"' : '';

                // Prepare the text content based on settings
                $text_content = $instance['text'];

                // Process text based on settings
                if (isset($instance['render_html']) && $instance['render_html']) {
                    // If HTML rendering is enabled, use wp_kses_post to allow safe HTML
                    $text_content = wp_kses_post($text_content);

                    // If both HTML rendering and formatting preservation are enabled,
                    // we need to be careful not to double-process line breaks
                    if (isset($instance['preserve_formatting']) && $instance['preserve_formatting']) {
                        // For HTML content with preserved formatting, we need to ensure
                        // that line breaks not already handled by HTML tags are converted
                        $text_content = wpautop($text_content);
                    }
                } else {
                    // If HTML rendering is disabled but formatting is preserved
                    if (isset($instance['preserve_formatting']) && $instance['preserve_formatting']) {
                        // Escape HTML first, then convert line breaks
                        $text_content = esc_html($text_content);
                        $text_content = nl2br($text_content);
                    } else {
                        // Just escape HTML, no formatting preservation
                        $text_content = esc_html($text_content);
                    }
                }

                return '<p class="' . esc_attr(implode(' ', $classes)) . '"' . $style_attr . '>' . $text_content . '</p>';
            });
        }
    }
}

// Initialize shortcodes
function ctp_init()
{
    ctp_register_shortcodes();
}

add_action('init', 'ctp_init');

// Handle AJAX save instance
function ctp_ajax_save_instance() {
    // Log the request for debugging
    error_log('AJAX save instance request received: ' . json_encode($_POST));

    try {
        // Check nonce
        check_ajax_referer('ctp_instance_nonce', 'nonce');

        $instances = get_option('ctp_instances', array());
        $id = sanitize_text_field($_POST['instance_id']);
        $success_message = '';

        // Log the instance ID
        error_log('Processing save for instance ID: ' . $id);

        if (isset($_POST['ctp_instance']) && is_array($_POST['ctp_instance'])) {
            $instance = $_POST['ctp_instance'][$id];

            $shortcode_name = sanitize_title($instance['shortcode_name']);
            if (empty($shortcode_name)) {
                $shortcode_name = 'configurable_text_' . $id;
            }

            $instances[$id] = array(
                'name' => sanitize_text_field($instance['name']),
                'text' => isset($instance['render_html']) && $instance['render_html'] ? 
                    wp_kses_post($instance['text']) : 
                    (isset($instance['preserve_formatting']) && $instance['preserve_formatting'] ? 
                        sanitize_textarea_field($instance['text']) : 
                        sanitize_text_field($instance['text'])),
                'shortcode_name' => $shortcode_name,
                'rotation_speed' => intval($instance['rotation_speed']),
                'rotation_axis' => sanitize_text_field($instance['rotation_axis']),
                'text_align' => sanitize_text_field($instance['text_align']),
                'font_family' => sanitize_text_field($instance['font_family']),
                'font_size' => sanitize_text_field($instance['font_size']),
                'font_weight' => sanitize_text_field($instance['font_weight']),
                'font_style' => sanitize_text_field($instance['font_style']),
                'text_color' => sanitize_text_field($instance['text_color']),
                'line_height' => sanitize_text_field($instance['line_height']),
                'preserve_formatting' => isset($instance['preserve_formatting']) ? 1 : 0,
                'render_html' => isset($instance['render_html']) ? 1 : 0
            );

            update_option('ctp_instances', $instances);
            $success_message = sprintf(esc_html__('Instance "%s" saved.', 'configurable-text-plugin'), $instance['name']);

            // Re-register shortcodes
            ctp_register_shortcodes();

            error_log('Instance saved successfully: ' . $id . ' - ' . $instance['name']);

            wp_send_json_success(array(
                'message' => $success_message,
                'instance_id' => $id,
                'instance_name' => $instance['name']
            ));
        } else {
            error_log('Error: ctp_instance not set or not an array');
            wp_send_json_error(array(
                'message' => esc_html__('Error saving instance: Missing instance data.', 'configurable-text-plugin')
            ));
        }
    } catch (Exception $e) {
        error_log('Exception in ctp_ajax_save_instance: ' . $e->getMessage());
        wp_send_json_error(array(
            'message' => esc_html__('Error saving instance: ', 'configurable-text-plugin') . $e->getMessage()
        ));
    }

    wp_die();
}

add_action('wp_ajax_ctp_save_instance', 'ctp_ajax_save_instance');

// Handle AJAX delete instance
function ctp_ajax_delete_instance() {
    // Log the request for debugging
    error_log('AJAX delete instance request received: ' . json_encode($_POST));

    try {
        // Check nonce
        check_ajax_referer('ctp_instance_nonce', 'nonce');

        $instances = get_option('ctp_instances', array());
        $id = sanitize_text_field($_POST['instance_id']);
        $success_message = '';

        // Log the instance ID
        error_log('Processing delete for instance ID: ' . $id);

        if (isset($instances[$id])) {
            $instance_name = $instances[$id]['name'];
            unset($instances[$id]);
            update_option('ctp_instances', $instances);
            $success_message = sprintf(esc_html__('Instance "%s" deleted.', 'configurable-text-plugin'), $instance_name);

            // Re-register shortcodes
            ctp_register_shortcodes();

            error_log('Instance deleted successfully: ' . $id . ' - ' . $instance_name);

            wp_send_json_success(array(
                'message' => $success_message,
                'deleted_id' => $id
            ));
        } else {
            error_log('Error: Instance ID ' . $id . ' not found');
            wp_send_json_error(array(
                'message' => esc_html__('Error deleting instance: Instance not found.', 'configurable-text-plugin')
            ));
        }
    } catch (Exception $e) {
        error_log('Exception in ctp_ajax_delete_instance: ' . $e->getMessage());
        wp_send_json_error(array(
            'message' => esc_html__('Error deleting instance: ', 'configurable-text-plugin') . $e->getMessage()
        ));
    }

    wp_die();
}

add_action('wp_ajax_ctp_delete_instance', 'ctp_ajax_delete_instance');
