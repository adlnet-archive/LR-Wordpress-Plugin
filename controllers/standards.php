<?php
	/*
Controller name: Standards
Controller description: Standards Controller
*/
	class JSON_API_Standards_Controller {
		const SEARCH_ROOT = "http://12.109.40.31/new/standards/";
		public function standards(){
			global $json_api;
			$standard = $json_api->query->get("standard");				
			$raw_data = file_get_contents(self::SEARCH_ROOT . $standard);
			$data = json_decode($raw_data);			
			return Array(
				data => $data
				);

		}
	}
?>