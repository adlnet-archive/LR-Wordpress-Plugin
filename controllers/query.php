<?php
	/*
Controller name: Query
Controller description: Query Controller
*/
	class JSON_API_Query_Controller {
		const SEARCH_ROOT = "http://72.243.185.28/search?";
		const PUBLISHER_ROOT = "http://72.243.185.28/publisher/";
		
		public function handleRedisSearch($termsArr){
			global $redis_enabled;
			if(!$redis_enabled){
				return file_get_contents(self::SEARCH_ROOT . http_build_query($termsArr));
			}
			
			global $lr_redis;
			
			try{
				$key = md5('$search/' . strtolower(http_build_query($termsArr)));
				$value = $lr_redis->get($key);
				
				if(empty($value)){
					$value = file_get_contents(self::SEARCH_ROOT . http_build_query($termsArr));
					$lr_redis->set($key, $value);
				}
				
				$lr_redis->expire($key, '10800');
				return $value;
			}
			
			catch(Exception $e){
				return file_get_contents(self::SEARCH_ROOT . http_build_query($termsArr));
			}
		}
		
		public function search(){
			global $json_api;
			$query = array (
                            "terms" => $json_api->query->get("terms"),
                            "filter" => $json_api->query->get("filter"),
                            "page" => $json_api->query->get("lr_page"),
							"gov" => $json_api->query->get("gov")
                        );
			$raw_data = $this->handleRedisSearch($query);
			$data = json_decode($raw_data);	
			return $data;

		}
		
		public function publisher(){
			global $json_api;
			$query = array (
                            "filter" => $json_api->query->get("filter"),
                            "page" => $json_api->query->get("lr_page"),
                        );
            $url = self::PUBLISHER_ROOT . rawurlencode($json_api->query->get("terms"));
            if ($json_api->query->get("filter") || $json_api->query->get("lr_page"))
                $url = $url . '?' . http_build_query($query);
            try{
                $raw_data = file_get_contents($url);
                $data = json_decode($raw_data);	
                return array('data'=>$data);
            }catch(Exception $ex){
                return "fail";
            }
		}
	}
?>
