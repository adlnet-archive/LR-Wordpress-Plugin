<?php
	/*
Controller name: Standards
Controller description: Standards Controller
*/	
	class JSON_API_Standards_Controller {
		const SEARCH_ROOT = "http://72.243.185.28/new/standards/";
		
		public function handleRedisStandard($standard){
			global $redis_enabled;
			if(!$redis_enabled){
				return file_get_contents(self::SEARCH_ROOT . $standard);
			}
			
			global $lr_redis;
			
			try{
				$key = md5('$standards/' .$standard);
				$value = $lr_redis->get($key);
				
				if(empty($value)){
					$value = file_get_contents(self::SEARCH_ROOT . $standard);
					$lr_redis->set($key, $value);
				}
				
				$lr_redis->expire($key, '10800');
				return $value;
			}
			
			catch(Exception $e){
				return file_get_contents(self::SEARCH_ROOT . $standard);
			}
		}
		
		public function standards(){
			global $json_api;

			$standard = $json_api->query->get("standard");
			
			$raw_data = $this->handleRedisStandard($standard);
			$data = json_decode($raw_data);			
			return Array(
				'data' => $data
				);

		}
		

	}
?>