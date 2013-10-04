<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

butler_register_actions('butler_admin_meta_box', array('call' => array('butlerAdminMetaBoxes', 'init')));

class butlerAdminMetaBoxes {
			
	public static function init($args = array()) {
		
		return new butlerAdminMetaBoxesConstruct($args);
					
	}
		
}

class butlerAdminMetaBoxesConstruct {

	protected $args;
	protected $template_path;

	function __construct($args) {
	
		if ( !is_admin() )
			return;

		$this->args = $args;
		
		add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
		add_action('admin_menu', array($this, 'add'));
		add_action('save_post', array($this, 'save'));
		
	}
	
	function enqueue_assets() {
	
		global $post;
		
		foreach ( $this->args['pages'] as $page ) {
				
			if ( isset($post) && $post->post_type == $page ) {
			
				/* we enqueue the general css */
				wp_enqueue_style('btr-meta-boxes-general', BUTLER_URL . 'admin/templates/meta-boxes/assets/css/general' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION);
				
				foreach ( $this->args['fields'] as $field ) {
				
					if ( isset($field['custom']) && $field['custom'] )
						$template_path = $field['custom'];
					else
						$template_path = BUTLER_URL . 'admin/templates/meta-boxes/';
					
					/* we enqueue our css */
					if ( file_exists($template_path . 'assets/css/' . $field['type'] . BUTLER_MIN_CSS . '.css') )
						wp_enqueue_style('btr-meta-boxes' . $field['type'], butler_path_to_url($template_path) . 'assets/css/' . $field['type'] . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION);
					
					/* we enqueue our js */
					if ( file_exists($template_path . 'assets/js/' . $field['type'] . BUTLER_MIN_JS . '.js') )
						wp_enqueue_script('btr-meta-boxes' . $field['type'], butler_path_to_url($template_path) . 'assets/js/' . $field['type'] . BUTLER_MIN_JS . '.js', array('jquery'), BUTLER_VERSION);
										
				}
			
			}
			
		}

	}


	function add() {
	
		$this->args['context'] = empty($this->args['context']) ? 'normal' : $this->args['context'];
		
		$this->args['priority'] = empty($this->args['priority']) ? 'high' : $this->args['priority'];
				
		$pages = in_array('all', $this->args['pages']) ? butler_get_post_types(false, true) : $this->args['pages'];
		
		foreach ( $pages as $page )
			add_meta_box($this->args['id'], $this->args['title'], array($this, 'show'), $page, $this->args['context'], $this->args['priority']);

	}


	function show() {
	
		global $post;

		/* we use nonce for verification */
		echo '<input type="hidden" name="meta_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

		foreach ( $this->args['fields'] as $field ) {
		
			if ( isset($field['custom']) && $field['custom'] )
				$template_path = $field['custom'];
			else
				$template_path = BUTLER_PATH . 'admin/templates/meta-boxes/';
					
			if ( file_exists($template_path . $field['type'] . '.php') ) {
				
				/* we get current post meta data */
				$meta = get_post_meta($post->ID, $field['id'], true);
										
				if ( $field['name'] )
					echo '<label for="' . $field['id'] . '">' . $field['name'] . '</label>';
				
				echo butler_render($template_path . $field['type'] . '.php', array('meta' => $meta, 'field' => $field));
				
			}			
			
		}
		
	}
	

	function save($post_id) {
	
		/* we verify the nonce before proceeding */
		if ( !isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], basename(__FILE__)) )
			return $post_id;

		/* we verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything */
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return $post_id;

		/* we check permissions */
		if ( !current_user_can('edit_post', $post_id) )
			return $post_id;
			
		foreach ( $this->args['fields'] as $field ) {
		
			$name = $field['id'];

			$old = get_post_meta($post_id, $name, true);
			$new = $_POST[$field['id']];
			
			if ( $field['type'] == 'wysiwyg' )
				$new = wpautop($new);

			if ( $new && $new != $old )
				update_post_meta($post_id, $name, $new);
			
			elseif ( '' == $new && $old )
				delete_post_meta($post_id, $name, $old);

		}
		
	}
		
}