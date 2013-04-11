<?php
	function ga_dash_clear_cache(){
		global $wpdb;
		$sqlquery=$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_gadash%%'");
	}
	
	function ga_dash_safe_get($key) {
		if (array_key_exists($key, $_POST)) {
			return $_POST[$key];
		}
		return false;
	}
	
	function ga_dash_store_token ($token){
		update_option('ga_dash_token', $token);
	}		
	
	function ga_dash_get_token (){

		if (get_option('ga_dash_token')){
			return get_option('ga_dash_token');
		}
		else{
			return;
		}
	
	}
	
	function ga_dash_reset_token (){
		update_option('ga_dash_token', "");
		update_option('ga_dash_tableid', "");
		update_option('ga_dash_tableid_jail', "");
		update_option('ga_dash_profile_list', "");
		update_option('ga_dash_access', ""); 		
	}

// Get Top Pages
	function ga_dash_top_pages($service, $projectId, $from, $to){

		$metrics = 'ga:pageviews'; 
		$dimensions = 'ga:pageTitle';
		try{
			$serial='gadash_qr4'.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to);
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$data = $service->data_ga->get('ga:'.$projectId, $from, $to, $metrics, array('dimensions' => $dimensions, 'sort' => '-ga:pageviews', 'max-results' => '24', 'filters' => 'ga:pagePath!=/'));
				set_transient( $serial, $data, get_option('ga_dash_cachetime') );
			}else{
				$data = $transient;	
			}			
		}  
			catch(exception $e) {
			echo "<br />".__("ERROR LOG:")."<br /><br />".$e; 
		}	
		if (!$data['rows']){
			return 0;
		}
		
		$ga_dash_data="";
		$i=0;
		while (isset($data['rows'][$i][0])){
			$ga_dash_data.="['".str_replace("'"," ",$data['rows'][$i][0])."',".$data['rows'][$i][1]."],";
			$i++;
		}

		return $ga_dash_data;
	}
	
// Get Top referrers
	function ga_dash_top_referrers($service, $projectId, $from, $to){

		$metrics = 'ga:visits'; 
		$dimensions = 'ga:source,ga:medium';
		try{
			$serial='gadash_qr5'.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to);
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$data = $service->data_ga->get('ga:'.$projectId, $from, $to, $metrics, array('dimensions' => $dimensions, 'sort' => '-ga:visits', 'max-results' => '24', 'filters' => 'ga:medium==referral'));	
				set_transient( $serial, $data, get_option('ga_dash_cachetime') );
			}else{
				$data = $transient;		
			}			
		}  
			catch(exception $e) {
			echo "<br />".__("ERROR LOG:")."<br /><br />".$e; 
		}	
		if (!$data['rows']){
			return 0;
		}
		
		$ga_dash_data="";
		$i=0;
		while (isset($data['rows'][$i][0])){
			$ga_dash_data.="['".str_replace("'"," ",$data['rows'][$i][0])."',".$data['rows'][$i][2]."],";
			$i++;
		}

		return $ga_dash_data;
	}

// Get Top searches
	function ga_dash_top_searches($service, $projectId, $from, $to){

		$metrics = 'ga:visits'; 
		$dimensions = 'ga:keyword';
		try{
			$serial='gadash_qr6'.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to);
			$transient = get_transient($serial);
			if ( empty( $transient ) ){
				$data = $service->data_ga->get('ga:'.$projectId, $from, $to, $metrics, array('dimensions' => $dimensions, 'sort' => '-ga:visits', 'max-results' => '24', 'filters' => 'ga:keyword!=(not provided);ga:keyword!=(not set)'));
				set_transient( $serial, $data, get_option('ga_dash_cachetime') );
			}else{
				$data = $transient;		
			}			
		}  
			catch(exception $e) {
			echo "<br />".__("ERROR LOG:")."<br /><br />".$e; 
		}	
		if (!$data['rows']){
			return 0;
		}
		
		$ga_dash_data="";
		$i=0;
		while (isset($data['rows'][$i][0])){
			$ga_dash_data.="['".str_replace("'"," ",$data['rows'][$i][0])."',".$data['rows'][$i][1]."],";
			$i++;
		}

		return $ga_dash_data;
	}
// Get Visits by Country
	function ga_dash_visits_country($service, $projectId, $from, $to){

		$metrics = 'ga:visits'; 
		$dimensions = 'ga:country';
		try{
			$serial='gadash_qr7'.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to);
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
		if (!$data['rows']){
			return 0;
		}
		
		$ga_dash_data="";
		for ($i=0;$i<$data['totalResults'];$i++){
			$ga_dash_data.="['".str_replace("'"," ",$data['rows'][$i][0])."',".$data['rows'][$i][1]."],";
		}

		return $ga_dash_data;

	}	
// Get Traffic Sources
	function ga_dash_traffic_sources($service, $projectId, $from, $to){

		$metrics = 'ga:visits'; 
		$dimensions = 'ga:medium';
		try{
			$serial='gadash_qr8'.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to);
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
		if (!$data['rows']){
			return 0;
		}
		
		$ga_dash_data="";
		for ($i=0;$i<$data['totalResults'];$i++){
			$ga_dash_data.="['".str_replace("(none)","direct",$data['rows'][$i][0])."',".$data['rows'][$i][1]."],";
		}

		return $ga_dash_data;

	}

// Get New vs. Returning
	function ga_dash_new_return($service, $projectId, $from, $to){

		$metrics = 'ga:visits'; 
		$dimensions = 'ga:visitorType';
		try{
			$serial='gadash_qr9'.str_replace(array('ga:',',','-',date('Y')),"",$projectId.$from.$to);
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
		if (!$data['rows']){
			return 0;
		}
		
		$ga_dash_data="";
		for ($i=0;$i<$data['totalResults'];$i++){
			$ga_dash_data.="['".str_replace("'"," ",$data['rows'][$i][0])."',".$data['rows'][$i][1]."],";
		}

		return $ga_dash_data;

	}	
?>