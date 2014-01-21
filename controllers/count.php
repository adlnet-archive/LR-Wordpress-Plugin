<?php
	/*
Controller name: Count
Controller description: Count Controller
*/
	class JSON_API_Count_Controller {
		const SEARCH_ROOT = "http://72.243.185.28/data";
		public function get_count(){
			$raw_data = file_get_contents(self::SEARCH_ROOT);
			$data = json_decode($raw_data);			
			return $data;
		}
	}
?>
