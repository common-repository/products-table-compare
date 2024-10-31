<?php
/*
   Plugin Name: Products Table Compare
   Plugin URI: http://wordpress.org/extend/plugins/products-table-compare/
   Version: 0.5
   Author: hippocampustech
   Description: compare products with table
   Text Domain: products-table-compare
   License: GPLv3
  */

/*


$ProductsTableCompare_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function ProductsTableCompare_noticePhpVersionWrong() {
    global $ProductsTableCompare_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Products Table Compare" requires a newer version of PHP to be running.',  'products-table-compare').
            '<br/>' . __('Minimal version of PHP required: ', 'products-table-compare') . '<strong>' . $ProductsTableCompare_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'products-table-compare') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function ProductsTableCompare_PhpVersionCheck() {
    global $ProductsTableCompare_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $ProductsTableCompare_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'ProductsTableCompare_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * @return void
 */
function ProductsTableCompare_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('products-table-compare', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','ProductsTableCompare_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (ProductsTableCompare_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('products-table-compare_init.php');
    ProductsTableCompare_init(__FILE__);
}
