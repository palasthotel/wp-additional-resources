<?php
/**
 * Plugin Name: Additional Resources
 * Description: Add custom JavaScript and Styles to you posts
 * Version: 1.0
 * Author: PALASTHOTEL (by Edward Bock)
 * Author URI: https://palasthotel.de
 */

namespace CustomResources;


class Plugin {

	const DOMAIN  = "additinoal_resources";

	const POST_META_RESOURCES = "additional_resources";

	const FILTER_HEAD_RESOURCES_PRIORITY = "additional_resources_head_priority";
	const FILTER_FOOTER_RESOURCES_PRIORITY = "additional_resources_header_priority";

	public function __construct() {
		$this->url = plugin_dir_url(__FILE__ );
		$this->path = plugin_dir_path(__FILE__ );

		require_once dirname(__FILE__)."/classes/meta-box.php";
		$this->meta_box = new MetaBox($this);

		require_once dirname(__FILE__)."/classes/render.php";
		$this->render = new Render($this);

	}
}
new Plugin();