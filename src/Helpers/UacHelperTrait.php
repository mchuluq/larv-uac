<?php

namespace Mchuluq\Laravel\Uac\Helpers; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Mchuluq\Laravel\Uac\Models\User;


Trait UacHelperTrait{

    function keygen(){
        return sha1(microtime(true).mt_rand(10000,90000));
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
