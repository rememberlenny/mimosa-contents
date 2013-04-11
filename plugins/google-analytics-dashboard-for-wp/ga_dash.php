<?php
/* 
Plugin Name: Google Analytics Dashboard for WP
Plugin URI: http://www.deconf.com
Description: This plugin will display Google Analytics data and statistics into Admin Dashboard. 
Author: Deconf.com
Version: 3.3
Author URI: http://www.deconf.com
*/  

function ga_dash_admin() {  
    include('ga_dash_admin.php');  
} 
	
function ga_dash_admin_actions() {
	if (current_user_can('manage_options')) {  
		add_options_page("Google Analytics Dashboard", "GA Dashboard", "manage_options", "Google_Analytics_Dashboard", "ga_dash_admin");

	}
}  


add_filter('the_content', 'ga_dash_front_content');  
add_action('wp_dashboard_setup', 'ga_dash_setup');
add_action('admin_menu', 'ga_dash_admin_actions'); 
add_action('admin_enqueue_scripts', 'ga_dash_admin_enqueue_scripts');
add_action('plugins_loaded', 'ga_dash_init');

function ga_dash_front_content($content) {
	global $post;
	if (!current_user_can(get_option('ga_dash_access')) OR !get_option('ga_dash_frontend')) {
		return $content;
	}
	if(!is_feed() && !is_home()) {
	
		require_once 'functions.php';
		
		if(!get_option('ga_dash_cachetime')){
			update_option('ga_dash_cachetime', "900");	
		}

		if (!class_exists('Google_Exception')) {
			require_once 'src/Google_Client.php';
		}
			
		require_once 'src/contrib/Google_AnalyticsService.php';
		
		$scriptUri = "http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];

		$client = new Google_Client();
		$client->setAccessType('offline'); 
		$client->setApplicationName('GA Dashboard');
		$client->setClientId(get_option('ga_dash_clientid'));
		$client->setClientSecret(get_option('ga_dash_clientsecret'));
		$client->setRedirectUri($scriptUri);
		$client->setDeveloperKey(get_option('ga_dash_APIKEY'));
		
		if ((!get_option('ga_dash_clientid')) OR (!get_option('ga_dash_clientsecret')) OR (!get_option('ga_dash_apikey'))){
			return $content;
		}	
		
		$service = new Google_AnalyticsService($client);

		if (ga_dash_get_token()) { 
			$token = ga_dash_get_token();
			$client->setAccessToken($token);
		}else{
			return $content;
		}		
		
		$from = date('Y-m-d', time()-30*24*60*60);
		$to = date('Y-m-d');		
		$metrics = 'ga:visits';
		$dimensions = 'ga:year,ga:month,ga:day';
		$page_url = $_SERVER["REQUEST_URI"];
		//echo $page_url;
		$post_id = $post->ID;
		$title = "Visits";
		if (get_option('ga_dash_style')=="light"){ 
			$css="colors:['gray','darkgray'],";
			$colors="black";
		} else{
			$css="";
			$colors="blue";
		}		

		if (current_user_can('manage_options')) { 
			if (get_option('ga_dash_jailadmins')){
				if (get_option('ga_dash_tableid_jail')){
					$projectId = get_option('ga_dash_tableid_jail');
				}else{
					//_e("Ask an admin to asign a Google Analytics Profile", 'ga-dash');
					return $content;
				}
			}else{

				$projectId = get_option('ga_dash_tableid');
			}	
		} else{
			if (get_option('ga_dash_tableid_jail')){
				$projectId = get_option('ga_dash_tableid_jail');
			}else{
				//_e("Ask an admin to asign a Google Analytics Profile", 'ga-dash');
				return $content;
			}	
		}		
		
		try{
			$serial='gadash_qr21'.$post_id.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to.$metrics);
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$data = $service->data_ga->get('ga:'.$projectId, $from, $to, $metrics, array('dimensions' => $dimensions,'filters' => 'ga:pagePath=='.$page_url));
				set_transient( $serial, $data, get_option('ga_dash_cachetime') );
			}else{
				$data = $transient;		
			}	
		}  
			catch(exception $e) {
			return $content;
		}
		if (!$data['rows']){
			return $content;
		}
		
		$ga_dash_statsdata="";
		for ($i=0;$i<$data['totalResults'];$i++){
			$ga_dash_statsdata.="['".$data['rows'][$i][0]."-".$data['rows'][$i][1]."-".$data['rows'][$i][2]."',".round($data['rows'][$i][3],2)."],";
		}
		
		$metrics = 'ga:visits'; 
		$dimensions = 'ga:keyword';
		try{
			$serial='gadash_qr22'.$post_id.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to);
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$data = $service->data_ga->get('ga:'.$projectId, $from, $to, $metrics, array('dimensions' => $dimensions, 'sort' => '-ga:visits', 'max-results' => '24', 'filters' => 'ga:keyword!=(not provided);ga:keyword!=(not set);ga:pagePath=='.$page_url));
				set_transient( $serial, $data, get_option('ga_dash_cachetime') );
			}else{
				$data = $transient;		
			}			
		}  
			catch(exception $e) {
			return $content; 
		}	

		$ga_dash_organicdata="";
		if (isset($data['rows'])){
			$i=0;
			while (isset($data['rows'][$i][0])){
				$ga_dash_organicdata.="['".str_replace("'"," ",$data['rows'][$i][0])."',".$data['rows'][$i][1]."],";
				$i++;
			}		
		
		}	

		$content.='<style>
		#ga_dash_sdata td{
			line-height:1.5em;
			padding:2px;
			font-size:1em;
		}
		#ga_dash_sdata{
			line-height:10px;
		}
		</style>';
		
		$content.='<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
		  google.load("visualization", "1", {packages:["corechart"]});
		  google.setOnLoadCallback(ga_dash_callback);

		  function ga_dash_callback(){
				ga_dash_drawstats();
				if(typeof ga_dash_drawsd == "function"){
					ga_dash_drawsd();
				}				
		  }	

		  function ga_dash_drawstats() {
			var data = google.visualization.arrayToDataTable(['."
			  ['".__("Date", 'ga-dash')."', '".$title."'],"
			  .$ga_dash_statsdata.
			"  
			]);

			var options = {
			  legend: {position: 'none'},	
			  pointSize: 3,".$css."
			  title: '".$title."',
			  chartArea: {width: '85%'},
			  hAxis: { showTextEvery: 5}
			};

			var chart = new google.visualization.AreaChart(document.getElementById('ga_dash_statsdata'));
			chart.draw(data, options);
			
			}";
		if ($ga_dash_organicdata){
			$content.='
					google.load("visualization", "1", {packages:["table"]})
					function ga_dash_drawsd() {
					
					var datas = google.visualization.arrayToDataTable(['."
					  ['".__("Top Searches",'ga-dash')."', '".__("Visits",'ga-dash')."'],"
					  .$ga_dash_organicdata.
					"  
					]);
					
					var options = {
						page: 'enable',
						pageSize: 6,
						width: '100%',
					};        
					
					var chart = new google.visualization.Table(document.getElementById('ga_dash_sdata'));
					chart.draw(datas, options);
					
				  }";
		}
		  $content.="</script>";		
		
		
		$content .= '<div id="ga_dash_statsdata"></div><div id="ga_dash_sdata" ></div>';
	}
	return $content;
}

function ga_dash_init() {
  	load_plugin_textdomain( 'ga-dash', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

function ga_dash_admin_enqueue_scripts() {
	if (get_option('ga_dash_style')=="blue"){
		wp_register_style( 'ga_dash', plugins_url('ga_dash.css', __FILE__) );
		wp_enqueue_style( 'ga_dash' );
	} else{
		wp_register_style( 'ga_dash', plugins_url('ga_dash_light.css', __FILE__) );
		wp_enqueue_style( 'ga_dash' );
	}	
}

function ga_dash_setup() {
	if (current_user_can(get_option('ga_dash_access'))) {
		wp_add_dashboard_widget(
			'ga-dash-widget',
			'Google Analytics Dashboard',
			'ga_dash_content',
			$control_callback = null
		);
	}
}

function ga_dash_content() {
	
	require_once 'functions.php';
	
	if(!get_option('ga_dash_cachetime')){
		update_option('ga_dash_cachetime', "900");	
	}

	if (!class_exists('Google_Exception')) {
		require_once 'src/Google_Client.php';
	}
		
	require_once 'src/contrib/Google_AnalyticsService.php';
	
	$scriptUri = "http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];

	$client = new Google_Client();
	$client->setAccessType('offline');
	$client->setApplicationName('GA Dashboard');
	$client->setClientId(get_option('ga_dash_clientid'));
	$client->setClientSecret(get_option('ga_dash_clientsecret'));
	$client->setRedirectUri($scriptUri);
	$client->setDeveloperKey(get_option('ga_dash_APIKEY'));

	if ((!get_option('ga_dash_clientid')) OR (!get_option('ga_dash_clientsecret')) OR (!get_option('ga_dash_apikey'))){
		
		echo "<div style='padding:20px;'>".__("Client ID, Client Secret or API Key is missing", 'ga-dash')."</div>";
		return;
		
	}	
	$service = new Google_AnalyticsService($client);

	if (isset($_GET['code']) AND !(ga_dash_get_token())) {
		$client->authenticate();
		ga_dash_store_token($client->getAccessToken());

	}

	if (ga_dash_get_token()) { 
		$token = ga_dash_get_token();
		$client->setAccessToken($token);
	}

	if (!$client->getAccessToken()) {
		
		$authUrl = $client->createAuthUrl();
		
		if (!isset($_REQUEST['authorize'])){
			if (!current_user_can('manage_options')){
				_e("Ask an admin to authorize this Application", 'ga-dash');
				return;
			}
			echo '<div style="padding:20px;"><form name="input" action="#" method="get">
			<input type="submit" class="button button-primary" name="authorize" value="'.__("Authorize Google Analytics Dashboard", 'ga-dash').'"/>
		</form></div>';
			return;
		}		
		else{
			echo '<script> window.location="'.$authUrl.'"; </script> ';
			return;
		}

	}
	
	if (current_user_can('manage_options')) {
	
		if (isset($_REQUEST['ga_dash_profiles'])){ 
			update_option('ga_dash_tableid',$_REQUEST['ga_dash_profiles']);
		}	

		try {
			$client->setUseObjects(true);
			$profile_switch="";
			$serial='gadash_qr1';
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$profiles = $service->management_profiles->listManagementProfiles('~all','~all');
				set_transient( $serial, $profiles, get_option('ga_dash_cachetime') );
			}else{
				$profiles = $transient;		
			}
			$items = $profiles->getItems();
			$profile_switch.= '<form><select id="ga_dash_profiles" name="ga_dash_profiles" onchange="this.form.submit()">';
			
			if (count($items) != 0) {
				$ga_dash_profile_list="";
				foreach ($items as &$profile) {
					if (!get_option('ga_dash_tableid')) {
						update_option('ga_dash_tableid',$profile->getId());
					}
					$profile_switch.= '<option value="'.$profile->getId().'"'; 
					if ((get_option('ga_dash_tableid')==$profile->getId())) $profile_switch.= "selected='yes'";
					$profile_switch.= '>'.$profile->getName().'</option>';
					$ga_dash_profile_list[]=array($profile->getName(),$profile->getId());
				}
				update_option('ga_dash_profile_list',$ga_dash_profile_list);
			}
			$profile_switch.= "</select></form><br />";
			$client->setUseObjects(false);
		} catch (exception $e) {
			echo "<div style='padding:20px;'>".__("Can't retrive your Google Analytics Profiles", 'ga-dash')."</div>";
			return;
		}
	}
	if (current_user_can('manage_options')) { 
		if (get_option('ga_dash_jailadmins')){
			if (get_option('ga_dash_tableid_jail')){
				$projectId = get_option('ga_dash_tableid_jail');
			}else{
				_e("Ask an admin to asign a Google Analytics Profile", 'ga-dash');
				return;
			}
		}else{
			echo $profile_switch;
			$projectId = get_option('ga_dash_tableid');
		}	
	} else{
		if (get_option('ga_dash_tableid_jail')){
			$projectId = get_option('ga_dash_tableid_jail');
		}else{
			_e("Ask an admin to asign a Google Analytics Profile", 'ga-dash');
			return;
		}	
	}
	
	if(isset($_REQUEST['query']))
		$query = $_REQUEST['query'];
	else	
		$query = "visits";
		
	if(isset($_REQUEST['period']))	
		$period = $_REQUEST['period'];
	else
		$period = "last30days"; 	

	switch ($period){

		case 'today'	:	$from = date('Y-m-d'); 
							$to = date('Y-m-d');
							break;

		case 'yesterday'	:	$from = date('Y-m-d', time()-24*60*60);
								$to = date('Y-m-d', time()-24*60*60);
								break;
		
		case 'last7days'	:	$from = date('Y-m-d', time()-7*24*60*60);
							$to = date('Y-m-d');
							break;	

		case 'last14days'	:	$from = date('Y-m-d', time()-14*24*60*60);
							$to = date('Y-m-d');
							break;	
							
		default	:	$from = date('Y-m-d', time()-30*24*60*60);
					$to = date('Y-m-d');
					break;

	}

	switch ($query){

		case 'visitors'	:	$title=__("Visitors",'ga-dash'); break;

		case 'pageviews'	:	$title=__("Page Views",'ga-dash'); break;
		
		case 'visitBounceRate'	:	$title=__("Bounce Rate",'ga-dash'); break;	

		case 'organicSearches'	:	$title=__("Organic Searches",'ga-dash'); break;
		
		default	:	$title=__("Visits",'ga-dash');

	}

	$metrics = 'ga:'.$query;
	$dimensions = 'ga:year,ga:month,ga:day';

	try{
		$serial='gadash_qr2'.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to.$metrics);
		$transient = get_transient($serial);
		if ( empty( $transient ) ){
			$data = $service->data_ga->get('ga:'.$projectId, $from, $to, $metrics, array('dimensions' => $dimensions));
			set_transient( $serial, $data, get_option('ga_dash_cachetime') );
		}else{
			$data = $transient;		
		}	
	}  
		catch(exception $e) {
		echo "<br />".__("ERROR LOG:")."<br /><br />".$e; 
	}
	$ga_dash_statsdata="";
	for ($i=0;$i<$data['totalResults'];$i++){
		$ga_dash_statsdata.="['".$data['rows'][$i][0]."-".$data['rows'][$i][1]."-".$data['rows'][$i][2]."',".round($data['rows'][$i][3],2)."],";
	}

	$metrics = 'ga:visits,ga:visitors,ga:pageviews,ga:visitBounceRate,ga:organicSearches,ga:timeOnSite';
	$dimensions = 'ga:year';
	try{
		$serial='gadash_qr3'.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to);
		$transient = get_transient($serial);
		if ( empty( $transient ) ){
			$data = $service->data_ga->get('ga:'.$projectId, $from, $to, $metrics, array('dimensions' => $dimensions));
			set_transient( $serial, $data, get_option('ga_dash_cachetime') );
		}else{
			$data = $transient;		
		}	
	}  
		catch(exception $e) {
		echo "<br />".__("ERROR LOG:")."<br /><br />".$e; 
	}
	
	if (get_option('ga_dash_style')=="light"){ 
		$css="colors:['gray','darkgray'],";
		$colors="black";
	} else{
		$css="";
		$colors="blue";
	}
	
    $code='<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(ga_dash_callback);

	  function ga_dash_callback(){
			ga_dash_drawstats();
			if(typeof ga_dash_drawmap == "function"){
				ga_dash_drawmap();
			}
			if(typeof ga_dash_drawpgd == "function"){
				ga_dash_drawpgd();
			}			
			if(typeof ga_dash_drawrd == "function"){
				ga_dash_drawrd();
			}
			if(typeof ga_dash_drawsd == "function"){
				ga_dash_drawsd();
			}
			if(typeof ga_dash_drawtraffic == "function"){
				ga_dash_drawtraffic();
			}			
	  }	

      function ga_dash_drawstats() {
        var data = google.visualization.arrayToDataTable(['."
          ['".__("Date", 'ga-dash')."', '".$title."'],"
		  .$ga_dash_statsdata.
		"  
        ]);

        var options = {
		  legend: {position: 'none'},	
		  pointSize: 3,".$css."
          title: '".$title."',
		  chartArea: {width: '85%'},
          hAxis: { title: '".__("Date",'ga-dash')."',  titleTextStyle: {color: '".$colors."'}, showTextEvery: 5}
		};

        var chart = new google.visualization.AreaChart(document.getElementById('ga_dash_statsdata'));
		chart.draw(data, options);
		
      }";
	if (get_option('ga_dash_map')){
		$ga_dash_visits_country=ga_dash_visits_country($service, $projectId, $from, $to);
		if ($ga_dash_visits_country){
		 $code.='
			google.load("visualization", "1", {packages:["geochart"]})
			function ga_dash_drawmap() {
			var data = google.visualization.arrayToDataTable(['."
			  ['".__("Country",'ga-dash')."', '".__("Visits",'ga-dash')."'],"
			  .$ga_dash_visits_country.
			"  
			]);
			
			var options = {
				colors: ['white', '".$colors."']
			};
			
			var chart = new google.visualization.GeoChart(document.getElementById('ga_dash_mapdata'));
			chart.draw(data, options);
			
		  }";
		}
	}
	if (get_option('ga_dash_traffic')){
		$ga_dash_traffic_sources=ga_dash_traffic_sources($service, $projectId, $from, $to);
		$ga_dash_new_return=ga_dash_new_return($service, $projectId, $from, $to);
		if ($ga_dash_traffic_sources AND $ga_dash_new_return){
		 $code.='
			google.load("visualization", "1", {packages:["corechart"]})
			function ga_dash_drawtraffic() {
			var data = google.visualization.arrayToDataTable(['."
			  ['".__("Source",'ga-dash')."', '".__("Visits",'ga-dash')."'],"
			  .$ga_dash_traffic_sources.
			'  
			]);

			var datanvr = google.visualization.arrayToDataTable(['."
			  ['".__("Type",'ga-dash')."', '".__("Visits",'ga-dash')."'],"
			  .$ga_dash_new_return.
			"  
			]);
			
			var chart = new google.visualization.PieChart(document.getElementById('ga_dash_trafficdata'));
			chart.draw(data, {
				is3D: true,
				tooltipText: 'percentage',
				legend: 'none',
				title: '".__("Traffic Sources",'ga-dash')."'
			});
			
			var chart1 = new google.visualization.PieChart(document.getElementById('ga_dash_nvrdata'));
			chart1.draw(datanvr,  {
				is3D: true,
				tooltipText: 'percentage',
				legend: 'none',
				title: '".__("New vs. Returning",'ga-dash')."'
			});
			
		  }";
		}
	}	
	if (get_option('ga_dash_pgd')){
		$ga_dash_top_pages=ga_dash_top_pages($service, $projectId, $from, $to);
		if ($ga_dash_top_pages){
		 $code.='
			google.load("visualization", "1", {packages:["table"]})
			function ga_dash_drawpgd() {
			var data = google.visualization.arrayToDataTable(['."
			  ['".__("Top Pages",'ga-dash')."', '".__("Visits",'ga-dash')."'],"
			  .$ga_dash_top_pages.
			"  
			]);
			
			var options = {
				page: 'enable',
				pageSize: 6,
				width: '100%'
			};        
			
			var chart = new google.visualization.Table(document.getElementById('ga_dash_pgddata'));
			chart.draw(data, options);
			
		  }";
		}
	}
	if (get_option('ga_dash_rd')){
		$ga_dash_top_referrers=ga_dash_top_referrers($service, $projectId, $from, $to);
		if ($ga_dash_top_referrers){
		 $code.='
			google.load("visualization", "1", {packages:["table"]})
			function ga_dash_drawrd() {
			var datar = google.visualization.arrayToDataTable(['."
			  ['".__("Top Referrers",'ga-dash')."', '".__("Visits",'ga-dash')."'],"
			  .$ga_dash_top_referrers.
			"  
			]);
			
			var options = {
				page: 'enable',
				pageSize: 6,
				width: '100%'
			};        
			
			var chart = new google.visualization.Table(document.getElementById('ga_dash_rdata'));
			chart.draw(datar, options);
			
		  }";
		}
	}
	if (get_option('ga_dash_sd')){
		$ga_dash_top_searches=ga_dash_top_searches($service, $projectId, $from, $to);
		if ($ga_dash_top_searches){
		 $code.='
			google.load("visualization", "1", {packages:["table"]})
			function ga_dash_drawsd() {
			
			var datas = google.visualization.arrayToDataTable(['."
			  ['".__("Top Searches",'ga-dash')."', '".__("Visits",'ga-dash')."'],"
			  .$ga_dash_top_searches.
			"  
			]);
			
			var options = {
				page: 'enable',
				pageSize: 6,
				width: '100%'
			};        
			
			var chart = new google.visualization.Table(document.getElementById('ga_dash_sdata'));
			chart.draw(datas, options);
			
		  }";
		}
	}
    $code.="</script>";
	$code.='<div id="ga-dash">
	<center>
		<div id="buttons_div">
		
			<input class="gabutton" type="button" value="'.__("Today",'ga-dash').'" onClick="window.location=\'?period=today&query='.$query.'\'" />
			<input class="gabutton" type="button" value="'.__("Yesterday",'ga-dash').'" onClick="window.location=\'?period=yesterday&query='.$query.'\'" />
			<input class="gabutton" type="button" value="'.__("Last 7 days",'ga-dash').'" onClick="window.location=\'?period=last7days&query='.$query.'\'" />
			<input class="gabutton" type="button" value="'.__("Last 14 days",'ga-dash').'" onClick="window.location=\'?period=last14days&query='.$query.'\'" />
			<input class="gabutton" type="button" value="'.__("Last 30 days",'ga-dash').'" onClick="window.location=\'?period=last30days&query='.$query.'\'" />
		
		</div>
		
		<div id="ga_dash_statsdata"></div>
		<div id="details_div">
			
			<table class="gatable" cellpadding="4">
			<tr>
			<td width="24%">'.__("Visits:",'ga-dash').'</td>
			<td width="12%" class="gavalue"><a href="?query=visits&period='.$period.'" class="gatable">'.$data['rows'][0][1].'</td>
			<td width="24%">'.__("Visitors:",'ga-dash').'</td>
			<td width="12%" class="gavalue"><a href="?query=visitors&period='.$period.'" class="gatable">'.$data['rows'][0][2].'</a></td>
			<td width="24%">'.__("Page Views:",'ga-dash').'</td>
			<td width="12%" class="gavalue"><a href="?query=pageviews&period='.$period.'" class="gatable">'.$data['rows'][0][3].'</a></td>
			</tr>
			<tr>
			<td>'.__("Bounce Rate:",'ga-dash').'</td>
			<td class="gavalue"><a href="?query=visitBounceRate&period='.$period.'" class="gatable">'.round($data['rows'][0][4],2).'%</a></td>
			<td>'.__("Organic Search:",'ga-dash').'</td>
			<td class="gavalue"><a href="?query=organicSearches&period='.$period.'" class="gatable">'.$data['rows'][0][5].'</a></td>
			<td>'.__("Pages per Visit:",'ga-dash').'</td>
			<td class="gavalue"><a href="#" class="gatable">'.(($data['rows'][0][1]) ? round($data['rows'][0][3]/$data['rows'][0][1],2) : '0').'</a></td>
			</tr>
			</table>
					
		</div>';
		
	if (get_option('ga_dash_map')){
		$code.='<br /><h3>'.__("Visits by Country",'ga-dash').'</h3>
		<div id="ga_dash_mapdata"></div>';
	}
	
	if (get_option('ga_dash_traffic')){
		$code.='<br /><h3>'.__("Traffic Overview",'ga-dash').'</h3>
		<table width="100%"><tr><td width="50%"><div id="ga_dash_trafficdata"></div></td><td width="50%"><div id="ga_dash_nvrdata"></div></td></tr></table>';
	}
	
	$code.='</center>		
	</div>';
	if (get_option('ga_dash_pgd'))
		$code .= '<div id="ga_dash_pgddata"></div>';
	if (get_option('ga_dash_rd'))	
		$code .= '<div id="ga_dash_rdata"></div>';
	if (get_option('ga_dash_sd'))	
		$code .= '<div id="ga_dash_sdata"></div>';
	
	echo $code; 
   

}	
?>