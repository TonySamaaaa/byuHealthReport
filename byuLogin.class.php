<?php
require __DIR__ . '/vendor/autoload.php';

use Curl\Curl;

class byuLogin {
	private $curl;
	
	private $loginCode;			//验证码Cookie
	private $AuthToken;
	
	function __construct() {
		$this->curl = new Curl();
		
		$this->curl->setHeader('User-Agent', 'Mozilla/5.0 (Linux; Android 9; SM-G9650 Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.126 MQQBrowser/6.2 TBS/044903 Mobile Safari/537.36 MMWEBID/7909 MicroMessenger/7.0.6.1460(0x27000636) Process/tools NetType/WIFI Language/zh_CN');
	}
	
	function getloginCode() {
		return($this->loginCode);
	}
	
	function setloginCode($loginCode) {
		$this->loginCode = $loginCode;
	}
	
	function getAuthToken() {
		return($this->AuthToken);
	}
	
	function genCode() {
		$this->curl->get('https://byu.educationgroup.cn/sso/auth/genCode');
		if ($this->curl->error) throw new Exception('genCode error');
		
		$this->setloginCode($this->curl->getCookie('loginCode'));
		return('data:image/jpeg;base64,' . base64_encode($this->curl->response));
	}
	
	function login($username, $password, $code) {
		$this->curl->setCookie('loginCode', $this->loginCode);
		$this->curl->post('https://byu.educationgroup.cn/sso/auth/login', array(
			'redirect' => '/api?client_id=A0002&scope=base&response_type=code&state=1&redirect_uri=https://byu.educationgroup.cn/portal/oauthApi/getAccessToken',
			'loginType' => 'account',
			'username' => base64_encode($username),
			'password' => base64_encode($password),
			'code' => $code
		));
		if ($this->curl->error) throw new Exception('login error');
		
		$this->AuthToken = $this->curl->getCookie('Auth-Token');
		return($this->AuthToken);
	}
}
?>