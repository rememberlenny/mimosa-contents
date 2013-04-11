<?php
/*
Plugin Name: Advanced Custom Fields: Options Page
Plugin URI: http://www.advancedcustomfields.com/
Description: Adds the options page
Version: 1.0.1
Author: Elliot Condon
Author URI: http://www.elliotcondon.com/
License: GPL
Copyright: Elliot Condon
*/

class acf_options_page_plugin
{
	var $settings;
	
	
	/*
	*  Constructor
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function __construct()
	{
		// vars
		$this->settings = array(
			'title' => __('Options','acf'), // title / menu name ('Site Options')
			'capability' => 'edit_posts', // capability to view options page
			'pages' => array(), // an array of sub pages ('Header, Footer, Home, etc')
		);
		
		
		// create remote update
		if( is_admin() )
		{
			$update_settings = array(
				'version' => '1.0.1',
				'remote' => 'http://download.advancedcustomfields.com/OPN8-FA4J-Y2LW-81LS/info/',
				'basename' => plugin_basename(__FILE__),
			);
			
			if( !class_exists('acf_remote_update') )
			{
				include_once('acf-remote-update.php');
			}
			
			new acf_remote_update( $update_settings );
		}
		
		
		// actions
		add_action('init', array($this,'init'), 1 );
		add_action('admin_menu', array($this,'admin_menu'), 11, 0);
		
		
		// filters
		add_filter('acf/location/rule_types', array($this,'acf_location_rules_types'));
		add_filter('acf/location/rule_values/options_page', array($this,'acf_location_rules_values_options_page'));
	}
	
	
	/*
	*  init
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 12/02/13
	*/
	
	function init()
	{
		// filters
		$this->settings = apply_filters('acf/options_page/settings', $this->settings);
		
		
		if( isset($GLOBALS['acf_options_pages']) && !empty($GLOBALS['acf_options_pages']) )
		{
			$this->settings['pages'] = array_merge( $this->settings['pages'], $GLOBALS['acf_options_pages'] );
		}
	}
	
	
	/*
	*  acf_location_rules_types
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 2/02/13
	*/
	
	function acf_location_rules_types( $choices )
	{
	    $choices[ __("Options Page",'acf') ]['options_page'] = __("Options Page",'acf');
	 
	    return $choices;
	}
	
	
	/*
	*  acf_location_rules_values_options_page
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 2/02/13
	*/
	function acf_location_rules_values_options_page( $choices )
	{			
		$choices = array(
			'acf-options' => $this->settings['title']
		);
		
		$titles = $this->settings['pages'];
		if( !empty($titles) )
		{
			$choices = array();
			foreach( $titles as $title )
			{
				$slug = 'acf-options-' . sanitize_title( $title );
				$choices[ $slug ] = $title;
			}
		}
		
		
	    return $choices;
	}

	
	/*
	*  admin_menu
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 5/12/12
	*/
	
	function admin_menu() 
	{
		// vars
		$parent_slug = 'acf-options';
		$parent_title = $this->settings['title'];
		$parent_menu = $this->settings['title'];
		
		
		// redirect to first child
		if( !empty($this->settings['pages']) )
		{	
			$parent_title = $this->settings['pages'][0];
			$parent_slug = 'acf-options-' . sanitize_title( $parent_title );
		}
		
		
		// Parent
		$parent_page = add_menu_page($parent_title, $parent_menu, $this->settings['capability'], $parent_slug, array($this, 'html'));	
		
		
		// actions
		add_action('load-' . $parent_page, array($this,'admin_load'));
		
		
		
		if( !empty($this->settings['pages']) )
		{
			foreach($this->settings['pages'] as $c)
			{
				$sub_title = $c;
				$sub_slug = 'acf-options-' . sanitize_title( $sub_title );
				
				$child_page = add_submenu_page($parent_slug, $sub_title, $sub_title, $this->settings['capability'], $sub_slug, array($this, 'html'));
			
				
				// actions
				add_action('load-' . $child_page, array($this,'admin_load'));
			}
		}

	}
	
	
	/*
	*  load
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 2/02/13
	*/
	
	function admin_load()
	{
		add_action('admin_enqueue_scripts', array($this,'admin_enqueue_scripts'));
		add_action('admin_head', array($this,'admin_head'));
		add_action('admin_footer', array($this,'admin_footer'));
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  @description: run after post query but before any admin script / head actions. A good place to register all actions.
	*  @since: 3.6
	*  @created: 26/01/13
	*/
	
	function admin_enqueue_scripts()
	{
		// actions
		do_action('acf/input/admin_enqueue_scripts');
	}
	
	
	/*
	*  admin_head
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 5/12/12
	*/
	
	function admin_head()
	{	
	
		// verify nonce
		if( isset($_POST['acf_nonce']) && wp_verify_nonce($_POST['acf_nonce'], 'input') )
		{
			do_action('acf/save_post', 'options');
			
			$this->data['admin_message'] = __("Options Updated",'acf');
		}
		
		
		// get field groups
		$filter = array();
		$metabox_ids = array();
		$metabox_ids = apply_filters( 'acf/location/match_field_groups', $metabox_ids, $filter );

		
		if( empty($metabox_ids) )
		{
			$this->data['no_fields'] = true;
			return false;	
		}
		
		
		// Style
		echo '<style type="text/css">#side-sortables.empty-container { border: 0 none; }</style>';
		
		
		// add user js + css
		do_action('acf/input/admin_head');
		
		
		// get field groups
		$acfs = apply_filters('acf/get_field_groups', array());
		
		
		if( $acfs )
		{
			foreach( $acfs as $acf )
			{
				// load options
				$acf['options'] = apply_filters('acf/field_group/get_options', array(), $acf['id']);
				
				
				// vars
				$show = in_array( $acf['id'], $metabox_ids ) ? 1 : 0;
				
				if( !$show )
				{
					continue;
				}
				
				
				// add meta box
				add_meta_box(
					'acf_' . $acf['id'], 
					$acf['title'], 
					array($this, 'meta_box_input'), 
					'acf_options_page',
					$acf['options']['position'], 
					'high',
					array( 'field_group' => $acf, 'show' => $show, 'post_id' => 'options' )
				);
				
			}
			// foreach($acfs as $acf)
		}
		// if($acfs)
		
	}
	
	
	/*
	*  meta_box_input
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function meta_box_input( $post, $args )
	{
		// vars
		$options = $args['args'];
		
		
		echo '<div class="options" data-layout="' . $options['field_group']['options']['layout'] . '" data-show="' . $options['show'] . '" style="display:none"></div>';
		
		$fields = apply_filters('acf/field_group/get_fields', array(), $options['field_group']['id']);
					
		do_action('acf/create_fields', $fields, $options['post_id']);
		
	}
	
	
	/*
	*  admin_footer
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 5/12/12
	*/
	
	function admin_footer()
	{
		// add togle open / close postbox
		?>
		<script type="text/javascript">
		(function($){
			
			$('.postbox .handlediv').live('click', function(){
				
				var postbox = $(this).closest('.postbox');
				
				if( postbox.hasClass('closed') )
				{
					postbox.removeClass('closed');
				}
				else
				{
					postbox.addClass('closed');
				}
				
			});
			
		})(jQuery);
		</script>
		<?php
	}
	
	
	/*
	*  html
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 5/12/12
	*/
	
	function html()
	{
		?>
		<div class="wrap no_move">
		
			<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php echo get_admin_page_title(); ?></h2>
			
			<?php if(isset($this->data['admin_message'])): ?>
			<div id="message" class="updated"><p><?php echo $this->data['admin_message']; ?></p></div>
			<?php endif; ?>
			
			<?php if(isset($this->data['no_fields'])): ?>
			<div id="message" class="updated"><p><?php _e("No Custom Field Group found for the options page",'acf'); ?>. <a href="<?php echo admin_url(); ?>post-new.php?post_type=acf"><?php _e("Create a Custom Field Group",'acf'); ?></a></p></div>
			<?php else: ?>
			
			<form id="post" method="post" name="post">
			<div class="metabox-holder has-right-sidebar" id="poststuff">
				
				<!-- Sidebar -->
				<div class="inner-sidebar" id="side-info-column">
					
					<!-- Update -->
					<div class="postbox">
						<h3 class="hndle"><span><?php _e("Publish",'acf'); ?></span></h3>
						<div class="inside">
							<input type="hidden" name="HTTP_REFERER" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" />
							<input type="hidden" name="acf_nonce" value="<?php echo wp_create_nonce( 'input' ); ?>" />
							<input type="submit" class="acf-button" value="<?php _e("Save Options",'acf'); ?>" />
						</div>
					</div>
					
					<?php $meta_boxes = do_meta_boxes('acf_options_page', 'side', null); ?>
					
				</div>
					
				<!-- Main -->
				<div id="post-body">
				<div id="post-body-content">
					<?php $meta_boxes = do_meta_boxes('acf_options_page', 'normal', null); ?>
					<script type="text/javascript">
					(function($){
					
						$('#poststuff .postbox[id*="acf_"]').addClass('acf_postbox');

					})(jQuery);
					</script>
				</div>
				</div>
			
			</div>
			</form>
			
			<?php endif; ?>
		
		</div>
		
		<?php
				
	}
	
	
}

new acf_options_page_plugin();


/*
*  register_options_page()
*
*  This function is used to register an options page
*
*  @type	function
*  @since	3.0.0
*  @date	29/01/13
*
*  @param	$title - the page title
*
*  @return
*/


if( !function_exists('register_options_page') )
{

$GLOBALS['acf_options_pages'] = array();

function register_options_page( $title = "" )
{
	$GLOBALS['acf_options_pages'][] = $title;
}

}


?>
