# Configurable Text Plugin

A WordPress plugin that allows you to display configurable text on your website using customizable shortcodes with rotation effects.

## Description

The Configurable Text Plugin provides an easy way to add and manage multiple text instances that can be displayed anywhere on your WordPress site using customizable shortcodes. Each text instance can have its own settings, including custom shortcode name, detailed font customization options, text alignment, and Z-axis rotation effect with configurable speed.

## Features

- Support for multiple text instances
- Customizable shortcode names for each text instance
- Text alignment options (left, center, right)
- Detailed font customization (family, size, weight, style, color, line height)
- 3D rotation effect with configurable axis (X, Y, or Z) and speed (rotations per minute)
- Clean, user-friendly settings interface
- Easy to add, edit, and remove text instances
- Multilingual support (currently includes German translations)

## Installation

1. Upload the `configurable-text-plugin` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Configurable Text to configure your text instances

## Usage

### Admin Configuration

1. Navigate to Settings > Configurable Text in your WordPress admin
2. For each text instance, you can configure:
   - The text content to display
   - A custom shortcode name
   - Text alignment (left, center, or right)
   - Font settings (family, size, weight, style, color, line height)
   - Rotation settings (axis: X, Y, or Z and speed in rotations per minute)
3. Add new instances with the "Add New Text Instance" button
4. Remove instances with the "Remove" link (if you have more than one)
5. Click "Save All Instances" to save your changes

The plugin features a modern, responsive Bootstrap-based interface for easy configuration and management of multiple text instances.

### Displaying the Text

Use the shortcode corresponding to each text instance to display it on any page or post:

```
[your_custom_shortcode_name]
```

For example, if you set the shortcode name to "rotating_text", you would use:

```
[rotating_text]
```

The default shortcode for the first instance is `[configurable_text]`.

## Text Alignment

To control the alignment of your text:

1. Select the desired alignment (left, center, or right) in the text instance settings
2. The text will be aligned according to your selection
3. This respects the WordPress editor's alignment settings

## Font Customization

To customize the font appearance of your text:

1. Expand the Font Settings section in the text instance settings
2. Configure the following options:
   - **Font Family**: Choose from common web-safe fonts (Arial, Helvetica, Georgia, etc.)
   - **Font Size**: Set the size with units (e.g., 16px, 1.2em, 90%)
   - **Font Weight**: Select the weight/boldness (normal, bold, 100-900)
   - **Font Style**: Choose normal, italic, or oblique
   - **Text Color**: Pick any color using the color picker
   - **Line Height**: Set the spacing between lines (e.g., 1.5, 2, 150%)
3. Each text instance can have its own font settings
4. Changes apply immediately when you save the settings

## Rotation Effect

To add a rotation effect to your text:

1. Set a rotation speed greater than 0 in the text instance settings
2. Choose the rotation axis:
   - **X-axis**: Rotates the text horizontally (flipping top to bottom)
   - **Y-axis**: Rotates the text vertically (flipping left to right)
   - **Z-axis**: Rotates the text in a circular motion (like a spinning wheel)
3. The default axis is X-axis if none is selected
4. The rotation is smooth and continuous at the specified speed (rotations per minute)

## Translations

The plugin is translation-ready and includes the following translations:
- English (default)
- German (de_DE)

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## License

This plugin is licensed under the GPL v2 or later.

## Author

Created by ckitte
