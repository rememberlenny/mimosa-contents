<?php
require_once 'functions.php';

if ( !current_user_can( 'manage_options' ) ) {
	return;
}
if (isset($_REQUEST['Clear'])){
	ga_dash_clear_cache();
	?><div class="updated"><p><strong><?php _e('Cleared Cache.', 'ga-dash' ); ?></strong></p></div>  
	<?php
}
if (isset($_REQUEST['Reset'])){

	ga_dash_reset_token();
	?><div class="updated"><p><strong><?php _e('Token Reseted.', 'ga-dash'); ?></strong></p></div>  
	<?php
}else if(ga_dash_safe_get('ga_dash_hidden') == 'Y') {  
        //Form data sent  
        $apikey = ga_dash_safe_get('ga_dash_apikey');  
        update_option('ga_dash_apikey', sanitize_text_field($apikey));  
          
        $clientid = ga_dash_safe_get('ga_dash_clientid');  
        update_option('ga_dash_clientid', sanitize_text_field($clientid));  
          
        $clientsecret = ga_dash_safe_get('ga_dash_clientsecret');  
        update_option('ga_dash_clientsecret', sanitize_text_field($clientsecret));  

        $dashaccess = ga_dash_safe_get('ga_dash_access');  
        update_option('ga_dash_access', $dashaccess);
		
		$ga_dash_tableid_jail = ga_dash_safe_get('ga_dash_tableid_jail');  
        update_option('ga_dash_tableid_jail', $ga_dash_tableid_jail); 
		
		$ga_dash_pgd = ga_dash_safe_get('ga_dash_pgd');
		update_option('ga_dash_pgd', $ga_dash_pgd);

		$ga_dash_rd = ga_dash_safe_get('ga_dash_rd');
		update_option('ga_dash_rd', $ga_dash_rd);

		$ga_dash_sd = ga_dash_safe_get('ga_dash_sd');
		update_option('ga_dash_sd', $ga_dash_sd);		
		
		$ga_dash_map = ga_dash_safe_get('ga_dash_map');
		update_option('ga_dash_map', $ga_dash_map);
		
		$ga_dash_traffic = ga_dash_safe_get('ga_dash_traffic');
		update_option('ga_dash_traffic', $ga_dash_traffic);		

		$ga_dash_frontend = ga_dash_safe_get('ga_dash_frontend');
		update_option('ga_dash_frontend', $ga_dash_frontend);		
		
		$ga_dash_style = ga_dash_safe_get('ga_dash_style');
		update_option('ga_dash_style', $ga_dash_style);
		
		$ga_dash_jailadmins = ga_dash_safe_get('ga_dash_jailadmins');
		update_option('ga_dash_jailadmins', $ga_dash_jailadmins);
		
		$ga_dash_cachetime = ga_dash_safe_get('ga_dash_cachetime');
		update_option('ga_dash_cachetime', $ga_dash_cachetime);
		if (!isset($_REQUEST['Clear']) AND !isset($_REQUEST['Reset'])){
			?>  
			<div class="updated"><p><strong><?php _e('Options saved.', 'ga-dash'); ?></strong></p></div>  
			<?php
		}
    }else if(ga_dash_safe_get('ga_dash_hidden') == 'A') {
        $apikey = ga_dash_safe_get('ga_dash_apikey');  
        update_option('ga_dash_apikey', sanitize_text_field($apikey));  
          
        $clientid = ga_dash_safe_get('ga_dash_clientid');  
        update_option('ga_dash_clientid', sanitize_text_field($clientid));  
          
        $clientsecret = ga_dash_safe_get('ga_dash_clientsecret');  
        update_option('ga_dash_clientsecret', sanitize_text_field($clientsecret));  
	}
	
if (isset($_REQUEST['Authorize']) AND get_option('ga_dash_apikey') AND get_option('ga_dash_clientid') AND get_option('ga_dash_clientsecret')){
	$adminurl = admin_url("#ga-dash-widget");
	echo '<script> window.location="'.$adminurl.'"; </script> ';
}
else if (isset($_REQUEST['Authorize'])){
	?><div class="updated"><p><strong><?php _e('API Key, Client ID or Client Secret is missing.', 'ga-dash' ); ?></strong></p></div>  
	<?php
}
	
if(!get_option('ga_dash_access')){
	update_option('ga_dash_access', "manage_options");	
}

if(!get_option('ga_dash_style')){
	update_option('ga_dash_style', "blue");	
}

$apikey = get_option('ga_dash_apikey');  
$clientid = get_option('ga_dash_clientid');  
$clientsecret = get_option('ga_dash_clientsecret');  
$dashaccess = get_option('ga_dash_access'); 
$ga_dash_tableid_jail = get_option('ga_dash_tableid_jail');
$ga_dash_pgd = get_option('ga_dash_pgd');
$ga_dash_rd = get_option('ga_dash_rd');
$ga_dash_sd = get_option('ga_dash_sd');
$ga_dash_map = get_option('ga_dash_map');
$ga_dash_traffic = get_option('ga_dash_traffic');
$ga_dash_frontend = get_option('ga_dash_frontend');
$ga_dash_style = get_option('ga_dash_style');
$ga_dash_cachetime = get_option('ga_dash_cachetime');
$ga_dash_jailadmins = get_option('ga_dash_jailadmins');

?>  

<div class="wrap">  
    <?php echo "<h2>" . __( 'Google Analytics Dashboard Settings', 'ga_dash' ) . "</h2>"; ?>  
        <form name="ga_dash_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
        <hr />
		<?php echo "<h3><u>" . __( 'Google Analytics API', 'ga_dash' ). " (". __("watch this", 'ga-dash')." <a href='http://www.deconf.com/en/projects/google-analytics-dashboard-for-wordpress/' target='_blank'>". __("Step by step video tutorial")."</a>)"."</u></h3>"; ?>  
        <p><?php echo "<b>".__("API Key:", 'ga-dash')." </b>"; ?><input type="text" name="ga_dash_apikey" value="<?php echo $apikey; ?>" size="61"><?php echo "<i> ".__("ex: AIzaSyASK7dLaii4326AZVyZ6MCOIQOY6F30G_1", 'ga-dash')."</i>"; ?></p>  
        <p><?php echo "<b>".__("Client ID:", 'ga-dash')." </b>"; ?><input type="text" name="ga_dash_clientid" value="<?php echo $clientid; ?>" size="60"><?php echo "<i> ".__("ex: 111342334706.apps.googleusercontent.com", 'ga-dash')."</i>"; ?></p>  
        <p><?php echo "<b>".__("Client Secret:", 'ga-dash')." </b>"; ?><input type="text" name="ga_dash_clientsecret" value="<?php echo $clientsecret; ?>" size="55"><?php echo "<i> ".__("ex: c62POy23C_2qK5fd3fdsec2o", 'ga-dash')."</i>"; ?></p>  
		<p><?php 
			if (get_option('ga_dash_token')){
				echo "<input type=\"submit\" name=\"Reset\" class=\"button button-primary\" value=\"".__("Clear Authorization", 'ga-dash')."\" />";
				echo '<input type="hidden" name="ga_dash_hidden" value="Y">';  
			} else{
				echo "<input type=\"submit\" name=\"Authorize\" class=\"button button-primary\" value=\"".__("Authorize Application", 'ga-dash')."\" />";
				echo '<input type="hidden" name="ga_dash_hidden" value="A">';
				echo "</form>";
				_e("(the rest of the settings will show up after completing the authorization process)", 'ga-dash' );
				return;
			} ?>
		</p>  
		<hr />
		<?php echo "<h3><u>" . __( 'Access Level', 'ga_dash' ). "</u></h3>";?>
		<p><?php _e("View Access Level: ", 'ga-dash' ); ?>
		<select id="ga_dash_access" name="ga_dash_access">
			<option value="manage_options" <?php if (($dashaccess=="manage_options") OR (!$dashaccess)) echo "selected='yes'"; echo ">".__("Administrators", 'ga-dash');?></option>
			<option value="edit_pages" <?php if ($dashaccess=="edit_pages") echo "selected='yes'"; echo ">".__("Editors", 'ga-dash');?></option>
			<option value="publish_posts" <?php if ($dashaccess=="publish_posts") echo "selected='yes'"; echo ">".__("Authors", 'ga-dash');?></option>
			<option value="edit_posts" <?php if ($dashaccess=="edit_posts") echo "selected='yes'"; echo ">".__("Contributors", 'ga-dash');?></option>
		</select></p>

		<p><?php
		if (get_option('ga_dash_profile_list')){
			_e("Lock selected access level to this profile: ", 'ga-dash' );
			$profiles=get_option('ga_dash_profile_list');
			echo '<select id="ga_dash_tableid_jail" name="ga_dash_tableid_jail">';
			foreach ($profiles as $items) {
				if ($items[0]){
					if (!get_option('ga_dash_tableid_jail')) {
						update_option('ga_dash_tableid_jail',$items[1]);
					}
					echo '<option value="'.$items[1].'"'; 
					if ((get_option('ga_dash_tableid_jail')==$items[1])) echo "selected='yes'";
					echo '>'.$items[0].'</option>';
				}	
			}
			echo '</select>';
		
		}?></p>
		
		<p><input name="ga_dash_jailadmins" type="checkbox" id="ga_dash_jailadmins" value="1"<?php if (get_option('ga_dash_jailadmins')) echo " checked='checked'"; ?>  /><?php _e(" disable dashboard's Switch Profile functionality", 'ga-dash' ); ?></p>
		<hr />
		<?php echo "<h3><u>" . __( 'Frontend Settings', 'ga_dash' ). "</u></h3>";?>
		<p><input name="ga_dash_frontend" type="checkbox" id="ga_dash_frontend" value="1"<?php if (get_option('ga_dash_frontend')) echo " checked='checked'"; ?>  /><?php _e(" show page visits and top searches in frontend (after each article)", 'ga-dash' ); ?></p>
		<hr />
		<?php echo "<h3><u>" . __( 'Backend Settings', 'ga_dash' ). "</u></h3>";?>
		<p><input name="ga_dash_map" type="checkbox" id="ga_dash_map" value="1"<?php if (get_option('ga_dash_map')) echo " checked='checked'"; ?>  /><?php _e(" show geo map for visits", 'ga-dash' ); ?></p>
		<p><input name="ga_dash_traffic" type="checkbox" id="ga_dash_traffic" value="1"<?php if (get_option('ga_dash_traffic')) echo " checked='checked'"; ?>  /><?php _e(" show traffic overview", 'ga-dash' ); ?></p>
		<p><input name="ga_dash_pgd" type="checkbox" id="ga_dash_pgd" value="1"<?php if (get_option('ga_dash_pgd')) echo " checked='checked'"; ?>  /><?php _e(" show top pages", 'ga-dash' ); ?></p>
		<p><input name="ga_dash_rd" type="checkbox" id="ga_dash_rd" value="1"<?php if (get_option('ga_dash_rd')) echo " checked='checked'"; ?>  /><?php _e(" show top referrers", 'ga-dash' ); ?></p>		
		<p><input name="ga_dash_sd" type="checkbox" id="ga_dash_sd" value="1"<?php if (get_option('ga_dash_sd')) echo " checked='checked'"; ?>  /><?php _e(" show top searches", 'ga-dash' ); ?></p>		
		<p><?php _e("CSS Settings: ", 'ga-dash' ); ?>
		<select id="ga_dash_style" name="ga_dash_style">
			<option value="blue" <?php if ($ga_dash_style=="blue") echo "selected='yes'>".__("Blue Theme", 'ga-dash');?></option>
			<option value="light" <?php if ($ga_dash_style=="light") echo "selected='yes'>".__("Light Theme", 'ga-dash');?></option>
		</select></p>
		<hr />
		<?php echo "<h3><u>" . __( 'Cache Settings', 'ga_dash' ). "</u></h3>";?>
		<p><?php _e("Cache Time: ", 'ga-dash' ); ?>
		<select id="ga_dash_cachetime" name="ga_dash_cachetime">
			<option value="10" <?php if ($ga_dash_cachetime=="10") echo "selected='yes'>".__("None", 'ga-dash');?></option>
			<option value="900" <?php if ($ga_dash_cachetime=="900") echo "selected='yes'>".__("15 minutes", 'ga-dash');?></option>
			<option value="1800" <?php if (($ga_dash_cachetime=="1800") OR (!$ga_dash_cachetime)) echo "selected='yes'>".__("30 minutes", 'ga-dash');?></option>
			<option value="3600" <?php if ($ga_dash_cachetime=="3600") echo "selected='yes'>".__("1 hour", 'ga-dash');?></option>
		</select></p>		
		<p class="submit">  
        <input type="submit" name="Submit" class="button button-primary" value="<?php _e('Update Options', 'ga_dash' ) ?>" />
		<input type="submit" name="Clear" class="button button-primary" value="<?php _e('Clear Cache', 'ga_dash' ) ?>" />		
        </p>  
    </form>  
</div> 