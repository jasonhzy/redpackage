<?php
header("Content-type: text/html; charset=utf-8");
class RedCashAPI {
	private $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
	
	function  __construct($cash_conf = array()){
		$this->redcash_id = $cash_conf['redcash_id'];
		$this->openid = $cash_conf['openid'];
		$this->token = $cash_conf['token'];
	}
	
	function sendRedCash( ) {
		//get config info
		$conf = $this->get_setting();
		//red cash all params
		$package = array();
		$package['nonce_str'] = $this->createNoncestr(32);
		$package['mch_billno'] = $conf['mchid'].date('YmdHis').rand(1000, 9999);
		$package['mch_id'] = $conf['mchid'];
		$package['wxappid'] = $conf['appid'];
		$package['nick_name'] = $conf['nick_name'];
		$package['send_name'] = $conf['send_name'] ;
		$package['re_openid'] = $this->openid;
		$package['total_amount'] = $conf['total_amount'];
		$package['min_value'] = $conf['min_value'];
		$package['max_value'] = $conf['max_value'];
		$package['total_num'] = 1;
		$package['wishing'] = $conf['wishing'];
		$package['client_ip'] = $this->getClientIP();
		$package['act_name'] = $conf['act_name'];
		$package['remark'] = $conf['remark'];
		ksort($package, SORT_STRING);
		$strSign = '';
		foreach($package as $key => $v) {
			$strSign .= "{$key}={$v}&";
		}
		$strSign .= "key={$conf['key']}";
		$package['sign'] = strtoupper(md5($strSign));
		$xml = $this->arrayToXml($package);
		
		$response = $this->http_request($this->url, $xml, $conf['certs'], 'post');
		$responseObj = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
		$aMsg = (array)$responseObj;
		$db_data =array('cashsetting_id' => $this->redcash_id, 'token' => $this->token, 'mch_billno' => $package['mch_billno'], 'openid' => $package['re_openid'], 'total_amount' => $package['total_amount'], 'param_msg' => serialize($package), 'create_time' => date('Y-m-d H:i:s'));
		if (isset($aMsg['err_code'])) {
			$db_data['err_code'] = $aMsg['err_code'];
			$db_data['err_code_des'] = $aMsg['err_code_des'];
		}else {
			$db_data['err_code'] = 'SUCCESS';
			$db_data['err_code_des'] = '发送成功，领取红包';
		}
		$db_data['return_msg'] = serialize($aMsg);
		M('redcash_list')->add($db_data);
		return $db_data;
	}
	
	function get_setting() {
		$payconf = M('redcash_wxconf')->where(array('token' => $this->token))->find();
		if ( !$payconf['mchid'] || !$payconf['appid'] ||!$payconf['key']) {
			Log::record('get red cash wechat params: '.print_r($payconf, 1), Log::INFO);
            Log::save();
			die('微信参数配置不完整');
		}
		
		$setting = M('redcash_setting')->where(array('token' => $this->token, 'id' => $this->redcash_id))->find();
		$setting['mchid'] = $payconf['mchid'];
		$setting['appid'] = $payconf['appid'];
		$setting['key'] = $payconf['key'];
		
		if ($setting['status'] == '1') {
			$money = intval($setting['fixed_amount'] * 100); 
			$setting['min_value'] = $money;
			$setting['max_value'] = $money;
			$setting['total_amount'] = $money;
		}
		if (!$setting['nick_name'] || !$setting['send_name']  || !$setting['fixed_amount']  || !$setting['wishing']  || !$setting['act_name']  || !$setting['remark']) {
			Log::record('get red cash params : '.print_r($setting, 1), Log::INFO);
            Log::save();
			die('活动信息配置不完整');
		}
		
		$certs = array(
			'SSLCERT' => getcwd().'/'.$payconf['ssl_cert'],
			'SSLKEY' => getcwd().'/'.$payconf['ssl_key'],
			'CAINFO' => getcwd().'/'.$payconf['ssl_cainfo'],
		);
		if (!$certs) {
			die('未设置微信支付证书信息');
		}
		$setting['certs'] = array('certs' => $certs);
		return $setting;
	}
	
	
	
	function createNoncestr( $length = 32 ) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}
	
	function getClientIP() {
		$onlineip = '';
		if (getenv ( 'HTTP_CLIENT_IP' ) && strcasecmp ( getenv ( 'HTTP_CLIENT_IP' ), 'unknown' )) {
			$onlineip = getenv ( 'HTTP_CLIENT_IP' );
		} elseif (getenv ( 'HTTP_X_FORWARDED_FOR' ) && strcasecmp ( getenv ( 'HTTP_X_FORWARDED_FOR' ), 'unknown' )) {
			$onlineip = getenv ( 'HTTP_X_FORWARDED_FOR' );
		} elseif (getenv ( 'REMOTE_ADDR' ) && strcasecmp ( getenv ( 'REMOTE_ADDR' ), 'unknown' )) {
			$onlineip = getenv ( 'REMOTE_ADDR' );
		} elseif (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], 'unknown' )) {
			$onlineip = $_SERVER ['REMOTE_ADDR'];
		}
		return $onlineip;
	}
	
	function arrayToXml($arr = null){
		if(!is_array($arr) || empty($arr)){
			die("参数不为数组无法解析");
		}
		$xml = "<xml>";
		foreach ($arr as $key=>$val){
			if (is_numeric($val)){
				$xml.="<".$key.">".$val."</".$key.">"; 
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
			}
		}
		$xml.="</xml>";
		return $xml; 
	}
	
	function http_request($url, $fields, $params, $method='get', $second=30){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_TIMEOUT, $second);
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		if (isset($params['certs'])) {
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, $params['certs']['SSLCERT']);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, $params['certs']['SSLKEY']);
			curl_setopt($ch, CURLOPT_CAINFO, 'PEM');
			curl_setopt($ch,CURLOPT_CAINFO, $params['certs']['CAINFO']);
		}
		if ($method=='post') {
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}