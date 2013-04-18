<?php
/*
	Plugin Name: eHive Object Comments
	Plugin URI: http://developers.ehive.com/wordpress-plugins/
	Author: Vernon Systems limited
	Description: Display and add comments for eHive objects. The <a href="http://developers.ehive.com/wordpress-plugins#ehiveaccess" target="_blank">eHiveAccess plugin</a> must be installed. 
	Version: 2.1.1
	Author URI: http://vernonsystems.com
	License: GPL2+
*/
/*
	Copyright (C) 2012 Vernon Systems Limited

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if (in_array('ehive-access/EHiveAccess.php', (array) get_option('active_plugins', array()))) {

    class EHiveObjectComments {
        
        function __construct() {
        	
        	add_action("init", array(&$this, "init_sessions"), 1);

        	add_action("admin_init", array(&$this, "ehive_object_comments_admin_options_init"));
        	
            add_action("admin_menu", array(&$this, "ehive_object_comments_admin_menu"));
            
            add_action('wp_loaded', array(&$this, 'ehive_add_object_comment'));
            
            add_shortcode('ehive_object_comments', array(&$this, 'ehive_object_comments_shortcode'));
            
            add_action( 'wp_print_styles', array(&$this,'enqueue_styles'));            
            add_action( 'wp_print_scripts', array(&$this,'enqueue_scripts'));

        }

        public function init_sessions() {
        	if ( !isset($_SESSION ) ) {
        		session_start();
        	}
        }
        
        function ehive_object_comments_admin_options_init() {  
        	 
        	register_setting('ehive_object_comments_options', 'ehive_object_comments_options', array(&$this, 'plugin_options_validate') );
        	 
        	add_settings_section('comment_section', '', array(&$this, 'comment_section_fn'), __FILE__);
        
        	add_settings_section('object_comment_section', 'Comments', array(&$this, 'object_comment_section_fn'), __FILE__);
        	
        	add_settings_section('css_section', 'CSS - Stylesheet', array(&$this, 'css_section_fn'), __FILE__);
        	
        	add_settings_section('css_inline_section', 'CSS - inline', array(&$this, 'css_inline_section_fn'), __FILE__);
        	
        }
        
        /*
         * Validation
         */
        function plugin_options_validate($input) {
        	$input['name_placeholder'] =  wp_filter_nohtml_kses($input['name_placeholder']);
        	 
        	add_settings_error('ehive_object_comments_options', 'updated', 'eHive Object Comments settings saved.', 'updated');
        	 
        	return $input; // return validated input
        }
        
        /*
         * Plugin options content
         */
        function comment_section_fn() {
        	echo "<p><em>An overview of the plugin and shortcode documentation is available in the help.</em></p>";
        }
        
        function object_comment_section_fn() {
        	add_settings_field('commenting_enabled', 'Allow commenting', array(&$this, 'commenting_enabled_fn'), __FILE__, 'object_comment_section');
        	
        	add_settings_field('name_placeholder', 'Name placeholder text', array(&$this, 'name_placeholder_fn'), __FILE__, 'object_comment_section');
        	add_settings_field('name_required', 'Name required', array(&$this, 'name_required_fn'), __FILE__, 'object_comment_section');
        	
        	add_settings_field('email_placeholder', 'eMail placeholder text', array(&$this, 'email_placeholder_fn'), __FILE__, 'object_comment_section');
        	add_settings_field('email_address_required', 'eMail address required', array(&$this, 'email_address_required_fn'), __FILE__, 'object_comment_section');
        	
        	add_settings_field('comment_placeholder', 'Comment placeholder text', array(&$this, 'comment_placeholder_fn'), __FILE__, 'object_comment_section');
        	
        	add_settings_field('captcha_enabled', 'Captcha', array(&$this, 'captcha_enabled_fn'), __FILE__, 'object_comment_section');
        	add_settings_field('captcha_header_text', 'Captcha header text', array(&$this, 'captcha_header_text_fn'), __FILE__, 'object_comment_section');
        	add_settings_field('captcha_error_text', 'Captcha error text', array(&$this, 'captcha_error_text_fn'), __FILE__, 'object_comment_section');
        }
        
        function css_section_fn() {
        	add_settings_field('plugin_css_enabled', 'Enable plugin stylesheet', array(&$this, 'plugin_css_enabled_fn'), __FILE__, 'css_section');
        	add_settings_field('css_class', 'Custom class selector', array(&$this, 'css_class_fn'), __FILE__, 'css_section');
        }
        
        function css_inline_section_fn() {
        	add_settings_field('image_border_colour', 'Captcha image border colour', array(&$this, 'image_border_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('selected_image_border_colour', 'Selected Captcha image border colour', array(&$this, 'selected_image_border_colour_fn'), __FILE__, 'css_inline_section');
        }
        
        /**************************
         * OBJECT COMMENT OPTIONS *
        ***************************/
        function commenting_enabled_fn() {
        	$options = get_option('ehive_object_comments_options');
        	if(isset($options['commenting_enabled']) && $options['commenting_enabled'] == 'on') {
        		$checked = ' checked="checked" ';
        	}
        	echo "<input ".$checked." id='commenting_enabled' name='ehive_object_comments_options[commenting_enabled]' type='checkbox' />";
        }
         
        function name_placeholder_fn() {
        	$options = get_option('ehive_object_comments_options');
        	echo "<input class='regular-text' id='name_placeholder' name='ehive_object_comments_options[name_placeholder]' type='text' value='{$options['name_placeholder']}' />";
        }
        
        function name_required_fn() {
        	$options = get_option('ehive_object_comments_options');
			if(isset($options['name_required']) && $options['name_required'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input ".$checked." id='name_required' name='ehive_object_comments_options[name_required]' type='checkbox' />";
		}
        
		function email_placeholder_fn() {
			$options = get_option('ehive_object_comments_options');
			echo "<input class='regular-text' id='email_placeholder' name='ehive_object_comments_options[email_placeholder]' type='text' value='{$options['email_placeholder']}' />";
		}
        
		function email_address_required_fn() {
			$options = get_option('ehive_object_comments_options');
			if(isset($options['email_address_required']) && $options['email_address_required'] == 'on') {
        		$checked = ' checked="checked" ';
			}
			echo "<input ".$checked." id='email_address_required' name='ehive_object_comments_options[email_address_required]' type='checkbox' />";
		}
        
		function comment_placeholder_fn() {
			$options = get_option('ehive_object_comments_options');
			echo "<input class='regular-text' id='comment_placeholder' name='ehive_object_comments_options[comment_placeholder]' type='text' value='{$options['comment_placeholder']}' />";
		}
        
		function captcha_enabled_fn() {
			$options = get_option('ehive_object_comments_options');
			if(isset($options['captcha_enabled']) && $options['captcha_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input ".$checked." id='captcha_enabled' name='ehive_object_comments_options[captcha_enabled]' type='checkbox' />";
        }
        
		function captcha_header_text_fn() {
			$options = get_option('ehive_object_comments_options');
			echo "<input class='regular-text' id='captcha_header_text' name='ehive_object_comments_options[captcha_header_text]' type='text' value='{$options['captcha_header_text']}' />";
		}
        
		function captcha_error_text_fn() {
			$options = get_option('ehive_object_comments_options');
			echo "<input class='regular-text' id='captcha_error_text' name='ehive_object_comments_options[captcha_error_text]' type='text' value='{$options['captcha_error_text']}' />";
		}
         
        /***************
         * CSS SECTION *
         ***************/
        function plugin_css_enabled_fn() {
			$options = get_option('ehive_object_comments_options');
			if($options['plugin_css_enabled']) {
				$checked = ' checked="checked" ';
			}
			echo "<input ".$checked." id='plugin_css_enabled' name='ehive_object_comments_options[plugin_css_enabled]' type='checkbox' />";
		}
        
		function css_class_fn() {
			$options = get_option('ehive_object_comments_options');
			echo "<input class='regular-text' id='css_class' name='ehive_object_comments_options[css_class]' type='text' value='{$options['css_class']}' />";
		}
        
		/**********************
		 * CSS INLINE SECTION *
		 **********************/
		function image_border_colour_fn() {
			$options = get_option('ehive_object_comments_options');
			if(isset($options['image_border_colour_enabled']) && $options['image_border_colour_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
		
			echo "<input class='medium-text' id='image_border_colour' name='ehive_object_comments_options[image_border_colour]' type='text' value='{$options['image_border_colour']}' />";
			echo '<div id="image_border_colourpicker"></div>';
			echo "<td><input ".$checked." id='image_border_colour_enabled' name='ehive_object_comments_options[image_border_colour_enabled]' type='checkbox' /></td>";
		}
		
		function selected_image_border_colour_fn() {
			$options = get_option('ehive_object_comments_options');
			if(isset($options['selected_image_border_colour_enabled']) && $options['selected_image_border_colour_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
		
			echo "<input class='medium-text' id='selected_image_border_colour' name='ehive_object_comments_options[selected_image_border_colour]' type='text' value='{$options['selected_image_border_colour']}' />";
			echo '<div id="selected_image_border_colourpicker"></div>';
			echo "<td><input ".$checked." id='selected_image_border_colour_enabled' name='ehive_object_comments_options[selected_image_border_colour_enabled]' type='checkbox' /></td>";
		}
		
        /*
         * Admin menu setup
         */
		function ehive_object_comments_admin_menu() {
			global $ehive_object_comments_options_page;
		
			$ehive_object_comments_options_page = add_submenu_page('ehive_access', 'eHive object comments', 'Object Comments', 'manage_options', 'ehive_object_comments', array(&$this, 'ehive_object_comments_options_page'));
		
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'ehive_object_comments_plugin_action_links'), 10, 2);
		
			add_action("admin_print_styles-" . $ehive_object_comments_options_page, array(&$this, "ehive_object_comments_admin_enqueue_styles") );
		
			add_action("load-$ehive_object_comments_options_page",array(&$this, "ehive_object_comments_help"));
		}
		
		/*
		 * Options page setup
		 */
		function ehive_object_comments_options_page() {
			?>
        	<div class="wrap">
        		<div class="icon32" id="icon-options-ehive"><br></div>
        		<h2>eHive Object Comments Settings</h2>   
        		<?php settings_errors();?>     		
        		<form action="options.php" method="post">
        			<?php settings_fields('ehive_object_comments_options'); ?>
        			<?php do_settings_sections(__FILE__); ?>
        			<p class="submit">
        				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        			</p>
        		</form>
        	</div>
        	<?php
        }
        
        /*
         * Admin menu link
         */
        function ehive_object_comments_plugin_action_links($links, $file) {
        	$settings_link = '<a href="admin.php?page=ehive_object_comments">' . __('Settings') . '</a>';
        	array_unshift($links, $settings_link); // before other links
        	return $links;
        }
        
        /*
         * Admin stylesheet
         */
        function ehive_object_comments_admin_enqueue_styles() {
        	wp_enqueue_style('eHiveAdminCSS');
        }
        
        /*
         * Plugin options help
         */
        function ehive_object_comments_help() {
        	global $ehive_object_comments_options_page;
        	 
        	$screen = get_current_screen();
        	if ($screen->id != $ehive_object_comments_options_page)
        		return;
        	 
        	$screen->add_help_tab(array('id'      => 'ehive-object-comments-overview',
        			'title'   => 'Overview',
        			'content' => "<p>Allows the adding of comments on eHive objects",
        	));
        	 
        	$htmlShortcode = "<p><strong>Shortcode</strong> [ehive_object_comments]</p>";
        	$htmlShortcode.= "<p><strong>Attributes:</strong></p>";
        	$htmlShortcode.= "<ul>";
        	 
        	$htmlShortcode.= '<li><strong>css_class</strong> - Adds a custom class selector to the plugin markup.</li>';
        
        	$htmlShortcode.= '<li><strong>object_record_id</strong> - Allows adding of comments for the object record id. Attribute, a valid object record id.</li>';
        
        	$htmlShortcode.= '<p><strong>Examples:</strong></p>';
        	$htmlShortcode.= '<p>[ehive_object_comments]<br/>Shortcode with no attributes. Attributes default to the options settings.</p>';
        	$htmlShortcode.= '<p>[ehive_object_comments css_class="myClass"]<br/>Allows adding of comments with a custom class selector "myClass".</p>';
        	$htmlShortcode.= "</ul>";
        
        	$screen->add_help_tab(array('id'	  => 'ehive-object-comments-shortcode',
					        			'title'	  => 'Shortcode',
					        			'content' => $htmlShortcode
        	));
        
        	$screen->set_help_sidebar('<p><strong>For more information:</strong></p><p><a href="http://developers.ehive.com/wordpress-plugins#ehiveobjectcomments" target="_blank">Documentation for eHive plugins</a></p>');
        }
        
        /*
         * Add plugin stylesheet
         */
        public function enqueue_styles() {  
        	global $eHiveAccess;
        	      
        	$objectDetailPageId = $eHiveAccess->getObjectDetailsPageId();
        	
        	wp_dequeue_style( 'style' );
        	wp_deregister_style( 'style' );
        	
        	if (is_page( $objectDetailPageId )){    

        		$options = get_option('ehive_object_comments_options');        		
        		
        		if ($options['plugin_css_enabled'] == true) {
	        		wp_register_style($handle = 'eHiveObjectComments', $src = plugins_url('eHiveObjectComments.css', '/ehive-object-comments/css/eHiveObjectComments.css'));
	        		wp_enqueue_style( 'eHiveObjectComments');   
        		}
        		
        		if ($options['captcha_enabled'] == true) {
        			wp_register_style($handle = 'eHiveCaptcha', $src = plugins_url('eHiveCaptcha.css', '/ehive-object-comments/ehivecaptcha/css/eHiveCaptcha.css'));
        			wp_enqueue_style( 'eHiveCaptcha');
        		}
        	}
        }
        
        /*
         * Add plugin scripts
         */
        public function enqueue_scripts() {
			global $eHiveAccess;
        	
			$options = get_option('ehive_object_comments_options');
        	
        	$objectDetailPageId = $eHiveAccess->getObjectDetailsPageId();
        
        	if (is_page( $objectDetailPageId)){

				wp_enqueue_script( 'jquery' );
        		
				//wp_deregister_script( 'jquery' );
        		//wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
        		//wp_enqueue_script( 'jquery' );
        		        		
        		wp_enqueue_script( 'jquery-form' );
        		        		
        		wp_register_script($handle = 'eHiveObjectComments', $src= plugins_url('eHiveObjectComments.js', '/ehive-object-comments/js/eHiveObjectComments.js'), $deps = array('jquery', 'jquery-form'), $ver = '1.0.0', $media = 'all');
        		wp_enqueue_script( 'eHiveObjectComments' );
        		
        		if ($options['captcha_enabled']) {	        		
	        		wp_register_script($handle = 'eHiveCaptcha', $src= plugins_url('eHiveCaptcha.js', '/ehive-object-comments/ehivecaptcha/js/eHiveCaptcha.js'), $deps = array('jquery'), $ver = rand(), $media = 'all');
	        		wp_enqueue_script( 'eHiveCaptcha' );
        		}
        	}
        }

        
        /*
         * Plugin shortcode
         */
        function ehive_object_comments_shortcode($atts) {
        	
        	$options = get_option('ehive_object_comments_options');
        	
            extract(shortcode_atts(array('css_class'		=> array_key_exists('css_class', $options) ? $options['css_class'] : '',
            							 'object_record_id' => 0,
            							 'image_border_colour'					=> array_key_exists('image_border_colour', $options) ? $options['image_border_colour'] : '#cccccc',
										 'image_border_colour_enabled'			=> array_key_exists('image_border_colour_enabled', $options) ? $options['image_border_colour_enabled'] : 'on',
            							 'selected_image_border_colour'			=> array_key_exists('selected_image_border_colour', $options) ? $options['selected_image_border_colour'] : '#666666',
										 'selected_image_border_colour_enabled'	=> array_key_exists('selected_image_border_colour_enabled', $options) ? $options['selected_image_border_colour_enabled'] : 'on'), $atts));
            
            if ($object_record_id == 0) {
            	$object_record_id = ehive_get_var('ehive_object_record_id');
            }
			
			global $eHiveAccess;
            $eHiveApi = $eHiveAccess->eHiveApi();
            
			$commentsCollection = array();
			try {
	           	$commentsCollection = $eHiveApi->getObjectRecordComments($object_record_id, 0, 50);
	            
            } catch (Exception $exception) {
            	error_log('EHive Object comment plugin returned an error while trying to access the eHive API: ' . $exception->getMessage());
            	$eHiveApiErrorMessage = " ";
            	if ($eHiveAccess->getIsErrorNotificationEnabled()) {
            		$eHiveApiErrorMessage = $eHiveAccess->getErrorMessage();
            	}
            }
            
            $templateToFind = 'eHiveObjectComments.php' ;
            $template = locate_template(array($templateToFind));
            
            if ('' == $template) {
            	$template = "templates/{$templateToFind}";
            }
            
            ob_start();
            require($template);
            return apply_filters('ehive_object_comments', ob_get_clean());        
        }
        
        function ehive_add_object_comment() {
        	global $eHiveAccess;
        	$eHiveApi = $eHiveAccess->eHiveApi();
        	
            $add_object_comment = ehive_get_var('ehive-object-comment-form-submit');
            
            $object_record_id = ehive_get_var('ehive_object_record_id');

            if ($object_record_id && $add_object_comment) {

                $name = stripslashes_deep($_POST['ehive-object-comment-form-name-field']);
                $email = stripslashes_deep($_POST['ehive-object-comment-form-email-field']);
                $commentText = stripslashes_deep($_POST['ehive-object-comment-form-comment-text-field']);
                
                require_once  plugin_dir_path(__FILE__).'../ehive-access/ehive_api_client-php/domain/comments/Comment.php';

                $comment = new Comment();
                $comment->commentorName = $name;
                $comment->commentorEmailAddress = $email;
                $comment->commentText = $commentText;
                
            	$eHiveApi->addObjectRecordComment($object_record_id, $comment);
            	
            	// prevent resubmit of comment on browser refresh.
            	wp_redirect(  get_permalink() );
            }
        }
        
        /*
         * On plugin activate
         */
        public function activate() {        	
       		$arr = array("plugin_css_enabled"=>'on',
						 "css_class"=>'',
						 "commenting_enabled"=>'on',       					  
						 "name_required"=>'on', 
       					 "name_placeholder"=>"Your name",
       					 "email_address_required"=>'on',
       					 "email_placeholder"=>"Your eMail address",
       					 "comment_placeholder"=>"Your comment",
       					 "captcha_enabled"=>'on',
       					 "captcha_header_text"=>"Captcha",
       					 "captcha_error_text"=>"Only select the bees.",
						 "image_border_colour"=>"#cccccc",
						 "image_border_colour_enabled"=>true,
						 "selected_image_border_colour"=>"#666666",
						 "selected_image_border_colour_enabled"=>'on');
        		
       		update_option('ehive_object_comments_options', $arr);
        }
        
        /*
         * On plugin deactivate
         */
        public function deactivate() {        	
       		delete_option('ehive_object_comments_options');
        }        
    }

    $eHiveObjectComments = new EHiveObjectComments();
        
    add_action('activate_ehive-object-comments/EHiveObjectComments.php', array(&$eHiveObjectComments, 'activate'));
    add_action('deactivate_ehive-object-comments/EHiveObjectComments.php', array(&$eHiveObjectComments, 'deactivate'));
}
?>