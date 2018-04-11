<?php

namespace CustomResources;


class MetaBox {
	
	const POST_RESOURCES = "additional_resources";
	
	/**
	 * MetaBox constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin) {
		$this->plugin = $plugin;
		add_action( 'add_meta_boxes_post', array($this,'add_meta_box') );
		add_action( 'save_post', array($this,'save'), 10, 2 );
	}
	
	function add_meta_box() {
		add_meta_box(
			'additional-resources-meta-box',
			__( 'Additional resources', Plugin::DOMAIN ),
			array($this,'render'),
			'post',
			'advanced',
			'default'
		);
	}
	
	function render( $post ) {
		wp_nonce_field( '_additional_resources_nonce', 'additional_resources_nonce' );
		
		wp_enqueue_style("additional_resources_meta_box_style", $this->plugin->url."/css/meta-box.css");
		wp_enqueue_script("additional_resources_meta_box_script", $this->plugin->url."/js/meta-box.js",array(),1, true);

		$config = array(
			"strings" => array(
				"itemLabel" => __('Resource', Plugin::DOMAIN),
				"add" => __("Add resource", Plugin::DOMAIN),
				"delete" => __("Delete", Plugin::DOMAIN),
			),
			"options" => array(
				"type" => array(
						array(
							"value" => "js",
							"name" => __("JavaScript", Plugin::DOMAIN),
						),
						array(
							"value" => "css",
							"name" => __("Stylesheet", Plugin::DOMAIN),
						)
				),
				"position" => array(
					array(
						"value" => "footer",
						"name" => __("Footer", Plugin::DOMAIN),
					),
					array(
						"value" => "head",
						"name" => __("Head", Plugin::DOMAIN),
					)
				),
				"inplace" => array(
					array(
						"value" => "false",
						"name" => __("Link to external file", Plugin::DOMAIN),
					),
					array(
						"value" => "true",
						"name" => __("Embed external file", Plugin::DOMAIN),
					)
				)
			),
			"post_key" => self::POST_RESOURCES,
			"resources" => get_post_meta($post->ID, Plugin::POST_META_RESOURCES, true),
			"root_id" => "meta_additional_resources",
			
		);
		wp_localize_script('additional_resources_meta_box_script', 'AdditionalResources', $config);
		
		?>
		<div id="meta_additional_resources"></div>
		<?php
	}
	
	
	
	function save( $post_id, $post ) {

		// Verifying the nonce
		if ( ! isset( $_POST['additional_resources_nonce'] ) )
			return;
		if ( ! wp_verify_nonce( $_POST['additional_resources_nonce'], '_additional_resources_nonce' ) )
			return;
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;
		
		if(isset($_POST) && is_array($_POST[self::POST_RESOURCES])){

			$resources = $_POST[self::POST_RESOURCES];
			// keys and values from options
			$urls = $resources["url"];
			$types = $resources["type"];
			$positions = $resources["position"];
			$inplaces = $resources["inplace"];
			$value = array();
			foreach ($urls as $index => $url){
				if(empty($url)) continue;
				if(filter_var($url, FILTER_VALIDATE_URL) === FALSE) continue;
				$value[] = array(
					"url" => $url,
					"type" => $types[$index],
					"position" => $positions[$index],
					"inplace" => $inplaces[$index],
				);
			}
			update_post_meta($post_id, Plugin::POST_META_RESOURCES, $value);
		}
		
	}

}