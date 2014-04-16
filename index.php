<?php if(!defined('IS_CMS')) die();

/**
 * Plugin:   CreativeMenu
 * @author:  HPdesigner (hpdesigner[at]web[dot]de)
 * @version: v0.1.2013-08-31
 * @license: GPL v3+
 *
 * Plugin created by DEVMOUNT
 * www.devmount.de
 *
**/

class CreativeMenu extends Plugin {

	function getContent($value) {
		//global $CMS_CONF;
		global $CatPage;
		//global $syntax;

		// get conf
		$symbols 	= explode('<br />', $this->settings->get("symbols"));
		$subtitles 	= explode('<br />', $this->settings->get("subtitles"));

		$menu = '<div class="ca-menu">';

		// read categories
		$categoriesarray = $this->getCategories();

		$c = 0;
		$n = intval(15/$this->countCategories());

		// build menu
		foreach ($categoriesarray as $cat) {
			$menu .= '<div class="large-' . $n . ' small-' . $n . ' columns';
				if($CatPage->is_Activ($cat,false) or $CatPage->get_HrefText($cat,false) == substr(CAT_REQUEST, 0, strpos(CAT_REQUEST,'%2F'))) {
					$menu .= ' dmenuactive';
				} else $menu .= ' dmenu';
			$menu .= '">
						<a href="' . $CatPage->get_Href($cat,false) . '">
							<span class="ca-icon">' . trim($symbols[$c]) . '</span>
							<div class="ca-content hide-for-small">
								<h2 class="ca-main">' . $CatPage->get_HrefText($cat,false) . '</h2>
								<h3 class="ca-sub">' . trim($subtitles[$c]) . '</h3>
							</div>
						</a>
					</div>';
			$c++;
		}

		$menu .= "</div>";

		// return
		return $menu;
	}

	function getCategories() {
		global $CatPage;

		$nofirstcat = $this->settings->get("nofirstcat");
		$maxcat = $this->settings->get("maxcat");

		$categoriesarray = $CatPage->get_CatArray();

		if ($nofirstcat) unset($categoriesarray[0]);

		// ignore subcategories
		foreach ($categoriesarray as $key => $cat) {
			if (strpos($CatPage->get_HrefText($cat,false),'/') !== false) {
				unset($categoriesarray[$key]);
			}
		}

		if ($maxcat > 0 and $maxcat <= count($categoriesarray)) $categoriesarray = array_slice($categoriesarray, 0, $maxcat);

		return $categoriesarray;
	}

	function countCategories() {
		return count($this->getCategories());
	}

	function getConfig() {
		global $CatPage;

		$config = array();

		// nofirstcat
		$config['nofirstcat']  = array(
			"type" => "checkbox",
			"description" => 'Die erste Kategorie im Menü ignorieren.'
		);

		// maxcat
		$catnumbers = array();
		for ($i=1; $i <= count($CatPage->get_CatArray()); $i++) $catnumbers[$i] = $i;
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

		// Template
		$config['--template~~'] = '
				<div class="mo-in-li-l" style="width:29%;">{nofirstcat_description}</div>
				<div class="mo-in-li-l" style="width:19%;">{nofirstcat_checkbox}</div>
				<div class="mo-in-li-r" style="width:19%;">{maxcat_select}</div>
				<div class="mo-in-li-r" style="width:29%;">{maxcat_description}</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div class="mo-in-li-l" style="width:49%;">{symbols_description}<br />{symbols_textarea}</div>
				<div class="mo-in-li-r" style="width:49%;">{subtitles_description}<br />{subtitles_textarea}
		';

		return $config;
	}


	function getInfo() {
		global $ADMIN_CONF;
		$language = $ADMIN_CONF->get("language");

		$info['deDE'] = array(
			// Plugin-Name
			'<b>CreativeMenu</b> v0.1.2013-08-31',
			// CMS-Version
			"2.0",
			// Kurzbeschreibung
			'{CreativeMenu} erstellt das Hauptmenü mit Symbolen und Untertiteln zu jeweiligen Hauptmenpunkten.<br />Symbole und Untertitel können in den Textfeldern definiert werden, indem pro Zeile ein Symbol bzw. Untertitel notiert wird. Dabei sind Anzahl und Reihenfolge äquivalent zu den Hauptmenüpunkten.',
			// Name des Autors
			"HPdesigner",
			// Download-URL
			"http://www.devmount.de/Develop/moziloCMS/Plugins/CreativeMenu.html",
			array('{CreativeMenu}' => 'Erstellt ein CSS3 Menü')
		);

		if(isset($info[$language])) return $info[$language]; else return $info['deDE'];
	}

}
?>