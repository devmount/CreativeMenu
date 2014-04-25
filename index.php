<?php

/**
 * moziloCMS Plugin: CreativeMenu
 *
 * Generates a CSS3 menu of moziloCMS categories
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_MoziloPlugins
 * @author   HPdesigner <mail@devmount.de>
 * @license  GPL v3+
 * @version  GIT: v0.1.2013-08-31
 * @link     https://github.com/devmount/CreativeMenu
 * @link     http://devmount.de/Develop/moziloCMS/Plugins/CreativeMenu.html
 *
 * Plugin created by DEVMOUNT
 * www.devmount.de
 *
 */

// only allow moziloCMS environment
if (!defined('IS_CMS')) {
    die();
}

/**
 * CreativeMenu Class
 *
 * @category PHP
 * @package  PHP_MoziloPlugins
 * @author   HPdesigner <mail@devmount.de>
 * @license  GPL v3+
 * @link     https://github.com/devmount/CreativeMenu
 */
class CreativeMenu extends Plugin
{
    // language
    private $_admin_lang;

    // plugin information
    const PLUGIN_AUTHOR  = 'HPdesigner';
    const PLUGIN_DOCU
        = 'http://devmount.de/Develop/moziloCMS/Plugins/CreativeMenu.html';
    const PLUGIN_TITLE   = 'CreativeMenu';
    const PLUGIN_VERSION = 'v0.1.2013-08-31';
    const MOZILO_VERSION = '2.0';
    private $_plugin_tags = array(
        'tag1' => '{CreativeMenu}',
    );

    const LOGO_URL = 'http://media.devmount.de/logo_pluginconf.png';

    /**
     * creates plugin content
     *
     * @param string $value Parameter divided by '|'
     *
     * @return string HTML output
     */
    function getContent($value)
    {
        global $CatPage;

        // get conf
        $symbols   = explode('<br />', $this->settings->get("symbols"));
        $subtitles = explode('<br />', $this->settings->get("subtitles"));

        // read categories
        $categoriesarray = $this->getCategories();

        $c = 0;
        $n = intval(15/$this->countCategories());

        $catname = substr(CAT_REQUEST, 0, strpos(CAT_REQUEST, '%2F'));

        // initialize menu html content
        $menu = '<div class="ca-menu">';

        // build menu
        foreach ($categoriesarray as $cat) {
            $menu .= '<div class="large-' . $n . ' small-' . $n . ' columns';
            if ($CatPage->is_Activ($cat, false)
                or $CatPage->get_HrefText($cat, false) == $catname
            ) {
                $menu .= ' dmenuactive';
            } else {
                $menu .= ' dmenu';
            }
            $menu .= '">
                <a href="' . $CatPage->get_Href($cat, false) . '">
                    <span class="ca-icon">' . trim($symbols[$c]) . '</span>
                    <div class="ca-content hide-for-small">
                        <h2 class="ca-main">'
                        . $CatPage->get_HrefText($cat, false)
                        . '</h2>
                        <h3 class="ca-sub">'
                        . trim($subtitles[$c])
                        . '</h3>
                    </div>
                </a>
            </div>';
            $c++;
        }

        $menu .= "</div>";

        // return
        return $menu;
    }

    /**
     * returns categories by conf
     *
     * @return array categories
     */
    function getCategories()
    {
        global $CatPage;

        // get conf
        $nofirstcat = $this->settings->get("nofirstcat");
        $maxcat     = $this->settings->get("maxcat");

        // get all categories
        $categoriesarray = $CatPage->get_CatArray();

        if ($nofirstcat) {
            unset($categoriesarray[0]);
        }

        // ignore subcategories
        foreach ($categoriesarray as $key => $cat) {
            if (strpos($CatPage->get_HrefText($cat, false), '/') !== false) {
                unset($categoriesarray[$key]);
            }
        }

        // cut off categories over maxcat
        if ($maxcat > 0 and $maxcat <= count($categoriesarray)) {
            $categoriesarray = array_slice($categoriesarray, 0, $maxcat);
        }

        // return categories
        return $categoriesarray;
    }

    /**
     * calculates number of all categories
     *
     * @return integer number of all categories
     */
    function countCategories()
    {
        return count($this->getCategories());
    }

    /**
     * sets backend configuration elements and template
     *
     * @return Array configuration
     */
    function getConfig()
    {
        global $CatPage;

        $config = array();

        // nofirstcat
        $config['nofirstcat']  = array(
            "type" => "checkbox",
            "description" => 'Die erste Kategorie im Menü ignorieren.'
        );

        // maxcat
        $catnumbers = array();
        for ($i=1; $i <= count($CatPage->get_CatArray()); $i++) {
            $catnumbers[$i] = $i;
        }
        $config['maxcat']  = array(
            "type" => "select",
            "description" => 'Maximale Anzahl an Menüpunkten',
            'descriptions' => $catnumbers,
            'multiple' => false
        );

        // Symbols
        $config['symbols']  = array(
            "type" => "textarea",
            "description" => $this->countCategories() . ' Symbole (zeilenweise)',
            "rows" => "10"
        );

        // Subtitles
        $config['subtitles']  = array(
            "type" => "textarea",
            "description" => $this->countCategories() . ' Untertitel (zeilenweise)',
            "rows" => "10"
        );

        // read admin.css
        $admin_css = '';
        $lines = file('../plugins/' . self::PLUGIN_TITLE. '/admin.css');
        foreach ($lines as $line_num => $line) {
            $admin_css .= trim($line);
        }

        // add template CSS
        $template = '<style>' . $admin_css . '</style>';

        // build Template
        $template .= '
            <div class="plugindraft-admin-header">
            <span>'
                . $this->_admin_lang->getLanguageValue(
                    'admin_header',
                    self::PLUGIN_TITLE
                )
            . '</span>
            <a href="' . self::PLUGIN_DOCU . '" target="_blank">
            <img style="float:right;" src="' . self::LOGO_URL . '" />
            </a>
            </div>
        </li>
        <li class="mo-in-ul-li ui-widget-content plugindraft-admin-li">
            <div class="plugindraft-admin-subheader">'
            . $this->_admin_lang->getLanguageValue('admin_categories')
            . '</div>
            <div class="creativemenu-single-conf">
                {nofirstcat_checkbox}
                {nofirstcat_description}
            </div>
            <div class="creativemenu-single-conf">
                {maxcat_select}
                {maxcat_description}
           </div>
        </li>
        <li class="mo-in-ul-li ui-widget-content plugindraft-admin-li">
            <div class="plugindraft-admin-subheader">'
            . $this->_admin_lang->getLanguageValue('admin_content')
            . '</div>
            <div class="creativemenu-col-third">
                {symbols_description}<br />{symbols_textarea}
            </div>
            <div class="creativemenu-col-third">
                {subtitles_description}<br />{subtitles_textarea}
        ';

        $config['--template~~'] = $template;

        return $config;
    }

    /**
     * sets backend plugin information
     *
     * @return Array information
     */
    function getInfo()
    {
        global $ADMIN_CONF;

        $this->_admin_lang = new Language(
            $this->PLUGIN_SELF_DIR
            . 'lang/admin_language_'
            . $ADMIN_CONF->get('language')
            . '.txt'
        );

        // build plugin tags
        $tags = array();
        foreach ($this->_plugin_tags as $key => $tag) {
            $tags[$tag] = $this->_admin_lang->getLanguageValue('tag_' . $key);
        }

        $info = array(
            '<b>' . self::PLUGIN_TITLE . '</b> ' . self::PLUGIN_VERSION,
            self::MOZILO_VERSION,
            $this->_admin_lang->getLanguageValue(
                'description',
                htmlspecialchars($this->_plugin_tags['tag1'])
            ),
            self::PLUGIN_AUTHOR,
            self::PLUGIN_DOCU,
            $tags
        );

        return $info;
    }

}

?>