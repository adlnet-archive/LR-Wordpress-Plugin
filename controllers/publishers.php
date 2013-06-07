<?php
	/*
Controller name: Publishers
Controller description: Publishers Controller
*/
	class JSON_API_Publishers_Controller {
		const PUBLISHERS_ROOT = "http://12.109.40.31/publishers/?";
		public function publishers_list(){
			global $json_api;
			$page = $json_api->query->get("fetchPage");		
			$raw_data = file_get_contents(self::PUBLISHERS_ROOT . http_build_query(Array('page'=>$page)));
			$data = json_decode($raw_data);			
			return Array(
				data => $data
				);
		}
	}
?>