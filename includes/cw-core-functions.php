<?php


/**
 *
 */
function cw_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
{
    if ($args && is_array($args)) {
        extract($args);
    }

    $located = cw_locate_template($template_name, $template_path, $default_path);

    if (!file_exists($located)) {
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
        return;
    }

    include($located);

}

/**
 *
 */
function cw_locate_template($template_name, $template_path = '', $default_path = '')
{
    if (!$template_path) {
        $template_path = CW()->template_path();
    }

    if (!$default_path) {
        $default_path = CW()->plugin_path() . '/templates/';
    }

    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            trailingslashit($template_path) . $template_name,
            $template_name
        )
    );

    // Get default template
    if (!$template) {
        $template = $default_path . $template_name;
    }

    // Return what we found
    return apply_filters('woocommerce_locate_template', $template, $template_name, $template_path);
}