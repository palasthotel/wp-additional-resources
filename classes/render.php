<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 11.04.18
 * Time: 14:10
 */

namespace CustomResources;


class Render {
	public function __construct(Plugin $plugin) {
		add_action("init", array($this, "init"));
	}

	function init(){
		add_action("wp_head", array($this, "head"), apply_filters(Plugin::FILTER_HEAD_RESOURCES_PRIORITY, 99));
		add_action("wp_footer", array($this, "footer"), apply_filters(Plugin::FILTER_FOOTER_RESOURCES_PRIORITY, 99));
	}

	/**
	 * wp_head action
	 */
	function head(){
		$resources = get_post_meta(get_the_ID(), Plugin::POST_META_RESOURCES, true);
		if(!$this->isValid($resources)) return;

		$heads = array_filter($resources, function($element){
			return $element["position"] == "head";
		});
		$this->renderList($heads);
	}

	/**
	 * wp_footer action
	 */
	function footer(){
		$resources = get_post_meta(get_the_ID(), Plugin::POST_META_RESOURCES, true);
		if(!$this->isValid($resources)) return;

		$footers = array_filter($resources, function($element){
			return $element["position"] == "footer";
		});
		$this->renderList($footers);

	}

	/**
	 * render list of resources
	 * @param $list
	 */
	function renderList($list){
		foreach ($list as $item){
			$this->render($item);
		}
	}

	/**
	 * render single item
	 * @param $item
	 */
	function render($item){
		switch ($item["type"]){
			case "css":
				$this->renderCSS($item);
				break;
			case "js":
				$this->renderJS($item);
				break;
			default:
				echo "<!-- no idea how to embed {$item["url"]} -->";
		}
	}

	/**
	 * render "js" type
	 * @param $item
	 */
	function renderJS($item){
		if($item["inplace"] == "true"){
			echo "<script>".file_get_contents($item["url"])."</script>";
		} else {
			echo "<script src='{$item["url"]}'></script>";
		}
	}

	/**
	 * render "css" type
	 * @param $item
	 */
	function renderCSS($item){
		if($item["inplace"] == "true"){
			echo "<style>".file_get_contents($item["url"])."</style>";
		} else {
			echo "<link rel='stylesheet' href='{$item["url"]}' type='text/css' media='all'>";
		}
	}

	/**
	 * check if its a valid resource list
	 * @param $list
	 *
	 * @return bool
	 */
	private function isValid($list){
		return (is_array($list) && count($list) > 0);
	}
}