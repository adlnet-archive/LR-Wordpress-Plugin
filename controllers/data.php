<?php
/*
Controller name: Data
Controller description: Data Controller
*/
	class JSON_API_Data_Controller {
		const DATA_ROOT = "http://12.109.40.31/data/";
		public function get_data_item(){
			global $json_api;
			$doc_id = $json_api->query->get("doc_id");
			$raw_data = file_get_contents(self::DATA_ROOT . $doc_id);
			$data = json_decode($raw_data);
			return array(
				data => $data
				);			
		}
		public function get_data_items(){
			global $json_api;
			$keys = $json_api->query->get("keys");
			$raw_data = file_get_contents(self::DATA_ROOT . "?keys=" . $keys);
			$data = json_decode($raw_data);
			return array(
				data => $data
				);						
		}
	}
?>