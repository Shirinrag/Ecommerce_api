<?php
		include_once  APPPATH.'third_party/bitly_vendor/autoload.php';
		use Bitly\BitlyClient;
		class Bilty {
			public function shortenurl($url='')
	        {
	        	$bitlyClient = new BitlyClient('eb3fa2266078d6d833b6533bfa7f3dced39c34f6');
				$options = ['longUrl' => $url];
				$response = $bitlyClient->shorten($options);
				//echo '<pre>'; print_r($response); exit;
				return $response->data->url;
	        }
    }