<?php
/**
 * @package Customizer
 * @version 0.6
 */

/* TODO: Enable Customizer Pro featues */
/* $customizer_pro = true; */

//Customize Customizer Options Panel displayed under Settings in Admin Panel
add_action( 'admin_menu', 'customizer_menu' );
function customizer_menu() {
	add_options_page( 'Customizer Options', 'Customizer', 'manage_options', 'customizer_options_panel', 'customizer_options' );
}

function customizer_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this awesome page.' ) );
	}

	?>
  	<div class='wrap'>

    <?php //replace with Customizer logo ?>
    <div class="icon32" id="icon-themes"><br></div>

		<?php  echo "<h2>" . __( 'Customizer Plugin Settings', 'customizer' ) . "</h2>"; 	//heading	?>

    <form action="options.php" method="post">
		<?php settings_fields('customizer_options'); ?>
    <?php do_settings_sections('customizer'); ?>

    <p class='submit'>
    	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" class='button-primary' />
    </p>
    </form>

    </div><!--.wrap-->
  <?php
}

// add the admin settings and such
add_action('admin_init', 'customizer_admin_init');
wp_enqueue_style( 'customizer_css', plugins_url() . '/customizer/customizer.css' );
wp_enqueue_script( 'jquery_cookie', plugins_url() . '/customizer/jquery.cookie.js', array( 'jquery' ), '20120520', true );
wp_enqueue_script( 'customizer_js', plugins_url() . '/customizer/customizer.js', array( 'jquery' ), '20120520', true );
function customizer_admin_init(){
	global $customizer_pro;
	register_setting( 'customizer_options', 'customizer_options'/*, 'customizer_options_validate'*/ );
	add_settings_section('customizer_main', 'Main Settings', 'customizer_section_text', 'customizer');
	//add_settings_field('customizer_text_string', 'Customizer Text Input', 'customizer_setting_string', 'customizer', 'customizer_main');
	add_settings_field('customizer_is_disabled', 'Disable Customizer', 'customizer_is_disabled', 'customizer', 'customizer_main');
	add_settings_section('customizer_serialize', 'Serialization', 'customizer_serialize_text', 'customizer');
	add_settings_field('customizer_is_serialized', 'Use Serialization', 'customizer_is_serialized', 'customizer', 'customizer_serialize');
	add_settings_field('customizer_serialized_option', 'Serialized Options Group Name', 'customizer_serialized_option', 'customizer', 'customizer_serialize');
	if ($customizer_pro){
		add_settings_section('customizer_export', 'Export / Import', 'customizer_export_text', 'customizer');
		add_settings_field('customizer_import', 'Import from CSV', 'customizer_import', 'customizer', 'customizer_export');
		add_settings_field('customizer_export_section', 'Export to CSV', 'customizer_export_section', 'customizer', 'customizer_export');
	}
	add_settings_section('customizer_reset', 'Reset', 'customizer_reset_text', 'customizer');
	add_settings_field('customizer_reset_button', 'Remove Customizations', 'customizer_reset_button', 'customizer', 'customizer_reset');
}

//add label describing the field
function customizer_section_text() { ?>
	<p>Disable Customizer in the frontend (options in the database will remain intact).</p>
<?php
}
function customizer_serialize_text() { ?>
	<p>Check <em>Serialize Options</em> if you group your options in an array. Provide the <em>Options Group Name</em> in the field below.</p>
<?php
}
function customizer_export_text() { ?>
	<!--<p>Export &amp; Import requires Customizer Pro.</p>-->
<?php
}
function customizer_reset_text() { ?>
	<p>Sections and Controls added by <strong>Customizer</strong> will be removed. Created options will remain intact.</p>
<?php
}

//display Disable Customizer Checkbox
function customizer_is_disabled() {
	$options = get_option('customizer_options');
	?>
  	<input type='checkbox' name='customizer_options[disable_customizer]' id='customizer_is_disabled' <?php if( $options['disable_customizer'] == 'on' ) echo "checked='checked'"; ?> />
  <?php
}

function customizer_is_serialized() {
	$options = get_option('customizer_options');
	?>
  	<input type='checkbox' name='customizer_options[serialize]' id='customizer_is_serialized' <?php if( $options['serialize'] == 'on' ) echo "checked='checked'"; ?> />
  <?php
}

function customizer_serialized_option() {
	$options = get_option('customizer_options');
	?>
	<input id='customizer_serialized_option' name='customizer_options[serialized_option]' size='40' type='text' value='<?php echo $options['serialized_option']; ?>' />

  <!--<pre><?php //print_r($options); ?></pre>-->
	<?php
}

function customizer_import() {
	$options = get_option('customizer_options');
	?>

  <label for="upload_csv">
    <input id="upload_csv" type="text" size="36" name='customizer_options[csv_url]' value="<?php echo esc_textarea( $options['csv_url'] ); ?>" />
    <input id="upload_csv_button" type="button" value="Upload CSV" />
  </label>
  <br />
  	<div id='customizer_import_container'>
    	<p><a class='button-primary' id='customizer_csv_go'>Import Customizations from CSV</a></p>
    </div>
	<?php
}

function customizer_export_section(){?>
 	<p><a href='<?php echo plugins_url( "customizer" ); ?>/customizer_export.php' class='button'>Export Customizations to CSV</a></p>
<?php
}

function customizer_reset_button(){ ?>
	<p><a href='<?php echo plugins_url( "customizer" ); ?>/customizer_ajax.php?r=reset' class='button' id='customizer_reset_button'>Reset Customizer</a></p>
<?php
}

//display our form input text field
/*function customizer_setting_string() {
$options = get_option('customizer_options');?>
<pre><?php print_r($options); ?></pre>
<?php
echo "<input id='customizer_text_string' name='customizer_options[disable_customizer]' size='40' type='text' value='{$options[disable_customizer]}' />";
}*/

// validate our options !!! Specify correct values!!!
/*function customizer_options_validate($input) {
	$newinput['disable_customizer'] = trim($input['disable_customizer']);
	if(!preg_match('[a-z0-9]', $newinput['disable_customizer'])) {
		$newinput['disable_customizer'] = '';
	}
return $newinput;
}*/