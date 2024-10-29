<?php
/*
Plugin Name: App Links
Plugin URI: http://onlineboswachters.nl
Description: Facebook introduced App Links on F8 in april 2014. With this WordPress plugin you can add these App Links really easy.
Version: 0.2
Author: Online Boswachters
Author URI: http://onlineboswachters.nl
*/

/* Copyright 2014 Online Boswachters (email : info@onlineboswachters.nl) */

$pluginurl = plugin_dir_url(__FILE__);	
define( 'al_FRONT_URL', $pluginurl );
define( 'al_URL', plugin_dir_url(__FILE__) );
define( 'al_PATH', plugin_dir_path(__FILE__) );
define( 'al_BASENAME', plugin_basename( __FILE__ ) );
define( 'al_VERSION', '0.2' );

class app_links {
	
	function __construct() {
		
		$this->get_options();
		
		//TODO: set textdomain languages
		
		if (is_admin()) :
			$this->add_admin_includes();
			add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_scripts'), 11);
			add_action( 'admin_init', array( $this, 'options_init' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		else :
			add_action( 'wp_head', array( $this, 'add_app_links' ) );
		endif;
		
	}
	
	/* OPTIONS */
	
	/**
	 * Register the options needed for this plugins configuration pages.
	 */
	function options_init() {
		register_setting( 'app_links_settings', 'al_settings' );
	}
	
	/**
	 * Retrieve an option for the configuration page.
	 */
	function get_option($key = '') {
		if (!empty($this->options) && isset($this->options[$key])) {
			if (is_array($this->options)) :
				return $this->options[$key];
			else :
				return stripslashes($this->options[$key]);
			endif;
		}
		return false;
	}
	/**
	 * Retrieve all options for the configuration page from WP Options.
	 */
	function get_options() {
		if (isset($this->options)) return $this->options;
		if ($options = get_option('al_settings')) {
			if (is_array($options)) :
				$this->options = $options;
			else :
				$this->options = unserialize($options);	
			endif;
		}
	}
	
	/**
	 * Save all options to WP Options database
	 */
	function save_options() {
		if (!empty($this->options)) {
			update_option('al_settings', serialize($this->options));	
		}
	}
	
	/**
	 * Save a specifix option to WP Option database
	 */
	function save_option($key, $value, $save_to_db = false) {
		if (!empty($this->options)) {
			$this->options[$key] = $value;
		}
		if ($save_to_db == true) {
			$this->save_options();	
		}
	}
	
	/* INCLUDES */
	
	/**
	 * Include specific PHP files when visiting an admin page
	 */
	function add_admin_includes() {
		$includes = array('plugin-admin'); //add includes here that are in the includes fodler, without the .php
		$this->add_includes($includes);
	}
	
	/**
	 * Include specific PHP files when visiting a page on the website
	 */
	function add_includes($includes_new = array()) {
		$includes = array(); //add includes here that are in the includes fodler, without the .php
		if (is_array($includes_new)) $includes = $includes_new;
		if (!count($includes)) return false;
		foreach ($includes as $_include) :		
			$path = al_PATH.'includes/'.$_include.'.php';
			if (!file_exists($path)) continue;
			include_once($path);
		endforeach;
	}
	
	/* HELPERS */
	function enqueue_scripts() {
		wp_register_script('al-admin', plugins_url('/js/al-admin.js', __FILE__), false, '0.1', false);
		wp_enqueue_script('al-admin');
		wp_register_script('prettify', plugins_url('/js/google-code-prettify/run_prettify.js', __FILE__), false, '0.1', false);
		wp_enqueue_script('prettify');
	}
	
	function add_admin_menu() {
  		add_management_page( __( "App Links" ), __( "App Links" ), "administrator", 'al_settings', array( &$this, "al_settings" ) );
	}
	
	function get_app_links() {
		
		$app_links = array();
		$options = $this->get_options();
		foreach ($options as $_key => $_val) :
			if (strpos($_key,'app_links') === false) continue;
			$app_links[$_key] = $_val;
		endforeach;
		return $app_links;
		
	}
	
	function get_default_property_options() {
		return array(
			'iOS URL'=>'al:ios:url','iOS App Store ID'=>'al:ios:app_store_id','iOS App Name'=>'al:ios:app_name',
			'iPhone URL'=>'al:iphone:url','iPhone App Store ID'=>'al:iphone:app_store_id','iPhone App Name'=>'al:iphone:app_name',
			'iPad URL'=>'al:ipad:url','iPad App Store ID'=>'al:ipad:app_store_id','iPad App Name'=>'al:ipad:app_name',
			'Android URL'=>'al:android:url','Android Package'=>'al:android:package','Android Class'=>'al:android:class','Android App Name'=>'al:android:app_name',
			'Windows Phone URL'=>'al:windows_phone:url','Windows Phone App ID'=>'al:windows_phone:app_id','Windows Phone App Name'=>'al:windows_phone:app_name',
			'Fallback Web URL'=>'al:web:url','Web URL Should Fallback'=>'al:web:should_fallback'
		);	
	}
	
	function get_property_option($key) {
		$property_options = $this->get_default_property_options();
		if (!isset($property_options[$key])) return;
		return $property_options[$key];	
	}
	
	/* FRONTEND */
	function add_app_links() {
		
		$app_links = $this->get_option('app_links');
		if (!is_array($app_links) || (is_array($app_links) && !count($app_links))) return;
		$display 			= $this->get_option('display');
		if ($display != 'on') return;
		
		$output = "\t<!-- App Links by Online Boswachters v".al_VERSION." | http://onlineboswachters.nl -->\r\n";
		foreach ($app_links as $_key => $_link) :
			//add hooks for property and content
			$property 	= $_link['property'];
			$content 	= $_link['content'];
			if (empty($property)) :
			$select 	= $_link['select'];
			$option 	= $this->get_property_option($select);
			if (!empty($option)) $property = $option;
			endif;
					
			if (empty($property) || empty($content)) continue;
			$output .= "\t<meta property=\"".$property."\" content=\"".$content."\" />\r\n";
		endforeach;
	
		echo $output;
		
	}
	
	/* ADMIN */
	/**
	 * The settings page where you can edit the options of this plugin
	 */
	function al_settings() {
		
		global $table_prefix;
		global $plugin_admin;
		
		//TODO: if someone selects a special one, like al:ios:app_store_id, they need to add ios:url as well.
		//TODO: if someone selects app_store_id, the content needs to be an integer.
		
			$plugin_admin->admin_header(true, 'app_links_settings', 'al_settings');
			
			echo '<p>Here you can add as many App Links to your website as possible. Do you have a custom App Link, no worries, you can add a custom property and content as well.</p>';
			echo '<p>Do you want to know how to use App Links and what they can do for you? <a href="http://applinks.org/documentation/">Read official documentation</a></p>';			
			
			echo $plugin_admin->checkbox('display',__('Display your App Links on this website'));
			
			//generate example
			$content_meta = '';
			$content_app_links = '';
			
			$app_links = $this->get_option('app_links');
				
			$content_app_links = "
				<div id='app_links'>";
				
			if ($app_links) :
				
				//display links
				$i = 1;
				foreach ($app_links as $_key => $_link) :
				
					$property 	= $_link['property'];
					$content 	= $_link['content'];
					if (empty($property)) :
					$select 	= $_link['select'];
					$option 	= $this->get_property_option($select);
					if (!empty($option)) $property = $option;
					endif;
					
					$even = ($i % 2) ? '' : ' even' ;
				
					//$app_link_input = $plugin_admin->hidden('[app_links]['.$i.'][property]');
					//$app_link_input .= $plugin_admin->hidden('[app_links]['.$i.'][content]');
					$app_link_input = '<input type="hidden" id="app_links_'.$i.'_property" name="al_settings[app_links]['.$i.'][property]" value="'.$property.'"><input type="hidden" id="app_links_'.$i.'_property" name="al_settings[app_links]['.$i.'][content]" value="'.$content.'">';
					
					$content_app_links .= "
						<div class='row al_".$i.$even."'>
							<div class='app_links_property'><label class='app_links_property_label'>"._('Property').":</label><div class='app_links_value'>".$property."</div></div><div class='app_links_content'><label class='app_links_content_label'>"._('Content').":</label><div class='app_links_value'>".$content."</div></div>
						".$app_link_input."
							<a href='#' class='delete_app_link'>".__('Delete')."</a>
						</div>";
						
					$content_meta .= esc_html("<meta property=\"".$property."\" content=\"".$content."\" />\r\n");
						
					++$i;
				endforeach;
				
			endif;
			
			$content_app_links .= "
				</div>";
			
			//Add options like add app link
			$content_options = '
			<div class="app_links_options">
        	
				<!-- add app link -->
				<a href="#" class="button" id="add_app_link">'.__('Add App Link').'</a>
				
			</div>';
			
			$content_meta_output = "<div class='app_links_example'><h2>Current output in HTML</h2>\n\r";
			$content_meta_output .= "\t<pre class='prettyprint'><code class=''>".esc_html('<!-- App Links by Online Boswachters v'.al_VERSION.' | http://onlineboswachters.nl -->')."\r\n".$content_meta."</code></pre>\r\n";
			$content_meta_output .= "</div>\r\n";
			
			echo $content_meta_output;
			
			$content = $content_app_links . $content_options;
			
			$plugin_admin->postbox( 'al_settings', __( 'Your App Links', 'app_links' ), $content );
			
			
			$plugin_admin->admin_footer();
	}

}
$app_links = new app_links();
?>