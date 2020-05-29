<?php

namespace Mchuluq\Laravel\Uac\Helpers; 

//use Mchuluq\Uac\Tools\Ip2location_lite as ip2l;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
//use DeviceDetector\DeviceDetector;
//use DeviceDetector\Parser\Device\DeviceParserAbstract;

use Illuminate\Support\Arr;

use Mchuluq\Laravel\Uac\Models\User;


Trait UacHelperTrait{

    function keygen(){
        return sha1(microtime(true).mt_rand(10000,90000));
    }

    function guid($namespace = '',$strips=TRUE){
        if (function_exists('com_create_guid') === true){
            return trim(com_create_guid(), '{}');
        }
        
        static $guid = '';
        $uid = uniqid($namespace,true);

        $data = $namespace;
        $data .= microtime(true);
        $data .= mt_rand(10000,90000);
        $data .= (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'user_agent';
        $data .= (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
        $data .= (isset($_SERVER['REMOTE_PORT'])) ? $_SERVER['REMOTE_PORT'] : '8080';

        $hash = hash('ripemd128',$uid.$guid.md5($data));
        $guid = ($strips) ? substr($hash,0,8).'-'.substr($hash,8,4).'-'.substr($hash,12,4).'-'.substr($hash,16,4).'-'.substr($hash,20,12) : substr($hash,0,8).substr($hash,8,4).substr($hash,12,4).substr($hash,16,4).substr($hash,20,12);
        return $guid;
    }

    function getGravatar($email) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= '?'.http_build_query(config('uac.gravatar_options'));
        return $url;
    }

    public static function slugify($text){
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

    function getDevice(Request $req){
        $ip_address = $req->server('REMOTE_ADDR');
        $agent = $req->server('HTTP_USER_AGENT');
        $ip2l_config = config('uac.ip2location');

        $location = Cache::store(config('uac.cache_driver'))->rememberForever(self::key('ip2location_lite','getCity',$ip_address),function () use($ip_address,$ip2l_config) {
            $ip2l = new ip2l($ip2l_config);
            $get = $ip2l->getCity($ip_address);
            return array(
                'latitude'      => $get['latitude'],
                'longitude'     => $get['longitude'],
                'user_timezone' => $get['timezone'],
                'country_code'  => $get['countryCode'],
                'country_name'  => $get['countryName'],
                'region_name'   => $get['regionName'],
                'city_name'     => $get['cityName'],
                'zip_code'      => $get['zipCode'],
            );
        });

        $device = Cache::store(config('uac.cache_driver'))->rememberForever(self::key('device_agent',$agent),function () use($agent) {            
            DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);        
            $dd = new DeviceDetector($agent);
            $parse = $dd->parse();
            $client = $dd->getClient();
            $os = $dd->getOs();

            return array(
                'agent_string' => $agent,
                'browser_name' => $client['name'].' - '.$client['short_name'],
                'browser_version' => $client['version'],
                'os_platform' => implode(' ',array($os['name'],$os['version'],$os['platform'])),
                'is_mobile' => $dd->isMobile(),
                'device_name' => $dd->getDeviceName(),
                'device_brand' => $dd->getBrand(),
                'device_model' => $dd->getModel(),
            );
        });

        return array_merge($location,$device);
    }

    function generateKey(){
        return md5(serialize(func_num_args()));
    }

    protected function randomToken($result_length = 32){
		if(!isset($result_length) || intval($result_length) <= 8 ){
			$result_length = 32;
		}
		// Try random_bytes: PHP 7
		if (function_exists('random_bytes')) {
			return bin2hex(random_bytes($result_length / 2));
		}
		// Try mcrypt
		if (function_exists('mcrypt_create_iv')) {
			return bin2hex(mcrypt_create_iv($result_length / 2, MCRYPT_DEV_URANDOM));
		}
		// Try openssl
		if (function_exists('openssl_random_pseudo_bytes')) {
			return bin2hex(openssl_random_pseudo_bytes($result_length / 2));
		}
		// No luck!
		return FALSE;
	}

	protected function generateSelectorValidatorCouple($id=null,$selector_size = 40, $validator_size = 128){
		$selector = $this->randomToken($selector_size);
        $validator = $this->randomToken($validator_size);
        $validator_hashed = password_hash($validator,PASSWORD_BCRYPT);
		$user_code = "$selector.$validator.$id";
		return [
            'selector' => $selector,
            'id' => $id,
			'validator_hashed' => $validator_hashed,
			'user_code' => $user_code,
		];
	}

	protected function retrieveSelectorValidatorCouple($user_code){
		// Check code
		if ($user_code){
			$tokens = explode('.', $user_code);
			// Check tokens
			if (count($tokens) === 3){
				return [
					'selector' => $tokens[0],
					'validator' => $tokens[1],
					'id' => $tokens[2]
				];
			}
		}
		return FALSE;
    }
}
