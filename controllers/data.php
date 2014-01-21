<?php
/*
Controller name: Data
Controller description: Data Controller
*/
	class JSON_API_Data_Controller {
		const DATA_ROOT = "http://72.243.185.28/data/";
		
		public function handleRedisDataItem($doc_id){
			global $redis_enabled;
			if(!$redis_enabled){
				return file_get_contents(self::DATA_ROOT . $doc_id);
			}
			
			global $lr_redis;
			
			try{
				$key = md5('$data_item/' .$doc_id);
				$value = $lr_redis->get($key);
				
				if(empty($value)){
					$value = file_get_contents(self::DATA_ROOT . $doc_id);
					$lr_redis->set($key, $value);
				}
				
				$lr_redis->expire($key, '10800');
				return $value;
			}
			
			catch(Exception $e){
				return file_get_contents(self::DATA_ROOT . $doc_id);
			}
		}
		
		public function get_data_item(){
			global $json_api;			
			$doc_id = $json_api->query->get("doc_id");
			$raw_data = $this->handleRedisDataItem($doc_id);
			$data = json_decode($raw_data);
			return array(
				data => $data=>data
				);			
		}
		
		public function get_data_items(){
			global $json_api;
			$query = array (
				keys => str_replace("\\", "", $json_api->query->get("keys"))
			);
			$raw_data = $this->handleRedisDataItem("?" . http_build_query($query));
			$data = json_decode($raw_data);
			return array(
				data => $data=>data
				);						
		}
	}
?>
