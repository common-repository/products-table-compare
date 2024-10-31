<?php


abstract class ProductsTableCompare_ShortCodeLoader {

    /**
     * @param  $shortcodeName mixed either string name of the shortcode
     * @return void
     */
    public function register($shortcodeName) {
        $this->registerShortcodeToFunction($shortcodeName, 'handleShortcode');
    }

    /**
     * @param  $shortcodeName mixed either string name of the shortcode
     * @param  $functionName string name of public function in this class to call as the
     * @return void
     */
    protected function registerShortcodeToFunction($shortcodeName, $functionName) {
        if (is_array($shortcodeName)) {
            foreach ($shortcodeName as $aName) {
                add_shortcode($aName, array($this, $functionName));
            }
        }
        else {
            add_shortcode($shortcodeName, array($this, $functionName));
        }
    }

    /**
     * @abstract Override this function and add actual shortcode handling here
     * @param  $atts shortcode inputs
     * @return string shortcode content
     */
    public abstract function handleShortcode($atts);

}
