<?php

function ProductsTableCompare_init($file) {

    require_once('ProductsTableCompare_Plugin.php');
    $aPlugin = new ProductsTableCompare_Plugin();


    if (!$aPlugin->isInstalled()) {
        $aPlugin->install();
    }
    else {

        $aPlugin->upgrade();
    }

    // Add callbacks to hooks
    $aPlugin->addActionsAndFilters();

    if (!$file) {
        $file = __FILE__;
    }
    // Register the Plugin Activation Hook
    register_activation_hook($file, array(&$aPlugin, 'activate'));


    // Register the Plugin Deactivation Hook
    register_deactivation_hook($file, array(&$aPlugin, 'deactivate'));
}
