<?php
	/*
Controller name: Publishers
Controller description: Publishers Controller
*/
	class JSON_API_Publishers_Controller {
		const PUBLISHERS_ROOT = "http://72.243.185.28/publishers/";
		public function publishers_list(){
			global $json_api;
			$page = $json_api->query->get("fetchPage");		
			$gov = $json_api->query->get("gov");		
			$letter = $json_api->query->get("letter");		
			$raw_data = empty($letter) ? file_get_contents(self::PUBLISHERS_ROOT . '?' . http_build_query(Array('page'=>$page, 'gov'=>$gov))) :
										 file_get_contents(self::PUBLISHERS_ROOT .$letter.'?'.http_build_query(Array('page'=>$page)));
			$data = json_decode($raw_data);			
			return Array(
				data => $data
				);
		}
	}
?>