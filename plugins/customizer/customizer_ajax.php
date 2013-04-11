<?php
/**
 * @package Customizer
 * @version 0.6
 */
require_once('../../../wp-blog-header.php');

if ( !current_user_can( 'manage_options' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this awesome page.' ) );
}


function prefixCheck(){
	$customizer_options = get_option('customizer_options');
	//if serialize has been set under Options -> Customizer
	if ( $customizer_options['serialize'] == 'on' && $customizer_options['serialized_option'] ) {
		$prefix = $customizer_options['serialized_option'];
	} else {
		$prefix = 'undefined';
	}
	return $prefix;
}

/*
 *
 * Add Section & Control
 *
*/

function customizer_add_control($new_options){

	$options_control = array(
			'action'    => 'c',
			'id'        => $_REQUEST["sid"],
			'prefix1'   => prefixCheck(),
			'id1' 			=> $_REQUEST["cid"],
			'label1'		=> $_REQUEST["label"],
			'type1'			=> $_REQUEST["type"],
			'typeval1'	=> $_REQUEST["typeval"],
	);

	if ( $options_control['id'] && $options_control['id1'] && $options_control['type1'] && $options_control['id'] && $options_control['label1'] ){
		array_push( $new_options, $options_control );
		update_option( "customizer_array", $new_options );
	}
}

function customizer_add_section(){
	$options_section = array(
			'action' => 's',
			'id' => $_REQUEST["sid"],
			'title' => $_REQUEST["title"],
			'desc' => $_REQUEST["desc"],
			'priority' => $_REQUEST["priority"],
	);

	if ( $options_section['id'] && $options_section['title'] ){
		if ( get_option( "customizer_array" ) ){
			$new_options = get_option( "customizer_array" );
			array_push( $new_options, $options_section );
		} else { $new_options = array($options_section); }
	}
	customizer_add_control($new_options);
}

if ( $_REQUEST["customizer_action"] == "post_control" ){
	if (get_option( "customizer_array" )) { customizer_add_control(get_option( "customizer_array" )); } else {
		$a = array();
		customizer_add_control($a);
	}	  // here we have to check if it works if there's no array! Also not everything is being deleted.. check delete functions!

} elseif ( $_REQUEST["customizer_action"] == "post_section" ){
	customizer_add_section();
}

//Import from CSV Customizer PRO
if ( $_REQUEST["r"] == "csv" ) {

	require_once('customizer_import.php');

/* 																			-  -  -																		 */

//answer to addSection part 2
} elseif ( $_REQUEST["r"] == "addSection" ) {

	//content
	?>

		<div id="customizer_add_section_active">
			<!--<form id="customizer_add_section_form" action="">-->
				<ul class="customize-section-content">
					<li class="customize-control customize-control-text">
						<label for="customizer_add_section_ID"><span class="customize-control-title">Section ID</span>
							<input form="customizer_add_section_form" type="text" id="customizer_add_section_ID" name="customizer_add_section_ID" placeholder="unique ID" />
						</label>
					</li>
					<li class="customize-control customize-control-text">
						<label for="customizer_add_section_title"><span class="customize-control-title">Title</span>
							<input form="customizer_add_section_form" type="text" id="customizer_add_section_title" name="customizer_add_section_title" placeholder="Visible to users" />
						</label>
					</li>

          <li class="customize-control customize-control-text">
						<label for="customizer_add_section_description"><span class="customize-control-title">Description</span>
							<input form="customizer_add_section_form" type="text" id="customizer_add_section_description" name="customizer_add_section_description" placeholder="optional" />
						</label>
					</li>

					<li class="customize-control customize-control-text customize-control-last">
						<label for="customizer_add_section_priority"><span class="customize-control-title">Priority</span>
							<input form="customizer_add_section_form" type="number" id="customizer_add_section_priority" name="customizer_add_section_priority" placeholder="0 - 999" />
						</label>
					</li>
					<li class="customize-control customize-control-text">
						<h3 class='customizer-heading'>First Control Item</h3>
						<input form="customizer_add_section_form" type="hidden" value='prefixo' id='customizer_add_first_prefix' name='customizer_add_first_prefix' />
            <label for="customizer_add_first_ID"><span class="customize-control-title">Unique ID</span>
							<input form="customizer_add_section_form" type="text" id="customizer_add_first_id" name="customizer_add_first_id"  />
						</label>
					</li>
					<li class="customize-control customize-control-text">
						<label for="customizer_add_first_label"><span class="customize-control-title">Label</span>
							<input form="customizer_add_section_form" type="text" id="customizer_add_first_label" name="customizer_add_first_label" placeholder="Visible to users"   />
						</label>
					</li>
					<li class="customize-control customize-control-text">
						<label for="customizer_add_first_type"><span class="customize-control-title">Type</span>
							<select form="customizer_add_section_form" id="customizer_add_first_type" name="customizer_add_first_type">

                <option value="text">text</option>
                <option value="checkbox">checkbox</option>
                <option value="color">color</option>
                <option value="image">image</option>
                <option value="radio">radio</option>
                <option value="select">select</option>
                <!-- ///////////////////// TODO add more options!!!  \\\\\\\\\\\\\\\\\\\\\\\\\\\\ -->

							</select>
						</label>
            <div id='customizer_add_first_type_after'></div>
					</li>
				</ul>

				<input form="customizer_add_section_form" type="submit" value="Save Section" class="button-primary" id="customizer_submit_section_form">
				</input>
			<!--</form>-->
		</div>
	<?php

} elseif ( $_REQUEST["r"] == "removeSection" ) {

	/* We are removing a section here! */
 $options = get_option( "customizer_array" );
 $rid     = $_REQUEST["rid"];
 $nu_array = array();

 foreach ($options as $o) {
	if ($o["id"] != $rid ){
		$nu_array[] = $o;
	};
 }

 update_option('customizer_array', $nu_array);

} elseif ( $_REQUEST["r"] == "removeControl" ) {

	/* We are removing a section here! */
 $options = get_option( "customizer_array" );
 $rid     = $_REQUEST["rid"];
 $nu_array = array();

 foreach ($options as $o) {
	if (!$o["id1"] || $o["id1"] != $rid ){
		$nu_array[] = $o;
	};
 }
 update_option('customizer_array', $nu_array);

} else if ( $_REQUEST["r"] == "addControl" ) {
?>
 	<div id="customizer_add_control_active">
			<form id="customizer_add_control_form" action="">
  			<ul class="customize-section-content">
					<li class="customize-control customize-control-text">
						<h3 class='customizer-heading'>Add Control:</h3>
						<input type="hidden" value='prefixo' id='customizer_add_prefix' name='customizer_add_prefix'   />
            <label for="customizer_add_id"><span class="customize-control-title">Unique ID</span>
							<input type="text" id="customizer_add_id" name="customizer_add_id"   />
						</label>
					</li>
					<li class="customize-control customize-control-text">
						<label for="customizer_add_label"><span class="customize-control-title">Label</span>
							<input type="text" id="customizer_add_label" name="customizer_add_label" placeholder="Visible to users"   />
						</label>
					</li>
					<li class="customize-control customize-control-text">
						<label for="customizer_add_type"><span class="customize-control-title">Type</span>
							<select id="customizer_add_type" name="customizer_add_type">
                <option value="text">text</option>
                <option value="checkbox">checkbox</option>
                <option value="color">color</option>
                <option value="image">image</option>
                <option value="radio">radio</option>
                <option value="select">select</option>
							</select>
						</label>
            <div id='customizer_add_type_after'></div>
					</li>
        </ul>
        <input type="submit" value="Save Control" class="button-primary" id="customizer_submit_control_form">
				</input>
      </form>
  </div>
<?php
} elseif ( $_REQUEST["r"] == "reset" ) {
	delete_option('customizer_array');
} else if ( $_REQUEST["r"] == "suggest" ) {

	//Autocomplete array:

	$q = strtolower($_REQUEST["q"]);
	if (!$q) return;

	$ix = get_option('customizer_options');

	// Autosuggest will only work if we are using serialization
	if ($ix['serialize'] == 'on'){
		$ex = $ix['serialized_option'];
		$ax = get_option($ex);
	} else {
		return;
	}

	foreach ($ax as $key=>$value) {
		if (strpos(strtolower($key), $q) !== false) {
			echo "$key\n";
		}
	}

}	else {
	customizer_section_adder();
}
//places 'add new section' button
function customizer_section_adder() { ?>
<div id='customizer_section_adder'>
  <button id='customizer_add_section' class="button-header">Add Section</button>
</div>
<?php
}