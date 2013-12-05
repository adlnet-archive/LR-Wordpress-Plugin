<?php
/*
Controller name: Flagged
Controller description: Flagged Controller
*/
	class JSON_API_Flagged_Controller {
		const DATA_ROOT = "http://localhost:9000/flagged";				
		public function flag_item(){
		  global $json_api;			
		  $postData = array(
				    "reason" =>  $json_api->query->get("reason"),
				    "id" =>  $json_api->query->get("id"),
				    "description" =>  $json_api->query->get("description")
				    );

		  $content = json_encode($postData);
		  
		  $curl = curl_init(self::DATA_ROOT);
		  curl_setopt($curl, CURLOPT_HEADER, false);
		  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($curl, CURLOPT_HTTPHEADER,
			      array("Content-type: application/json"));
		  curl_setopt($curl, CURLOPT_POST, true);
		  curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		  
		  $json_response = curl_exec($curl);
		  
		  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		  return json_decode($json_response);
		}
		public function get_flagged_items(){
		  global $json_api;
		  $url = self::DATA_ROOT;
		  if($json_api->query->get("id")){
			$url = $url . '/' . $json_api->query->get("id");
		  }		  
		  $raw_data = file_get_contents($url);
		  return json_decode($raw_data);	
		}
	}
?>
