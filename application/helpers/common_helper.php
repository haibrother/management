<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 * author 417@668x.net
 *公用方法库
 * @filesource
 */

if ( ! function_exists('get_client_ip'))
{
	/**
	* 取得客户端的IP地址
	*/
	 function get_client_ip()
	{
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
}

if(! function_exists('get_rand_value')){
	/**
	 *获取一个随机字符
	 **/
	function get_rand_value($str_length=6){
		$str = '';  
		for ($i = 0; $i < $str_length; $i++)  {  
			$str .= chr(mt_rand(97, 122));  
		}  
		return strtoupper($str);
	}
}

if(! function_exists('mb_string')){
	/**
	 * 截取中文字符不乱码
	 * @param string
	 * @return string
	 * @author 417
	 */
	function mb_string($str,$start=0,$length=20,$encoding='utf-8',$ellipsis='...'){
		if(isset ($str) && is_string($str) && trim($str)){
			$string = mb_substr($str, $start, $length, $encoding);
			if(mb_strlen($str,$encoding)>$length){
				$string .= $ellipsis;
			}
			return $string;
		}else{
			return "";
		}
	}
}

/*
 *简繁体 转
 **/
if(! function_exists('transcoding')){
    function transcoding($string){

        $http_accept_language = !isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ?
                                    'zh-cn' :
                                    strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        if(strstr($http_accept_language, 'zh-cn')){
            $string = mb_convert_encoding($string,'gbk','UTF-8, big5, gbk');
        }else if(strstr($http_accept_language, 'zh-tw') || strstr($http_accept_language, 'zh-hk')){
            $string = mb_convert_encoding($string,'big5','UTF-8, big5, gbk');
        }
        return $string;	
    }
}
