<?php


include_once('ProductsTableCompare_LifeCycle.php');

class ProductsTableCompare_Plugin extends ProductsTableCompare_LifeCycle {

    /**
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'ATextInput' => array(__('Enter in some text', 'my-awesome-plugin')),
            'AmAwesome' => array(__('I like this awesome plugin', 'my-awesome-plugin'), 'false', 'true'),
            'CanDoSomething' => array(__('Which user role can do something', 'my-awesome-plugin'),
                                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone')
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Products Table Compare';
    }

    protected function getMainPluginFileName() {
        return 'products-table-compare.php';
    }

    /**
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
        
        add_action( 'wp_enqueue_scripts', array(&$this,'compare_products_scripts') );

            include_once('ProductsTableCompare_draw_table.php');
        	$sc = new ProductsTableCompare_draw_table();
        	$sc->register('products_compare');
    }
    
    
        public function compare_products_scripts()
    {
            wp_enqueue_script('products-table-compare-script', plugins_url('/js/products-table-compare.js', __FILE__));
            wp_enqueue_style('products-table-compare-style', plugins_url('/css/products-table-compare.css', __FILE__));

    }

}
