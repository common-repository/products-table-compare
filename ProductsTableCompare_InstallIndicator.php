<?php


include_once('ProductsTableCompare_OptionsManager.php');

class ProductsTableCompare_InstallIndicator extends ProductsTableCompare_OptionsManager {

    const optionInstalled = '_installed';
    const optionVersion = '_version';

    /**
     * @return bool indicating if the plugin is installed already
     */
    public function isInstalled() {
        return $this->getOption(self::optionInstalled) == true;
    }

    /**
     * @return null
     */
    protected function markAsInstalled() {
        return $this->updateOption(self::optionInstalled, true);
    }

    /**
     * @return bool returned form delete_option.
     */
    protected function markAsUnInstalled() {
        return $this->deleteOption(self::optionInstalled);
    }

    /**
     * @return null
     */
    protected function getVersionSaved() {
        return $this->getOption(self::optionVersion);
    }

    /**
     * @param  $version string best practice: use a dot-delimited string like '1.2.3' so version strings can be easily
     * @return null
     */
    protected function setVersionSaved($version) {
        return $this->updateOption(self::optionVersion, $version);
    }

    /**
     * @return string name of the main plugin file that has the header section with
     */
    protected function getMainPluginFileName() {
        return basename(dirname(__FILE__)) . 'php';
    }

    /**
     * @param $key string plugin header key
     * @return string if found, otherwise null
     */
    public function getPluginHeaderValue($key) {
        // Read the string from the comment header of the main plugin file
        $data = file_get_contents($this->getPluginDir() . DIRECTORY_SEPARATOR . $this->getMainPluginFileName());
        $match = array();
        preg_match('/' . $key . ':\s*(\S+)/', $data, $match);
        if (count($match) >= 1) {
            return $match[1];
        }
        return null;
    }

    /**
     * @return string
     */
    protected function getPluginDir() {
        return dirname(__FILE__);
    }

    /**
     * @return string
     */
    public function getVersion() {
        return $this->getPluginHeaderValue('Version');
    }


    /**
     * @return bool true if the version saved in the options is earlier than the version declared in getVersion().
     */
    public function isInstalledCodeAnUpgrade() {
        return $this->isSavedVersionLessThan($this->getVersion());
    }

    /**
     * @param  $aVersion string
     * @return bool true if the saved version is earlier (by natural order) than the input version
     */
    public function isSavedVersionLessThan($aVersion) {
        return $this->isVersionLessThan($this->getVersionSaved(), $aVersion);
    }

    /**
     * @param  $aVersion string
     * @return bool true if the saved version is earlier (by natural order) than the input version
     */
    public function isSavedVersionLessThanEqual($aVersion) {
        return $this->isVersionLessThanEqual($this->getVersionSaved(), $aVersion);
    }

    /**
     * @param  $version1 string a version string such as '1', '1.1', '1.1.1', '2.0', etc.
     * @param  $version2 string a version string such as '1', '1.1', '1.1.1', '2.0', etc.
     * @return bool true if version_compare of $versions1 and $version2 shows $version1 as the same or earlier
     */
    public function isVersionLessThanEqual($version1, $version2) {
        return (version_compare($version1, $version2) <= 0);
    }

    /**
     * @param  $version1 string a version string such as '1', '1.1', '1.1.1', '2.0', etc.
     * @param  $version2 string a version string such as '1', '1.1', '1.1.1', '2.0', etc.
     * @return bool true if version_compare of $versions1 and $version2 shows $version1 as earlier
     */
    public function isVersionLessThan($version1, $version2) {
        return (version_compare($version1, $version2) < 0);
    }

    /**
     * @return void
     */
    protected function saveInstalledVersion() {
        $this->setVersionSaved($this->getVersion());
    }


}
