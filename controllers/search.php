<?php
	/*
Controller name: Search
Controller description: Search Controller
*/
	class JSON_API_Search_Controller {
		const SEARCH_ROOT = "http://12.109.40.31/search?";
		const PUBLISHER_ROOT = "http://12.109.40.31/publisher/";
		public function search(){
			global $json_api;
			$query = array (
                            "terms" => $json_api->query->get("terms"),
                            "filter" => $json_api->query->get("filter"),
                            "page" => $json_api->query->get("page")
                        );
			$raw_data = file_get_contents(self::SEARCH_ROOT . http_build_query($query));
			$data = json_decode($raw_data);	
			return $data;

		}
		public function publisher(){
			global $json_api;
			$query = array (
                            "filter" => $json_api->query->get("filter"),
                            "page" => $json_api->query->get("page")
                        );
			$raw_data = file_get_contents(self::PUBLISHER_ROOT . $json_api->query->get("terms") . '?' . http_build_query($query));
			$data = json_decode($raw_data);	
			return array('data'=>$data);

		}
	}
?>