<?php


class ProductsTableCompare_OptionsManager {

    public function getOptionNamePrefix() {
        return get_class($this) . '_';
    }


    /**
     * @return array of key=>display-name and/or key=>array(display-name, choice1, choice2, ...)
     */
    public function getOptionMetaData() {
        return array();
    }

    /**
     * @return array of string name of options
     */
    public function getOptionNames() {
        return array_keys($this->getOptionMetaData());
    }

    /**
     * @return void
     */
    protected function initOptions() {
    }

    /**
     * @return void
     */
    protected function deleteSavedOptions() {
        $optionMetaData = $this->getOptionMetaData();
        if (is_array($optionMetaData)) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                $prefixedOptionName = $this->prefix($aOptionKey); // how it is stored in DB
                delete_option($prefixedOptionName);
            }
        }
    }

    /**
     * @return string display name of the plugin to show as a name/title in HTML.
     */
    public function getPluginDisplayName() {
        return get_class($this);
    }

    /**
     * @param  $name string option name to prefix. Defined in settings.php and set as keys of $this->optionMetaData
     * @return string
     */
    public function prefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) { // 0 but not false
            return $name; // already prefixed
        }
        return $optionNamePrefix . $name;
    }

    /**
     * @param  $name string
     * @return string $optionName without the prefix.
     */
    public function &unPrefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) {
            return substr($name, strlen($optionNamePrefix));
        }
        return $name;
    }

    /**
     * @param $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param $default string default value to return if the option is not set
     * @return string the value from delegated call to get_option(), or optional default value
     */
    public function getOption($optionName, $default = null) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        $retVal = get_option($prefixedOptionName);
        if (!$retVal && $default) {
            $retVal = $default;
        }
        return $retVal;
    }

    /**
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @return bool from delegated call to delete_option()
     */
    public function deleteOption($optionName) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return delete_option($prefixedOptionName);
    }

    /**
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function addOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return add_option($prefixedOptionName, $value);
    }

    /**
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function updateOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return update_option($prefixedOptionName, $value);
    }

    /**
     * @param  $optionName
     * @return string role name
     */
    public function getRoleOption($optionName) {
        $roleAllowed = $this->getOption($optionName);
        if (!$roleAllowed || $roleAllowed == '') {
            $roleAllowed = 'Administrator';
        }
        return $roleAllowed;
    }

    /**
     * @param  $roleName
     * @return string a WP capability or '' if unknown input role
     */
    protected function roleToCapability($roleName) {
        switch ($roleName) {
            case 'Super Admin':
                return 'manage_options';
            case 'Administrator':
                return 'manage_options';
            case 'Editor':
                return 'publish_pages';
            case 'Author':
                return 'publish_posts';
            case 'Contributor':
                return 'edit_posts';
            case 'Subscriber':
                return 'read';
            case 'Anyone':
                return 'read';
        }
        return '';
    }

    /**
     * @param $roleName string a standard WP role name like 'Administrator'
     * @return bool
     */
    public function isUserRoleEqualOrBetterThan($roleName) {
        if ('Anyone' == $roleName) {
            return true;
        }
        $capability = $this->roleToCapability($roleName);
        return current_user_can($capability);
    }

    /**
     * @param  $optionName string name of a Role option (see comments in getRoleOption())
     * @return bool indicates if the user has adequate permissions
     */
    public function canUserDoRoleOption($optionName) {
        $roleAllowed = $this->getRoleOption($optionName);
        if ('Anyone' == $roleAllowed) {
            return true;
        }
        return $this->isUserRoleEqualOrBetterThan($roleAllowed);
    }

    /**
     * @return void
     */
    public function createSettingsMenu() {
        $pluginName = $this->getPluginDisplayName();
        //create new top-level menu
        add_menu_page($pluginName . ' Plugin Settings',
                      $pluginName,
                      'administrator',
                      get_class($this),
                      array(&$this, 'settingsPage')
        /*,plugins_url('/images/icon.png', __FILE__)*/); // if you call 'plugins_url; be sure to "require_once" it

        //call register settings function
        add_action('admin_init', array(&$this, 'registerSettings'));
    }

    public function registerSettings() {
        $settingsGroup = get_class($this) . '-settings-group';
        $optionMetaData = $this->getOptionMetaData();
        foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
            register_setting($settingsGroup, $aOptionMeta);
        }
    }

    /**
     * @return void
     */
    public function settingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'products-table-compare'));
        }

        $optionMetaData = $this->getOptionMetaData();

        // Save Posted Options
        if ($optionMetaData != null) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                if (isset($_POST[$aOptionKey])) {
                    $this->updateOption($aOptionKey, sanitize_key($_POST[$aOptionKey]));
                }
            }
        }

        // HTML for the page
        $settingsGroup = get_class($this) . '-settings-group';
        ?>
        <div class="wrap">
            <h2><?php _e('System Settings', 'products-table-compare'); ?></h2>
            <table cellspacing="1" cellpadding="2"><tbody>
            <tr><td><?php _e('System', 'products-table-compare'); ?></td><td><?php echo php_uname(); ?></td></tr>
            <tr><td><?php _e('PHP Version', 'products-table-compare'); ?></td>
                <td><?php echo phpversion(); ?>
                <?php
                if (version_compare('5.2', phpversion()) > 0) {
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    _e('(WARNING: This plugin may not work properly with versions earlier than PHP 5.2)', 'products-table-compare');
                    echo '</span>';
                }
                ?>
                </td>
            </tr>
            <tr><td><?php _e('MySQL Version', 'products-table-compare'); ?></td>
                <td><?php echo $this->getMySqlVersion() ?>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    if (version_compare('5.0', $this->getMySqlVersion()) > 0) {
                        _e('(WARNING: This plugin may not work properly with versions earlier than MySQL 5.0)', 'products-table-compare');
                    }
                    echo '</span>';
                    ?>
                </td>
            </tr>
            </tbody></table>

            <h2><?php echo $this->getPluginDisplayName(); echo ' '; _e('Settings', 'products-table-compare'); ?></h2>

            <form method="post" action="">
            <?php settings_fields($settingsGroup); ?>
                <style type="text/css">
                    table.plugin-options-table {width: 100%; padding: 0;}
                    table.plugin-options-table tr:nth-child(even) {background: #f9f9f9}
                    table.plugin-options-table tr:nth-child(odd) {background: #FFF}
                    table.plugin-options-table tr:first-child {width: 35%;}
                    table.plugin-options-table td {vertical-align: middle;}
                    table.plugin-options-table td+td {width: auto}
                    table.plugin-options-table td > p {margin-top: 0; margin-bottom: 0;}
                </style>
                <table class="plugin-options-table"><tbody>
                <?php
                if ($optionMetaData != null) {
                    foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                        $displayText = is_array($aOptionMeta) ? $aOptionMeta[0] : $aOptionMeta;
                        ?>
                            <tr valign="top">
                                <th scope="row"><p><label for="<?php echo $aOptionKey ?>"><?php echo $displayText ?></label></p></th>
                                <td>
                                <?php $this->createFormControl($aOptionKey, $aOptionMeta, $this->getOption($aOptionKey)); ?>
                                </td>
                            </tr>
                        <?php
                    }
                }
                ?>
                </tbody></table>
                <p class="submit">
                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', 'products-table-compare') ?>"/>
                </p>
            </form>
        </div>
        <?php

    }

    /**
     * @param  $aOptionKey string name of the option (un-prefixed)
     * @param  $aOptionMeta mixed meta-data for $aOptionKey (either a string display-name or an array(display-name, option1, option2, ...)
     * @param  $savedOptionValue string current value for $aOptionKey
     * @return void
     */
    protected function createFormControl($aOptionKey, $aOptionMeta, $savedOptionValue) {
        if (is_array($aOptionMeta) && count($aOptionMeta) >= 2) { // Drop-down list
            $choices = array_slice($aOptionMeta, 1);
            ?>
            <p><select name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>">
            <?php
                            foreach ($choices as $aChoice) {
                $selected = ($aChoice == $savedOptionValue) ? 'selected' : '';
                ?>
                    <option value="<?php echo $aChoice ?>" <?php echo $selected ?>><?php echo $this->getOptionValueI18nString($aChoice) ?></option>
                <?php
            }
            ?>
            </select></p>
            <?php

        }
        else { // Simple input field
            ?>
            <p><input type="text" name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>"
                      value="<?php echo esc_attr($savedOptionValue) ?>" size="50"/></p>
            <?php

        }
    }

    /**
     * @param  $optionValue string
     * @return string __($optionValue) if it is listed in this method, otherwise just returns $optionValue
     */
    protected function getOptionValueI18nString($optionValue) {
        switch ($optionValue) {
            case 'true':
                return __('true', 'products-table-compare');
            case 'false':
                return __('false', 'products-table-compare');

            case 'Administrator':
                return __('Administrator', 'products-table-compare');
            case 'Editor':
                return __('Editor', 'products-table-compare');
            case 'Author':
                return __('Author', 'products-table-compare');
            case 'Contributor':
                return __('Contributor', 'products-table-compare');
            case 'Subscriber':
                return __('Subscriber', 'products-table-compare');
            case 'Anyone':
                return __('Anyone', 'products-table-compare');
        }
        return $optionValue;
    }

    /**
     * Query MySQL DB for its version
     * @return string|false
     */
    protected function getMySqlVersion() {
        global $wpdb;
        $rows = $wpdb->get_results('select version() as mysqlversion');
        if (!empty($rows)) {
             return $rows[0]->mysqlversion;
        }
        return false;
    }

    /**
     * @return string domain name
     */
    public function getEmailDomain() {
        // Get the site domain and get rid of www.
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }
        return $sitename;
    }
}

