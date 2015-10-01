<?php
/**
 * PHP SDK for www.kaixin001.com (using OAuth2)
 * 
 * @author vboy <vboy1010@gmail.com>
 */

/**
 * @ignore
 */
class OAuthException extends Exception {
	// pass
}


/**
 * 开心网 OAuth 认证类(OAuth2.0)
 *
 * 授权机制说明，请大家参考开心网开放平台文档：{@link http://wiki.open.kaixin001.com/index.php?id=OAuth2.0文档}
 *
 * @package SDK
 * @author vboy
 * @version 2.0
 */
class KxApiBase {
	/**
	 * @ignore
	 */
	public $client_id;
	/**
	 * @ignore
	 */
	public $client_secret;
	/**
	 * @ignore
	 */
	public $access_token;
	/**
	 * @ignore
	 */
	public $refresh_token;
	/**
	 * Contains the last HTTP status code returned. 
	 *
	 * @ignore
	 */
	public $http_code;
	/**
	 * Contains the last API call.
	 *
	 * @ignore
	 */
	public $url;
	/**
	 * Set up the API root URL.
	 *
	 * @ignore
	 */
	public $host = "https://api.kaixin001.com/";
	/**
	 * Set timeout default.
	 *
	 * @ignore
	 */
	public $timeout = 30;
	/**
	 * Set connect timeout.
	 *
	 * @ignore
	 */
	public $connecttimeout = 30;
	/**
	 * Verify SSL Cert.
	 *
	 * @ignore
	 */
	public $ssl_verifypeer = FALSE;
	/**
	 * Respons format.
	 *
	 * @ignore
	 */
	public $format = 'json';
	/**
	 * Decode returned json data.
	 *
	 * @ignore
	 */
	public $decode_json = TRUE;
	/**
	 * Contains the last HTTP headers returned.
	 *
	 * @ignore
	 */
	public $http_info;
	/**
	 * Set the useragnet.
	 *
	 * @ignore
	 */
	public $useragent = 'KX PHPSDK OAuth2 v2.0';

	/**
	 * print the debug info
	 *
	 * @ignore
	 */
	public $debug = FALSE;

	/**
	 * boundary of multipart
	 * @ignore
	 */
	public static $boundary = '';

	/**
	 * params of file
	 * @ignore
	 */

	public static $params_file = array(
		'pic',
		'image',
		'attachment',
		'attachment1',
		'attachment2',
		'attachment3',
		'attachment4',
	);

	/**
	 * Set API URLS
	 */
	/**
	 * @ignore
	 */
	public function accessTokenURL()  { return 'https://api.kaixin001.com/oauth2/access_token'; }

	/**
	 * @ignore
	 */
	public function authorizeURL()    { return 'https://api.kaixin001.com/oauth2/authorize'; }

	/**
	 * construct WeiboOAuth object
	 */
	public function __construct($client_id, $client_secret, $access_token = NULL, $refresh_token = NULL)
	{
		$this->client_id     = $client_id;
		$this->client_secret = $client_secret;
		$this->access_token  = $access_token;
		$this->refresh_token = $refresh_token;
	}

	/**
	 * authorize接口
	 *
	 * 对应API：{@link https://api.kaixin001.com/Oauth2/authorize}
	 *
	 * @param string $url 授权后的回调地址,站外应用需与回调地址一致,站内应用需要填写canvas page的地址
	 * @param string $response_type 支持的值包括 code 和token 默认值为code
	 * @param string $state 用于保持请求和回调的状态。在回调时,会在Query Parameter中回传该参数
	 * @param string $display 授权页面类型 可选范围: 
	 *  - default		默认授权页面
	 *  - mobile		支持html5的手机
	 *  - popup			弹窗授权页
	 * @return array
	 */
	public function getAuthorizeURL( $url, $response_type = 'code', $state = NULL, $display = NULL )
	{
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['redirect_uri'] = $url;
		$params['response_type'] = $response_type;
		$params['state'] = $state;
		$params['display'] = $display;
		return $this->authorizeURL() . "?" . http_build_query($params);
	}

	/**
	 * access_token接口
	 *
	 * 对应API：{@link https://api.kaixin001.com/Oauth2/access_token}
	 *
	 * @param string $type 请求的类型,可以为:code, password, token
	 * @param array $keys 其他参数：
	 *  - 当$type为code时： array('code'=>..., 'redirect_uri'=>...)
	 *  - 当$type为password时： array('username'=>..., 'password'=>...)
	 *  - 当$type为token时： array('refresh_token'=>...)
	 * @return array
	 */
	public function getAccessToken( $type = 'code', $keys )
	{
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['client_secret'] = $this->client_secret;
		if ( $type === 'token' ) {
			$params['grant_type'] = 'refresh_token';
			$params['refresh_token'] = $keys['refresh_token'];
		} elseif ( $type === 'code' ) {
			$params['grant_type'] = 'authorization_code';
			$params['code'] = $keys['code'];
			$params['redirect_uri'] = $keys['redirect_uri'];
		} elseif ( $type === 'password' ) {
			$params['grant_type'] = 'password';
			$params['username'] = $keys['username'];
			$params['password'] = $keys['password'];
		} else {
			throw new OAuthException("wrong auth type");
		}

		$response = $this->oAuthRequest($this->accessTokenURL(), 'POST', $params);
		$token = json_decode($response, true);
		if ( is_array($token) && !isset($token['error']) ) {
			$this->access_token = $token['access_token'];
			$this->refresh_token = $token['refresh_token'];
		} else {
			throw new OAuthException("get access token failed." . $token['error']);
		}
		return $token;
	}

	/**
	 * 解析 signed_request
	 *
	 * @param string $signed_request 应用框架在加载iframe时会通过向Canvas URL post的参数signed_request
	 *
	 * @return array
	 */
	public function parseSignedRequest($signed_request)
	{
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
		$sig = self::base64decode($encoded_sig) ;
		$data = json_decode(self::base64decode($payload), true);
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') return '-1';
		$expected_sig = hash_hmac('sha256', $payload, $this->client_secret, true);
		return ($sig !== $expected_sig)? '-2':$data;
	}

	/**
	 * @ignore
	 */
	public function base64decode($str)
	{
		return base64_decode(strtr($str.str_repeat('=', (4 - strlen($str) % 4)), '-_', '+/'));
	}

	/**
	 * 从数组中读取access_token和refresh_token
	 * 常用于从Session或Cookie中读取token，或通过Session/Cookie中是否存有token判断登录状态。
	 *
	 * @param array $arr 存有access_token和secret_token的数组
	 * @return array 成功返回array('access_token'=>'value', 'refresh_token'=>'value'); 失败返回false
	 */
	public function getTokenFromArray( $arr )
	{
		if (isset($arr['access_token']) && $arr['access_token']) {
			$token = array();
			$this->access_token = $token['access_token'] = $arr['access_token'];
			if (isset($arr['refresh_token']) && $arr['refresh_token']) {
				$this->refresh_token = $token['refresh_token'] = $arr['refresh_token'];
			}

			return $token;
		} else {
			return false;
		}
	}

	/**
	 * GET wrappwer for oAuthRequest.
	 *
	 * @return mixed
	 */
	public function get($url, $parameters = array())
	{
		$response = $this->oAuthRequest($url, 'GET', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	/**
	 * POST wreapper for oAuthRequest.
	 *
	 * @return mixed
	 */
	public function post($url, $parameters = array(), $multi = false)
	{
		$response = $this->oAuthRequest($url, 'POST', $parameters, $multi );
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	/**
	 * DELTE wrapper for oAuthReqeust.
	 *
	 * @return mixed
	 */
	public function delete($url, $parameters = array())
	{
		$response = $this->oAuthRequest($url, 'DELETE', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	/**
	 * Format and sign an OAuth / API request
	 *
	 * @return string
	 * @ignore
	 */
	public function oAuthRequest($url, $method, $parameters, $multi = false)
	{
		if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->host}{$url}.{$this->format}";
		}

		switch ($method) {
			case 'GET':
				$parameters['access_token'] = $this->access_token;
				$url = $url . '?' . http_build_query($parameters);
				return $this->http($url, 'GET');
			default:
				$headers = array();
				$parameters['access_token'] = $this->access_token;				
				if (!$multi && (is_array($parameters) || is_object($parameters)) ) {
					$body = http_build_query($parameters);
				} else {
					$body = self::build_http_query_multi($parameters);
					$headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
				}
				return $this->http($url, $method, $body, $headers);
		}
	}

	/**
	 * Make an HTTP request
	 *
	 * @return string API results
	 * @ignore
	 */
	public function http($url, $method, $postfields = NULL, $headers = array())
	{
		$this->http_info = array();
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		if ( isset($this->access_token) && $this->access_token ) {
			$headers[] = "Authorization: OAuth2 ".$this->access_token;
		}

		$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;

		if ($this->debug) {
			echo "=====post data======\r\n";
			var_dump($postfields);

			echo '=====info====='."\r\n";
			print_r( curl_getinfo($ci) );

			echo '=====$response====='."\r\n";
			print_r( $response );
		}
		curl_close ($ci);
		return $response;
	}

	/**
	 * Get the header info to store.
	 *
	 * @return int
	 * @ignore
	 */
	public function getHeader($ch, $header)
	{
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	/**
	 * @ignore
	 */
	public static function build_http_query_multi($params)
	{
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {
			if( in_array($parameter, self::$params_file) && $value{0} == '@' ) {				
				$url = ltrim( $value, '@' );
				if(!empty($url))
				{
					$content = file_get_contents( $url );
					$array = explode( '?', basename( $url ) );
					$filename = $array[0];

					$filename = $_FILES[$parameter]['name'];
					$multipartbody .= $MPboundary . "\r\n";
					$mime = self::get_image_mime($url); 
					$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";				
					$multipartbody .= "Content-Type: ".$mime."\r\n\r\n";
					$multipartbody .= $content. "\r\n";
				}
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}

		}

		$multipartbody .= $endMPboundary;		
		return $multipartbody;
	}
	public static function get_image_mime( $file )
    {
		//var_dump(PATHINFO_EXTENSION);
		//exit;
		
    	$ext = strtolower(pathinfo( $file , PATHINFO_EXTENSION ));		
    	switch( $ext )
    	{
    		case 'jpg':
    		case 'jpeg':
    			$mime = 'image/jpg';
    			break;
    		 	
    		case 'png';
    			$mime = 'image/png';
    			break;
    			
    		case 'gif';
    		default:
    			$mime = 'image/gif';
    			break;    		
    	}
    	return $mime;
    }

}



/**
 * 开心网 API操作类V2
 *
 * 使用前需要先手工调用 kxclient.class.php
 *
 * @package restapi
 * @@author vboy <vboy1010@gmail.com>
 * @version 2.0
 */
class KxApiClient
{
	/**
	 * 构造函数
	 * 
	 * @access public
	 * @param mixed $akey 微博开放平台应用APP KEY
	 * @param mixed $skey 微博开放平台应用APP SECRET
	 * @param mixed $access_token OAuth认证返回的token
	 * @param mixed $refresh_token OAuth认证返回的token secret
	 * @return void
	 */
	public function __construct( $akey, $skey, $access_token, $refresh_token = NULL)
	{
		$this->oauth = new KxApiBase( $akey, $skey, $access_token, $refresh_token );
	}

	/**
	 * 获取当前登录用户的个人资料
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前登录用户的资料}
	 *
	 * @param $fields	string  允许用户自定义返回字段，多个属性之间用英文半角逗号作为分隔符
	 * 
	 *                用户的所有属性( 以下字段均可选 ):
	 *                uid, name, gender, hometown, city, status, 
	 *                logo120, logo50, birthday, bodyform, blood, 
	 *                marriage, trainwith, interest, favbook, 
	 *                favmovie, favtv, idol, motto, wishlist, 
	 *                intro, education, schooltype, school, 
	 *                class, year, career, company, dept, 
	 *                beginyear, beginmonth, endyear, 
	 *                endmonth, isStar，pinyin，online
	 *
	 *                用户的基本属性：uid, name, gender, logo50
	 *
	 *                当fields字段为空的时候只返回用户的基本属性
	 * @access public
	 * @return array
	 */
	public function users_me( $fields = '' )
	{
		$params = array();
		$params['fields'] = $fields;
		return $this->oauth->get('users/me', $params);
	}

	/**
	 * 根据UID获取多个用户的资料
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=根据UID获取多个用户的资料}
	 *
	 * @param $uids		string  允许用户自定义返回字段，多个属性之间用英文半角逗号作为分隔符
	 * @param $fields	string  允许用户自定义返回字段，多个属性之间用英文半角逗号作为分隔符
	 * 
	 *                用户的所有属性( 以下字段均可选 ):
	 *                uid, name, gender, hometown, city, status, 
	 *                logo120, logo50, birthday, bodyform, blood, 
	 *                marriage, trainwith, interest, favbook, 
	 *                favmovie, favtv, idol, motto, wishlist, 
	 *                intro, education, schooltype, school, 
	 *                class, year, career, company, dept, 
	 *                beginyear, beginmonth, endyear, 
	 *                endmonth, isStar，pinyin，online
	 *
	 *                用户的基本属性：uid, name, gender, logo50
	 *
	 *                当fields字段为空的时候只返回用户的基本属性
	 * @access public
	 * @return array
	 */

	public function users_show( $uids , $fields = '')
	{
		$params = array();
		$params['uids'] = $uids;
		$params['fields'] = $fields;
		return $this->oauth->get('users/show', $params);
	}

	/**
	 * 获取可能认识的人
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=可能认识的人}
	 *
	 * @param $start	int  起始值，默认为0
	 * @param $num		int  返回数量，默认为20，最多50
	 * 
	 * @access public
	 * @return array
	 */
	public function users_mfriends( $start = 0 , $num = 20)
	{
		$params = array();
		$params['start'] = $start;
		$params['num'] = $num;
		return $this->oauth->get('users/mfriends', $params);
	}

	/**
	 * 获取当前用户的公共主页列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=公共主页列表}
	 *
	 * @param $start	int  起始值，默认为0
	 * @param $num		int  返回数量，默认为20，最多50，大于50只返回50个
	 * 
	 * @access public
	 * @return array
	 */
	public function users_page_list( $start = 0 , $num = 20)
	{
		$params = array();
		$params['start'] = $start;
		$params['num'] = $num;
		return $this->oauth->get('users/page_list', $params);
	}

	/**
	 * 获取当前用户的最近来访列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=来访列表}
	 *
	 * @param $start		int  起始值，默认为0
	 * @param $num			int  返回数量，默认为20，最多50，大于50只返回50个
	 * 
	 * @access public
	 * @return array
	 */
	public function users_visitors( $start = 0 , $num = 20)
	{
		$params = array();
		$params['start'] = $start;
		$params['num'] = $num;
		return $this->oauth->get('users/visitors', $params);
	}


	/**
	 * 获取用户的实名认证信息
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=来访列表}
	 *
	 * @param $uid		int  要查询的用户uid
	 * 
	 * @access public
	 * @return array
	 */
	public function users_nverifyinfo( $uid )
	{
		$params = array();
		$params['uid'] = $uid;
		return $this->oauth->get('users/nverifyinfo', $params);
	}


	/**
	 * 获取当前登录用户的好友资料
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前登录用户的好友资料}
	 *
	 * @param $fields		string  允许用户自定义返回字段，多个属性之间用英文半角逗号作为分隔符
	 * 
	 *                用户的所有属性( 以下字段均可选 ):
	 *                uid, name, gender, hometown, city, status, 
	 *                logo120, logo50, birthday, bodyform, blood, 
	 *                marriage, trainwith, interest, favbook, 
	 *                favmovie, favtv, idol, motto, wishlist, 
	 *                intro, education, schooltype, school, 
	 *                class, year, career, company, dept, 
	 *                beginyear, beginmonth, endyear, 
	 *                endmonth, isStar，pinyin，online
	 *
	 *                用户的基本属性：uid, name, gender, logo50
	 *
	 *                当fields字段为空的时候只返回用户的基本属性
	 * @param $start		int  起始值，默认为0
	 * @param $num			int  返回数量，默认为20，最多50，大于50只返回50个
	 * 
	 * @access public
	 * @return array
	 */
	public function friends_me( $fields = '' ,$start = 0, $num = 20 )
	{
		$params = array();
		$params['fields'] = $fields;
		$params['start'] = $start;
		$params['num'] = $num;
		return $this->oauth->get('friends/me', $params);
	}


	/**
	 * 获取两个用户间的好友关系
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取两个用户间的好友关系}
	 *
	 * @param $uid1		int  用户ID
	 * @param $uid2		int  用户ID
	 * 
	 * @access public
	 * @return array
	 */
	public function friends_relationship( $uid1 ,$uid2 )
	{
		$params = array();
		$params['uid1'] = $uid1;
		$params['uid2'] = $uid2;
		return $this->oauth->get('friends/relationship', $params);
	}


 	/**
	 * 获取两个用户的共同好友列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取两个用户的共同好友列表}
	 *
	 * @param $uid1		int  用户ID
	 * @param $uid2		int  用户ID
	 * 
	 * @access public
	 * @return array
	 */
	public function friends_mutual( $uid1 ,$uid2 )
	{
		$params = array();
		$params['uid1'] = $uid1;
		$params['uid2'] = $uid2;
		return $this->oauth->get('friends/mutual', $params);
	}


 	/**
	 * 添加好友
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=添加好友}
	 *
	 * @param $touid		int		要添加为好友的用户ID
	 * @param $code			string  用户输入的验证码
	 * @param $rcode		string  验证码识别串
	 * @param $content		string  好友请求说明
	 * 
	 * @access public
	 * @return array
	 */
	public function friends_add( $touid ,$code,$rcode = '' ,$content = '' )
	{
		$params = array();
		$params['touid'] = $touid;
		$params['code'] = $code;
		$params['rcode'] = $rcode;
		$params['content'] = $content;
		return $this->oauth->post('friends/add', $params);
	}


 	/**
	 * 获取用户安装组件的状态
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取用户安装组件的状态}
	 *
	 * @param $uids		string  用户ID，可以设置多个，以逗号分隔。如：123456,220993, 最多不能超过50个
	 * 
	 * @access public
	 * @return array
	 */
	public function app_status( $uids  )
	{
		$params = array();
		$params['uids'] = $uids;
		return $this->oauth->get('app/status', $params);
	}


 	/**
	 * 获取当前用户安装组件的好友uid列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户安装组件的好友uid列表}
	 *
	 * @param $uids		string  用户ID，可以设置多个，以逗号分隔。如：123456,220993, 最多不能超过50个
	 * 
	 * @access public
	 * @return array
	 */
	public function app_friends($start = 0, $num = 20 )
	{
		$params = array();
		$params['start'] = $start;
		$params['num'] = $num;
		return $this->oauth->get('app/friends', $params);
	}


 	/**
	 * 获取用户邀请成功的好友uid列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取用户邀请成功的好友uid列表}
	 *
	 * @param $uids		int  用户ID，可以设置多个，以逗号分隔。如：123456,220993, 最多不能超过50个
	 * @param $start	int  起始值，默认为0
	 * @param $num		int  返回数量，默认为20，最多50，大于50只返回50个
	 * 
	 * @access public
	 * @return array
	 */
	public function app_invited($uid , $start = 0, $num = 20 )
	{
		$params = array();
		$params['uid'] = $uid;
		$params['start'] = $start;
		$params['num'] = $num;
		return $this->oauth->get('app/invited', $params);
	}

 	/**
	 * 查询当前应用的剩余请求数
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=查询剩余请求数}
	 *
	 * @param 
	 * 
	 * @access public
	 * @return array
	 */
	public function app_rate_limit_status()
	{
		$params = array();
		return $this->oauth->get('app/rate_limit_status', $params);
	}


 	/**
	 * 发布一条记录(可以带一张图片)
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发表记录}
	 *
	 * @param array $records 要修改的学校信息。格式：array('key1'=>'value1', 'key2'=>'value2', .....)。
	 * 支持设置的项：
	 *  - content			string	发记录的内容(最多140个汉字或280个英文字母字符)。必填参数
	 *  - save_to_album		int		是否存到记录相册中，0/1-不保存/保存，默认为0不保存。
	 *  - location			string	记录的地理位置(目前仅在“我的记录”列表中显示)。
	 *  - lat				int		纬度 -90.0到+90.0，+表示北纬(目前暂不能显示)
	 *  - lon				string	经度 -180.0到+180.0，+表示东经(目前暂不能显示)
	 *  - sync_status		int		是否同步签名 0/1/2-无任何操作/同步/不同步，默认为0无任何操作
	 *  - spri				int		权限设置，0/1/2/3-任何人可见/好友可见/仅自己可见/好友及好友的好友可见
									默认为0任何人可见
	 *  - pic				string	发记录上传的图片，图片在10M以内，格式支持jpg/jpeg/gif/png/bmp
									pic和picurl只能选择其一，两个同时提交时，只取pic
	 *  - picurl			string	外部图片链接，图片在10M以内，格式支持jpg/jpeg/gif/png/bmp
									pic和picurl只能选择其一，两个同时提交时，只取pic
	 * 
	 * @access public
	 * @return array
	 */
	public function records_add($records)
	{
		return $this->oauth->post('records/add', $records);
	}

 	/**
	 * 获取我的记录列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=我的记录列表}
	 *
	 * @param $category int		分类条件，0/1/2/3/4/5/6/7
								全部/原创/转发/签名/公开/仅好友可见/仅自己可见/好友的好友可见
	 * @param $start	int		起始值，默认为0
	 * @param $num		int		返回数量，默认为20
	 * 
	 * @access public
	 * @return array
	 */
	public function records_me($category = 0, $start = 0, $num = 20 )
	{
		$params = array();
		$params['category']	= $category;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('records/me', $params);
	}


 	/**
	 * 获取所有好友的新记录
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=好友记录列表}
	 *
	 * @param $category		int  分类条件，0/1/2/3
								 全部/原创/转发/签名
	 * @param $start		int  起始值，默认为0
	 * @param $num			int  返回数量，默认为20
	 * 
	 * @access public
	 * @return array
	 */
	public function records_friends($category = 0, $start = 0, $num = 20 )
	{
		$params = array();
		$params['category']	= $category;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('records/friends', $params);
	}

 	/**
	 * 获取“随便看看”记录列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=随便看看记录列表}
	 *
	 * @param $start		int  起始值，默认为0
	 * @param $num			int  返回数量，默认为20
	 * 
	 * @access public
	 * @return array
	 */
	public function records_public($start = 0, $num = 20 )
	{
		$params = array();
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('records/public', $params);
	}

 	/**
	 * 获取某一用户的记录列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=单一用户的记录列表}
	 *
	 * @param $uid			int	 要添加为好友的用户ID
	 * @param $category		int  分类条件，0/1/2/3
								 全部/原创/转发/签名
	 * @param $start		int  起始值，默认为0
	 * @param $num			int  返回数量，默认为20
	 * 
	 * @access public
	 * @return array
	 */
	public function records_user($uid, $category = 0, $start = 0, $num = 20 )
	{
		$params = array();
		$params['uid']		= $uid;
		$params['category']	= $category;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('records/user', $params);
	}

 	/**
	 * 搜索记录，搜索全站隐私设置为“任何人可见”的记录
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=搜索记录}
	 *
	 * @param $keyword		string	 搜索关键词
	 * @param $start		int  起始值，默认为0
	 * @param $num			int  返回数量，默认为20
	 * 
	 * @access public
	 * @return array
	 */
	public function records_search($keyword, $start = 0, $num = 20 )
	{
		$params = array();
		$params['keyword']	= $keyword;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('records/search', $params);
	}

 	/**
	 * 获取记录的热门话题，返回数量不定
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=热门话题}
	 *
	 * @access public
	 * @return array
	 */
	public function records_topics()
	{
		$params = array();
		return $this->oauth->get('records/topics', $params);
	}

 	/**
	 * 删除记录
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=删除记录}
	 *
	 * @param $rid		int	 记录ID
	 * 
	 * @access public
	 * @return array
	 */
	public function records_delete( $rid )
	{
		$params = array();
		$params['rid']	= $rid;
		return $this->oauth->post('records/delete', $params);
	}

 	/**
	 * 创建照片专辑 需设置 scope=create_album 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=创建照片专辑}
	 *
	 * @param array $albuminfo 要修改的学校信息。格式：array('key1'=>'value1', 'key2'=>'value2', .....)。
	 * 支持设置的项：
	 *  - title			string	照片专辑标题。必填参数
	 *  - privacy		int		照片专辑隐私设置(0:任何人可见, 1:仅好友可见, 2:凭密码, 3:隐藏)，
								默认为0任何人可见。
	 *  - password		string	照片专辑密码(privacy为2时必填)。
	 *  - category		int		照片专辑分类(0:空, 1:美女, 2:帅哥, 3:宠物, 4:旅游, 5:美食, 6:家居, 7:街拍, 
								8:时尚,9:风景, 10:奇趣)
	 *  - allow_repaste	string	是否允许转贴，仅在privacy为1时有效，默认为允许转帖。其他情况下该参数无效
	 *  - location		string  照片专辑拍摄地点
	 *  - description	string  照片专辑描述
	 * 
	 * @access public
	 * @return array
	 */
	public function album_create( $albuminfo )
	{
		return $this->oauth->post('album/create', $albuminfo);
	}

 	/**
	 * 返回某个用户的照片专辑列表 需设置 scope=user_photo friends_photo 才能访问。如果只设置 scope=user_photo，只能获取	  到当前用户的照片专辑列表；如果只设置 scope=friends_photo，则只能获取到好友的照片专辑列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取照片专辑列表}
	 *
	 * @param $uid			int	 用户UID
	 * @param $start		int  起始值 默认为0
	 * @param $num			int  返回数量 默认为10
	 * 
	 * @access public
	 * @return array
	 */
	public function album_show( $uid, $start = 0, $num = 10)
	{
		$params = array();
		$params['uid']		= $uid;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('album/show', $params);
	}

 	/**
	 * 上传照片到指定的照片专辑 需设置 scope=upload_photo 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=上传照片}
	 *
	 * @param $albumid		int		照片专辑ID
	 * @param $title		string  照片标题
	 * @param $size			string  返回照片的大小尺寸. 可选值:(mid, small, cover)
	 * @param $send_news	int		是否发送动态. 可选值:(0:不发送动态, 1:发送动态)
	 * @param $pic			file	要上传的照片文件
	 * @param $tag			string  照片的标签
	 * 
	 * @access public
	 * @return array
	 */
	public function photo_upload( $albumid, $pic, $title = '', $size = 'mid', $send_news = 1, $tag = '')
	{
		$params = array();
		$params['albumid']		= $albumid;
		$params['title']		= $title;
		$params['size']			= $size;
		$params['send_news']	= $send_news;
		$params['pic']			= '@'.$pic['tmp_name'];
		$params['tag']			= $tag;
		return $this->oauth->post('photo/upload', $params ,true);
	}

 	/**
	 * 获取某个用户的某张照片或某照片专辑下的所有照片 需设置  scope=user_photo friends_photo 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取照片}
	 *
	 * @param $uid			int		用户UID
	 * @param $albumid		int		照片专辑ID
	 * @param $pid			int		照片ID
	 * @param $password		string	相册或照片的密码
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为50
	 * 
	 * @access public
	 * @return array
	 */
	public function photo_show( $uid, $albumid, $pid = '', $password = 'mid', $start = 0, $num = 50)
	{
		$params = array();
		$params['uid']		= $uid;
		$params['albumid']	= $albumid;
		$params['pid']		= $pid;
		$params['password']	= $password;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->post('photo/show', $params);
	}

 	/**
	 * 获取所有好友最新上传的照片列表 需设置  scope=friends_photo 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取照片}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 * 
	 * @access public
	 * @return array
	 */
	public function photo_latest( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('photo/latest', $params);
	}

 	/**
	 * 将当前用户创建为当前应用的玩友
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=创建玩友}
	 *
	 * @access public
	 * @return array
	 */
	public function game_friends_create()
	{
		$params = array();
		return $this->oauth->post('game_friends/create', $params);
	}

 	/**
	 * 设置用户是否可以被别人找到
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=玩友隐私设置}
	 *
	 * @param $privacy		int		玩友是否可以找到 1：可以 0：不可以
	 *
	 * @access public
	 * @return array
	 */
	public function game_friends_change_privacy($privacy)
	{
		$params = array();
		$params['privacy']	= $privacy;
		return $this->oauth->post('game_friends/change_privacy', $params);
	}

 	/**
	 * 当前用户向一位用户发送加为玩友的请求
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=玩友请求}
	 *
	 * @param $touid		int		要添加为玩友的用户ID
	 *
	 * @access public
	 * @return array
	 */
	public function game_friends_request($touid)
	{
		$params = array();
		$params['touid']	= $touid;
		return $this->oauth->post('game_friends/request', $params);
	}

 	/**
	 * 获取加当前用户为玩友的请求列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=玩友请求列表}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为1000
	 *
	 * @access public
	 * @return array
	 */
	public function game_friends_request_list( $start = 0, $num = 1000)
	{
		$params = array();
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('game_friends/request_list', $params);
	}

 	/**
	 * 处理玩友请求
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=处理玩友请求}
	 *
	 * @param $touid		int		要添加为玩友的用户ID
	 * @param $accept		int		是否接受 0：拒绝 1：接受
	 * @param $id			int		请求ID，为request_list接口返回数据中的id字段值
	 *
	 * @access public
	 * @return array
	 */
	public function game_friends_deal_request( $touid, $accept, $id)
	{
		$params = array();
		$params['touid']	= $touid;
		$params['accept']	= $accept;
		$params['id']		= $id;
		return $this->oauth->post('game_friends/deal_request', $params);
	}

 	/**
	 * 获取当前用户的玩友列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=用户玩友列表}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为1000
	 *
	 * @access public
	 * @return array
	 */
	public function game_friends_friends( $start = 0, $num = 1000)
	{
		$params = array();
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('game_friends/friends', $params);
	}

 	/**
	 * 设定条件搜索全部玩友
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=搜索玩友}
	 *
	 * @param $city			string	城市，例子：北京
	 * @param $agespan		int		年龄区间，可用参数：0,1959 1960,1969 1970,1979 1980,1984 1985,1989 1990
	 * @param $gender		int		0：男 1：女
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为1000
	 *
	 * @access public
	 * @return array
	 */
	public function game_friends_search( $city, $agespan, $gender, $start = 0,$num = 1000)
	{
		$params = array();
		$params['city']		= $city;
		$params['agespan']	= $agespan;
		$params['gender']	= $gender;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('game_friends/search', $params);
	}

 	/**
	 * 根据UID批量获取多个玩友的资料
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=批量获取玩友资料}
	 *
	 * @param $uids			string	用户ID，可以设置多个，以半角逗号分隔
	 *
	 * @access public
	 * @return array
	 */
	public function game_friends_show( $uids )
	{
		$params = array();
		$params['uids']		= $uids;
		return $this->oauth->get('game_friends/show', $params);
	}

 	/**
	 * 发送短消息 需设置 scope=send_message 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发送短消息}
	 *
	 * @param $content			string	短消息内容
	 * @param $fuids			string	要发送的用户id，用英文逗号分隔
	 * @param $attachment		string	短消息附件，单个文件大小在40M以内，总文件大小在100M以内
	 * @param $attachment1		string	短消息附件1，单个文件大小在40M以内，总文件大小在100M以内
	 * @param $attachment2		string	短消息附件2，单个文件大小在40M以内，总文件大小在100M以内
	 * @param $attachment3		string	短消息附件3，单个文件大小在40M以内，总文件大小在100M以内
	 * @param $attachment4		string	短消息附件4，单个文件大小在40M以内，总文件大小在100M以内
	 *
	 * @access public
	 * @return array
	 */
	public function message_send( $content,$fuids,$attachment,$attachment1,$attachment2,$attachment3,$attachment4 )
	{
		$params = array();
		$params['content']		= $content;
		$params['fuids']		= $fuids;
		$params['attachment']	= '@'.$attachment['tmp_name'];
		$params['attachment1']	= '@'.$attachment1['tmp_name'];
		$params['attachment2']	= '@'.$attachment2['tmp_name'];
		$params['attachment3']	= '@'.$attachment3['tmp_name'];
		$params['attachment4']	= '@'.$attachment4['tmp_name'];
		return $this->oauth->post('message/send', $params,true);
	}

 	/**
	 * 收件箱 需设置 scope=user_messagebox 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=收件箱}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为30
	 *
	 * @access public
	 * @return array
	 */
	public function message_inbox( $start = 0, $num = 30)
	{
		$params = array();
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('message/inbox', $params,true);
	}

 	/**
	 * 发件箱 需设置 scope=user_messagebox 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发件箱}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为30
	 *
	 * @access public
	 * @return array
	 */
	public function message_outbox( $start = 0, $num = 30)
	{
		$params = array();
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('message/outbox', $params,true);
	}

 	/**
	 * 回复短消息 需设置 scope=send_message  才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=回复短消息}
	 *
	 * @param $content			string	回复的消息内容
	 * @param $mid				string	被回复的消息ID
	 * @param $attachment		string	短消息附件，单个文件大小在40M以内，总文件大小在100M以内
	 * @param $attachment1		string	短消息附件1，单个文件大小在40M以内，总文件大小在100M以内
	 * @param $attachment2		string	短消息附件2，单个文件大小在40M以内，总文件大小在100M以内
	 * @param $attachment3		string	短消息附件3，单个文件大小在40M以内，总文件大小在100M以内
	 * @param $attachment4		string	短消息附件4，单个文件大小在40M以内，总文件大小在100M以内
	 *
	 * @access public
	 * @return array
	 */
	public function message_reply( $content,$mid,$attachment,$attachment1,$attachment2,$attachment3,$attachment4)
	{
		$params = array();
		$params['content']		= $content;
		$params['mid']			= $mid;
		$params['attachment']	= '@'.$attachment['tmp_name'];
		$params['attachment1']	= '@'.$attachment1['tmp_name'];
		$params['attachment2']	= '@'.$attachment2['tmp_name'];
		$params['attachment3']	= '@'.$attachment3['tmp_name'];
		$params['attachment4']	= '@'.$attachment4['tmp_name'];
		return $this->oauth->post('message/reply', $params,true);
	}

 	/**
	 * 短消息的详细信息 需设置 scope=user_messagebox 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=短消息详情}
	 *
	 * @param $mid			string	被回复的消息ID
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为30
	 *
	 * @access public
	 * @return array
	 */
	public function message_detail( $mid, $start = 0, $num = 30)
	{
		$params = array();
		$params['mid']		= $mid;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('message/detail', $params,true);
	}


 	/**
	 * 短消息附件下载 需设置 scope=user_messagebox 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=短消息附件下载}
	 *
	 * @param $uid			string	短消息附件属主
	 * @param $fileid		int		短消息附件ID
	 *
	 * @access public
	 * @return array
	 */
	public function message_attachment( $uid, $fileid)
	{
		$params = array();
		$params['uid']		= $uid;
		$params['fileid']	= $fileid;
		return $this->oauth->get('message/attachment', $params);
	}

 	/**
	 * 获取开心网所有表情
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=表情}
	 *
	 * @param $emotion_type			string	state或者face, 默认为face
	 *
	 * @access public
	 * @return array
	 */
	public function emotion_show( $emotion_type = 'face')
	{
		$params = array();
		$params['emotion_type']		= $emotion_type;
		return $this->oauth->get('emotion/show', $params);
	}

 	/**
	 * 创建圈子 需设置 scope=create_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=创建圈子}
	 *
	 * @param $title			string	圈子名字
	 * @param $category			string	圈子类型 0/1/2/3/4/5/6/7 默认/同学/家人/同事/饭友/玩友/闺蜜/其他
	 * @param $uids				string	要邀请的人uid 只能邀请好友
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_group_create( $title, $category = 0,$uids = '')
	{
		$params = array();
		$params['title']		= $title;
		$params['category']		= $category;
		$params['uids']			= $uids;
		return $this->oauth->post('rgroup/group_create', $params);
	}

 	/**
	 * 圈子列表 需设置 scope=user_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=圈子列表}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_groups( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('rgroup/groups', $params);
	}

 	/**
	 * 圈子动态 需设置 scope=user_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=圈子动态}
	 *
	 * @param $gid			int		圈子ID
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_talks( $gid,$start = 0, $num = 20)
	{
		$params = array();
		$params['gid']		= $gid;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('rgroup/talks', $params);
	}

 	/**
	 * 发布圈子动态 需设置 scope=user_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发布圈子动态}
	 *
	 * @param $gid			int			圈子ID
	 * @param $message		string		动态内容
	 * @param $pic			file		动态包含的图片
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_talk_create( $gid, $message, $pic)
	{
		$params = array();
		$params['gid']		= $gid;
		$params['message']	= $message;
		$params['pic']		= '@'.$pic['tmp_name'];
		return $this->oauth->post('rgroup/talk_create', $params,true);
	}

 	/**
	 * 圈子动态详情 需设置 scope=user_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=圈子动态详情}
	 *
	 * @param $gid			int		圈子ID
	 * @param $tid			int		动态ID
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 * @param $order		string	回复的排列顺序 asc：按照时间正序排列 desc：按照时间反序排列，默认desc
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_talk_detail( $gid, $tid,$start = 0, $num = 20, $order = 'desc')
	{
		$params = array();
		$params['gid']		= $gid;
		$params['tid']		= $tid;
		$params['start']	= $start;
		$params['num']		= $num;
		$params['order']	= $order;
		return $this->oauth->get('rgroup/talk_detail', $params);
	}

 	/**
	 * 圈子成员列表 需设置 scope=user_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=圈子成员列表}
	 *
	 * @param $gid			int		圈子ID
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_members( $gid,$start = 0, $num = 20 )
	{
		$params = array();
		$params['gid']		= $gid;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('rgroup/members', $params);
	}

 	/**
	 * 圈子消息 需设置 scope=user_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=圈子消息}
	 *
	 * @param $type			int		0/1 回复我的/我回复的
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_notices( $type,$start = 0, $num = 20 )
	{
		$params = array();
		$params['type']		= $type;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('rgroup/notices', $params);
	}

 	/**
	 * 圈子照片列表 需设置 scope=user_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=圈子照片列表}
	 *
	 * @param $gid			int		圈子ID
	 * @param $albumid		int		专辑ID，如果为空则展示整个圈子的照片
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_photos( $gid,$albumid = 0, $start = 0, $num = 20 )
	{
		$params = array();
		$params['gid']		= $gid;
		$params['albumid']	= $albumid;
		$params['start']	= $start;
		$params['num']		= $num;
		return $this->oauth->get('rgroup/photos', $params);
	}

 	/**
	 * 圈子照片 需设置 scope=user_rgroup 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=圈子照片列表}
	 *
	 * @param $gid		int		圈子ID
	 * @param $pid		int		照片ID
	 *
	 *
	 * @access public
	 * @return array
	 */
	public function rgroup_photo_detail( $gid,$pid )
	{
		$params = array();
		$params['gid']	= $gid;
		$params['pid']	= $pid;		
		return $this->oauth->get('rgroup/photo_detail', $params);
	}

 	/**
	 * 表达对某一资源的赞
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=表达对某一资源的赞}
	 *
	 * @param $objtype		string	被赞资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被赞资源的ID
	 * @param $ouid			int		被赞资源所有者的UID
	 *
	 * @access public
	 * @return array
	 */
	public function like_create( $objtype, $objid, $ouid )
	{
		$params = array();
		$params['objtype']	= $objtype;
		$params['objid']	= $objid;		
		$params['ouid']		= $ouid;		
		return $this->oauth->get('like/create', $params);
	}

 	/**
	 * 取消对某一资源的赞
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=取消对某一资源的赞}
	 *
	 * @param $objtype		string	被赞资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被赞资源的ID
	 * @param $ouid			int		被赞资源所有者的UID
	 *
	 * @access public
	 * @return array
	 */
	public function like_cancel( $objtype, $objid, $ouid )
	{
		$params = array();
		$params['objtype']	= $objtype;
		$params['objid']	= $objid;		
		$params['ouid']		= $ouid;		
		return $this->oauth->get('like/cancel', $params);
	}

 	/**
	 * 判断用户是否赞过某一资源
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=判断用户是否赞过某一资源}
	 *
	 * @param $objtype		string	被赞资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被赞资源的ID
	 * @param $ouid			int		被赞资源所有者的UID
	 * @param $uids			int		待判断的用户ID，多个uid用英文半角逗号分隔
	 *
	 * @access public
	 * @return array
	 */
	public function like_check( $objtype, $objid, $ouid, $uids)
	{
		$params = array();
		$params['objtype']	= $objtype;
		$params['objid']	= $objid;		
		$params['ouid']		= $ouid;		
		$params['uids']		= $uids;		
		return $this->oauth->get('like/check', $params);
	}

 	/**
	 * 获取对某一资源赞过的用户列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取对某一资源赞过的用户列表}
	 *
	 * @param $objtype		string	被赞资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被赞资源的ID
	 * @param $ouid			int		被赞资源所有者的UID
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为50
	 *
	 * @access public
	 * @return array
	 */
	public function like_show( $objtype, $objid, $ouid, $start = 0, $num = 50)
	{
		$params = array();
		$params['objtype']	= $objtype;
		$params['objid']	= $objid;		
		$params['ouid']		= $ouid;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('like/show', $params);
	}

 	/**
	 * 添加对某一资源的评论
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=添加对某一资源的评论}
	 *
	 * @param $objtype		string	被评论资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被评论资源的ID
	 * @param $ouid			int		被评论资源所有者的UID
	 * @param $content		string	评论内容
	 * @param $secret		int		是否悄悄话 0：不是悄悄话，默认为0 1：是悄悄话
	 *
	 * @access public
	 * @return array
	 */
	public function comment_create( $objtype, $objid, $ouid, $content = '', $secret = 0)
	{
		$params = array();
		$params['objtype']	= $objtype;
		$params['objid']	= $objid;		
		$params['ouid']		= $ouid;		
		$params['content']	= $content;		
		$params['secret']	= $secret;		
		return $this->oauth->post('comment/create', $params);
	}

 	/**
	 * 对某一资源的某条评论进行回复
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=对某一资源的某条评论进行回复}
	 *
	 * @param $objtype		string	被评论资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被评论资源的ID
	 * @param $ouid			int		被评论资源所有者的UID
	 * @param $thread_cid	int		评论的ID
	 * @param $owner		int		评论所有者的UID
	 * @param $content		string	回复内容
	 *
	 * @access public
	 * @return array
	 */
	public function comment_reply( $objtype, $objid, $ouid, $thread_cid, $owner, $content)
	{
		$params = array();
		$params['objtype']		= $objtype;
		$params['objid']		= $objid;		
		$params['ouid']			= $ouid;		
		$params['thread_cid']	= $thread_cid;		
		$params['owner']		= $owner;		
		$params['content']		= $content;		
		return $this->oauth->post('comment/reply', $params);
	}

 	/**
	 * 获取某一资源的所有评论
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取某一资源的所有评论}
	 *
	 * @param $objtype		string	被评论资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被评论资源的ID
	 * @param $ouid			int		被评论资源所有者的UID
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为10
	 *
	 * @access public
	 * @return array
	 */
	public function comment_list( $objtype, $objid, $ouid, $start = 0, $num = 10)
	{
		$params = array();
		$params['objtype']		= $objtype;
		$params['objid']		= $objid;		
		$params['ouid']			= $ouid;		
		$params['start']		= $start;		
		$params['num']			= $num;		
		return $this->oauth->get('comment/list', $params);
	}

 	/**
	 * 获取别人给我的评论 需设置 scope=user_comment 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取别人给我的评论}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function comment_comment_list( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']		= $start;		
		$params['num']			= $num;		
		return $this->oauth->get('comment/comment_list', $params);
	}

 	/**
	 * 获取我给别人的评论及回复 需设置 scope=user_comment 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取我给别人的评论及回复}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function comment_reply_list( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']		= $start;		
		$params['num']			= $num;		
		return $this->oauth->get('comment/reply_list', $params);
	}

 	/**
	 * 删除评论
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=删除评论}
	 *
	 * @param $uid		int		评论资源所有者的UID
	 * @param $cid		int		评论ID或评论主线ID
	 *
	 * @access public
	 * @return array
	 */
	public function comment_delete( $uid, $cid)
	{
		$params = array();
		$params['uid']		= $uid;		
		$params['cid']		= $cid;		
		return $this->oauth->get('comment/delete', $params);
	}

 	/**
	 * 创建转帖
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=创建转帖}
	 *
	 * @param $title		string		评论资源所有者的UID
	 * @param $rcontent		string		转帖内容，支持HTML标签
	 * @param $vpmethod		string		互动观点的类型 1：标签方式(tag) 2：投票方式(vote)
	 * @param $tag			string		标签观点文案，如果选择了标签方式，此项必填
	 * @param $answer1		string		投票观点文案，如果选择了投票方式，answer1-answer8其中两项必填
	 * @param $answer2		string		投票观点文案，如果选择了投票方式，answer1-answer8其中两项必填
	 * @param $answer3		string		投票观点文案，如果选择了投票方式，answer1-answer8其中两项必填
	 * @param $answer4		string		投票观点文案，如果选择了投票方式，answer1-answer8其中两项必填
	 * @param $answer5		string		投票观点文案，如果选择了投票方式，answer1-answer8其中两项必填
	 * @param $answer6		string		投票观点文案，如果选择了投票方式，answer1-answer8其中两项必填
	 * @param $answer7		string		投票观点文案，如果选择了投票方式，answer1-answer8其中两项必填
	 * @param $answer8		string		投票观点文案，如果选择了投票方式，answer1-answer8其中两项必填
	 * @param $source_url	string		原帖网址，如果想发布视频转帖，请直接填写视频原页面地址
	 *									目前支持的网站有：六间房、优酷、酷6、土豆网、youtube、新浪播客、
	 *									爆米花、我乐网
	 *
	 * @access public
	 * @return array
	 */
	public function repaste_create( $title, $rcontent,$vpmethod, $answer1 ='',$answer2 ='', $answer3 ='',$answer4 ='', $answer5 ='',$answer6 ='', $answer7 ='', $answer8 ='', $source_url ='')
	{
		$params = array();
		$params['title']		= $title;		
		$params['rcontent']		= $rcontent;		
		$params['vpmethod']		= $vpmethod;		
		$params['answer1']		= $answer1;		
		$params['answer2']		= $answer2;		
		$params['answer3']		= $answer3;		
		$params['answer4']		= $answer4;		
		$params['answer5']		= $answer5;		
		$params['answer6']		= $answer6;		
		$params['answer7']		= $answer7;		
		$params['answer8']		= $answer8;		
		$params['source_url']	= $source_url;		
		return $this->oauth->post('repaste/create', $params);
	}

 	/**
	 * 获取当前用户转帖列表 需设置 scope=user_repaste 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户转帖列表}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为10
	 * @param $repaste_type	string	转贴的类型，包括以下类型： 热门:hotrp，默认类型 最新:latest 视频:video 照片			*								:photo 文章:text 经典:classic
	 *
	 * @access public
	 * @return array
	 */
	public function repaste_me( $start = 0, $num = 10, $repaste_type = '' )
	{
		$params = array();
		$params['start']		= $start;		
		$params['num']			= $num;		
		$params['repaste_type']	= $repaste_type;		
		return $this->oauth->get('repaste/me', $params);
	}

 	/**
	 * 获取当前用户全部好友的转帖列表 需设置 scope=friends_repaste  才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户全部好友的转帖列表}
	 *
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为10
	 *
	 * @access public
	 * @return array
	 */
	public function repaste_friends( $start = 0, $num = 10 )
	{
		$params = array();
		$params['start']		= $start;		
		$params['num']			= $num;		
		return $this->oauth->get('repaste/friends', $params);
	}

 	/**
	 * 获取热门转帖列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取热门转帖列表}
	 *
	 * @param $repaste_type	string	转贴的类型，包括以下类型： 热门:hotrp，默认类型 最新:latest 视频:video 照片			*								:photo 文章:text 经典:classic
	 * @param $start		int		起始值 默认为0
	 * @param $num			int		返回数量 默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function repaste_public( $start = 0, $num = 20, $repaste_type = '' )
	{
		$params = array();
		$params['start']			= $start;		
		$params['num']				= $num;		
		$params['repaste_type']		= $repaste_type;		
		return $this->oauth->get('repaste/public', $params);
	}

 	/**
	 * 获取当前用户某个好友的转帖列表 需设置 scope=friends_repaste 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户某个好友的转帖列表}
	 *
	 * @param $uid			int		用户好友UID
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为10，一次最多能获取10条
	 *
	 * @access public
	 * @return array
	 */
	public function repaste_user( $uid, $start = 0, $num = 10)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		$params['uid']		= $uid;		
		return $this->oauth->get('repaste/user', $params);
	}

 	/**
	 * 获取转帖详细内容 需设置 scope=user_repaste friends_repaste 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取转帖详细内容}
	 *
	 * @param $uid			int		用户好友UID
	 * @param $urpid		int		转贴的唯一ID
	 *
	 * @access public
	 * @return array
	 */
	public function repaste_show( $uid, $urpid)
	{
		$params = array();
		$params['uid']		= $uid;		
		$params['urpid']	= $urpid;		
		return $this->oauth->get('repaste/show', $params);
	}

 	/**
	 * 添加转帖标签观点 需设置 scope=create_repaste 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=添加转帖标签观点}
	 *
	 * @param $uid				int		用户好友UID
	 * @param $urpid			int		转贴的唯一ID
	 * @param $tag				int		TAD 名称,标签长度不能超过16个汉字
	 * @param $share_repaste	int		是否转帖（默认share） share：转帖 unshare：不转帖
	 *
	 * @access public
	 * @return array
	 */
	public function repaste_add_tag( $uid, $urpid,$tag, $share_repaste = 'share')
	{
		$params = array();
		$params['uid']				= $uid;		
		$params['urpid']			= $urpid;		
		$params['tag']				= $tag;		
		$params['share_repaste']	= $share_repaste;		
		return $this->oauth->post('repaste/add_tag', $params);
	}

 	/**
	 * 选择互动观点 需设置 scope=create_repaste 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=选择互动观点}
	 *
	 * @param $uid				int		用户好友UID
	 * @param $urpid			int		转贴的唯一ID
	 * @param $tagid			int		TAD ID (互动类型为TAG时,传递此参数)
	 * @param $voteid			int		投票答案的ID,值可选(1,2,3,4,5,6,7,8)
	 *									注意：tagid和voteid参数，必须二选其一
	 * @param $share_repaste	int		是否转帖（默认share） share：转帖 unshare：不转帖
	 *
	 * @access public
	 * @return array
	 */
	public function repaste_interact( $uid, $urpid,$tagid = 0, $voteid = 0, $share_repaste = 'share')
	{
		$params = array();
		$params['uid']				= $uid;
		$params['urpid']			= $urpid;
		$params['tagid']			= $tagid;
		$params['voteid']			= $voteid;
		$params['share_repaste']	= $share_repaste;
		return $this->oauth->post('repaste/interact', $params);
	}

 	/**
	 * 获取新消息
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取新消息}
	 *
	 * @param $msg_type				int		消息类型（非必填项，默认是全部字段）多个字段用半角逗号隔开
	 *			字段				说明
	 *			message				短消息
	 *			sysmsg_notice		通知
	 *			sysmsg_friends		好友请求
	 *			sysmsg_birthday		生日提醒
	 *			sysmsg_mention		提到我
	 *			sysmsg_forward		转发
	 *			bbs_msg				留言
	 *			bbs_reply			留言回复
	 *			rgroup_msg			圈子“回复我”
	 *			rgroup_reply		圈子“我回复”
	 *			comment				评论
	 *			reply				评论回复
	 *
	 * @access public
	 * @return array
	 */

	public function msg_summary( $msg_type = '')
	{
		$params = array();
		$params['msg_type']	= $msg_type;
		return $this->oauth->get('msg/summary', $params);
	}

 	/**
	 * 获取新消息
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取新消息}
	 *
	 * @param $msg_type				int		消息类型（必填）多个字段用半角逗号隔开
	 *			字段				说明
	 *			message				短消息
	 *			sysmsg_notice		通知
	 *			sysmsg_friends		好友请求
	 *			sysmsg_birthday		生日提醒
	 *			sysmsg_mention		提到我
	 *			sysmsg_forward		转发
	 *			bbs_msg				留言
	 *			bbs_reply			留言回复
	 *			rgroup_msg			圈子“回复我”
	 *			rgroup_reply		圈子“我回复”
	 *			comment				评论
	 *			reply				评论回复
	 *
	 * @access public
	 * @return array
	 */

	public function msg_clear( $msg_type)
	{
		$params = array();
		$params['msg_type']	= $msg_type;
		return $this->oauth->post('msg/clear', $params);
	}

 	/**
	 * 发表日记 需设置 scope=create_diary 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发表日记}
	 *
	 * @param $title			string		日记标题
	 * @param $content			string		日记内容，支持HTML标签
	 * @param $privacy			int			日记隐私设置 0:任何人可见 1:仅好友可见，默认为1 2:凭密码 3:隐藏
	 * @param $password			string		日记密码，privacy为2时必填
	 * @param $allow_repaste	int			是否允许好友转帖 1：允许 0：不允许
	 * @param $atfriends		string		提到的好友，多个好友用半角逗号分隔
	 *
	 * @access public
	 * @return array
	 */
	public function diary_create( $title, $content, $privacy = 1, $password = '', $allow_repaste = '', $atfriends = '')
	{
		$params = array();
		$params['title']			= $title;
		$params['content']			= $content;
		$params['privacy']			= $privacy;
		$params['password']			= $password;
		$params['allow_repaste']	= $allow_repaste;
		$params['atfriends']		= $atfriends;
		return $this->oauth->post('diary/create', $params);
	}

 	/**
	 * 获取当前用户日记列表 需设置 scope=user_diary 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户日记列表}
	 *
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为10，一次最多能获取10条
	 *
	 * @access public
	 * @return array
	 */
	public function diary_me( $start = 0, $num = 10)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('diary/me', $params);
	}

 	/**
	 * 获取当前用户全部好友的日记列表 需设置 scope=friends_diary 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户日记列表}
	 *
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为10，一次最多能获取10条
	 *
	 * @access public
	 * @return array
	 */
	public function diary_friends( $start = 0, $num = 10)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('diary/friends', $params);
	}

 	/**
	 * 获取提到当前用户的日记列表 需设置 scope=user_diary 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户日记列表}
	 *
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为10，一次最多能获取10条
	 *
	 * @access public
	 * @return array
	 */
	public function diary_tag( $start = 0, $num = 10)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('diary/tag', $params);
	}

 	/**
	 * 获取当前用户某个好友的日记列表 需设置 scope=friends_diary 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户某个好友的日记列表}
	 *
	 * @param $uid			int		用户ID
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为10，一次最多能获取10条
	 *
	 * @access public
	 * @return array
	 */
	public function diary_user( $uid ,$start = 0, $num = 10)
	{
		$params = array();
		$params['uid']		= $uid;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('diary/user', $params);
	}

 	/**
	 * 获取日记详细内容 需设置 scope=user_diary friends_diary 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取日记详细内容}
	 *
	 * @param $uid			int		用户ID
	 * @param $did			int		日记ID
	 * @param $password		int		加密日记的访问密码 仅在日记权限设置为“凭密码访问”时有效

	 *
	 * @access public
	 * @return array
	 */
	public function diary_show( $uid ,$did, $password = '')
	{
		$params = array();
		$params['uid']		= $uid;		
		$params['did']		= $did;		
		$params['password']	= $password;		
		return $this->oauth->post('diary/show', $params);
	}

 	/**
	 * 成为公共主页粉丝 需设置 scope=user_diary friends_diary 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=成为公共主页粉丝}
	 *
	 * @param $pageid			int		机构主页UID，必填

	 *
	 * @access public
	 * @return array
	 */
	public function page_add_fan( $pageid )
	{
		$params = array();
		$params['pageid']	= $pageid;		
		return $this->oauth->post('page/add_fan', $params);
	}

 	/**
	 * 在好友留言板上留言
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=留言}
	 *
	 * @param $uid			int		留言对象的UID
	 * @param $content		string		留言内容
	 * @param $secret		int		是否悄悄话，默认不是悄悄话(0：不是悄悄话；1：悄悄话)

	 *
	 * @access public
	 * @return array
	 */
	public function board_create( $uid, $content, $secret = 0 )
	{
		$params = array();
		$params['uid']		= $uid;		
		$params['content']	= $content;		
		$params['secret']	= $secret;		
		return $this->oauth->post('board/create', $params);
	}

 	/**
	 * 查看我的留言板
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=留言列表}
	 *
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为20，一次最多能获取50条
	 *
	 * @access public
	 * @return array
	 */
	public function board_me( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('board/me', $params);
	}

 	/**
	 * 好友留言板
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=好友留言板}
	 *
	 * @param $uid			int		用户ID
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为20，一次最多能获取50条
	 *
	 * @access public
	 * @return array
	 */
	public function board_user( $uid, $start = 0, $num = 20)
	{
		$params = array();
		$params['uid']		= $uid;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('board/user', $params);
	}

 	/**
	 * 查看我的留言回复列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=留言回复列表}
	 *
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为20，一次最多能获取50条
	 *
	 * @access public
	 * @return array
	 */
	public function board_reply_list( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('board/reply_list', $params);
	}

 	/**
	 * 删除留言及回复
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=删除留言}
	 *
	 * @param $uid			int		用户ID
	 * @param $bid			int		留言ID或留言主线ID
	 *
	 * @access public
	 * @return array
	 */
	public function board_delete( $uid, $bid)
	{
		$params = array();
		$params['uid']	= $uid;		
		$params['bid']	= $bid;		
		return $this->oauth->get('board/delete', $params);
	}

 	/**
	 * 发布好友动态 需设置 scope=send_feed 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发表日记}
	 *
	 * @param $linktext	string		动态里面的链接文字，不超过15个汉字。例如，链接文案：去xx帮忙。
	 * @param $link		string		动态里的链接地址。必须以http或https开头。
	 * @param $text		string		发送动态所使用的文案，不超过60个汉字，否则会被截断。
	 *								该文案可以有{_USER_} {_USER_TA_}变量，解析时会被替换为当前用户名字和他/她。
	 *								例如，动态文案：{_USER_} 在做XX任务时遇到了强大的XX，快去帮帮{_USER_TA_}！
	 * @param $word		string		动态里用户的附言，不能超过50个字
	 * @param $picurl	string		发送动态所使用的图片地址，如果动态分享中需要发布图片，则此项必填。 单张图片时，大小	*								为80×80。
	 *
	 * @access public
	 * @return array
	 */
	public function feed_send( $linktext, $link, $text,$word = '', $picurl = '' )
	{
		$params = array();
		$params['linktext']		= $linktext;
		$params['link']			= $link;
		$params['text']			= $text;
		$params['word']			= $word;
		$params['picurl']		= $picurl;
		return $this->oauth->post('feed/send', $params);
	}

 	/**
	 * 发送系统消息 需设置 scope=send_sysnews才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发送系统消息}
	 *
	 * @param $fuids	string		系统消息接收用户的uid,多个用户用半角逗号隔开，最多30个
	 * @param $linktext	string		动态里面的链接文字，不超过15个汉字。例如，链接文案：去xx帮忙。
	 * @param $link		string		动态里的链接地址。必须以http或https开头。
	 * @param $text		string		发送动态所使用的文案，不超过60个汉字，否则会被截断。
	 *								该文案可以有{_USER_} {_USER_TA_}变量，解析时会被替换为当前用户名字和他/她。
	 *								例如，动态文案：{_USER_} 在做XX任务时遇到了强大的XX，快去帮帮{_USER_TA_}！
	 * @param $word		string		动态里用户的附言，不能超过50个字
	 * @param $picurl	string		发送动态所使用的图片地址，如果动态分享中需要发布图片，则此项必填。 单张图片时，大小	*								为80×80。
	 *
	 * @access public
	 * @return array
	 */
	public function sysnews_send( $fuids, $linktext, $link, $text,$word = '', $picurl = '' )
	{
		$params = array();
		$params['fuids']		= $fuids;
		$params['linktext']		= $linktext;
		$params['link']			= $link;
		$params['text']			= $text;
		$params['word']			= $word;
		$params['picurl']		= $picurl;
		return $this->oauth->post('sysnews/send', $params);
	}

 	/**
	 * 发送邀请 需设置 scope=send_sysnews才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发送邀请}
	 *
	 * @param $fuids	string		系统消息接收用户的uid,多个用户用半角逗号隔开，最多30个
	 * @param $word		string		动态里用户的附言，不能超过50个字
	 *
	 * @access public
	 * @return array
	 */
	public function sysnews_invitation( $fuids,$word )
	{
		$params = array();
		$params['fuids']		= $fuids;
		$params['word']			= $word;
		return $this->oauth->post('sysnews/invitation', $params);
	}

 	/**
	 * 对某一资源进行转发
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=对某一资源进行转发}
	 *
	 * @param $objtype		string	被赞资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被赞资源的ID
	 * @param $ouid			int		被赞资源所有者的UID
	 * @param $content		string	转发附言内容
	 *
	 * @access public
	 * @return array
	 */
	public function forward_create( $objtype, $objid, $ouid, $content)
	{
		$params = array();
		$params['objtype']	= $objtype;
		$params['objid']	= $objid;		
		$params['ouid']		= $ouid;		
		$params['content']	= $content;		
		return $this->oauth->post('forward/create', $params);
	}

 	/**
	 * 对某一资源的转发再次转发
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=对某一资源的转发再次转发}
	 *
	 * @param $objtype		string	被赞资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被赞资源的ID
	 * @param $ouid			int		被赞资源所有者的UID
	 * @param $fid			int		转发ID
	 * @param $content		string	转发附言内容
	 *
	 * @access public
	 * @return array
	 */
	public function forward_reforward( $objtype, $objid, $ouid, $fid, $content)
	{
		$params = array();
		$params['objtype']	= $objtype;
		$params['objid']	= $objid;		
		$params['ouid']		= $ouid;		
		$params['fid']		= $fid;		
		$params['content']	= $content;		
		return $this->oauth->post('forward/reforward', $params);
	}

 	/**
	 * 获取某一资源的所有转发
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=留言回复列表}
	 *
	 * @param $objtype		string	被赞资源的类型，目前支持的资源有： photo, album, records, diary, repaste
	 *
	 *	objtype		资源名称	objid 对照表：
	 *
	 *	类型名称	资源名称	objid
	 *	photo		照片		pid
	 *	album		照片专辑	albumid
	 *	records		记录		rid
	 *	diary		日记		did
	 *	repaste		转帖		urpid
	 * @param $objid		int		被赞资源的ID
	 * @param $ouid			int		被赞资源所有者的UID
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为10
	 *
	 * @access public
	 * @return array
	 */
	public function forward_list( $objtype, $objid, $ouid,$start = 0, $num = 10)
	{
		$params = array();
		$params['objtype']	= $objtype;
		$params['objid']	= $objid;		
		$params['ouid']		= $ouid;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('forward/list', $params);
	}

 	/**
	 * 获取当前用户资源的转发详情 需设置 scope=user_forward 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取当前用户资源的转发详情}
	 *
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为10
	 *
	 * @access public
	 * @return array
	 */
	public function forward_me( $start = 0, $num = 10)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('forward/me', $params);
	}

 	/**
	 * 在某个地点签到 需设置 scope=places_checkin 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=签到}
	 *
	 * @param array $info		签到选项。格式：array('key0'=>'value0', 'key1'=>'value1', ....)。支持的key:
	 *  - lat			string		纬度
	 *  - lon			string		经度
	 *  - loc_name		string		地点名称
	 *  - addr			string		地址
	 *  - content		string		签到内容
	 *  - pic			string		图片
	 *  - coop_link		string		来源链接，通过此链接点击到合作方页面
	 *  - privacy		string		隐私设置（默认为0） 0：所有人可见 1：好友可见 2：仅自己可见
	 *  - country		string		国家 如中国
	 *  - province		string		省份 如陕西省
	 *	参数pic应为'@'.$_FILES['pic']['tmp_name']
	 *
	 * @access public
	 * @return array
	 */
	public function lbs_checkin( $info)
	{
		$params = $info;

		return $this->oauth->post('lbs/checkin', $params,true);
	}

 	/**
	 * 我的签到列表 需设置 scope=user_places 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=我的签到列表}
	 *
	 * @param $start		int		起始位置，默认为0
	 * @param $num			int		返回转帖的个数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function lbs_me( $start = 0,$num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('lbs/me', $params);
	}

 	/**
	 * 好友签到列表 需设置 scope=friends_places 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=好友签到列表}
	 *
	 * @param $lat			string		纬度
	 * @param $lon			string		经度
	 * @param $start		int			起始位置，默认为0
	 * @param $num			int			返回转帖的个数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function lbs_friend( $lat, $lon,$start = 0,$num = 20)
	{
		$params = array();
		$params['lat']		= $lat;		
		$params['lon']		= $lon;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('lbs/friend', $params);
	}

 	/**
	 * 附近的好友 需设置 scope=friends_places 才能访问
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=附近的好友}
	 *
	 * @param $lat			string		纬度 必选
	 * @param $lon			string		经度 必选
	 * @param $start		int			起始位置，默认为0
	 * @param $num			int			返回转帖的个数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function lbs_near_friends( $lat, $lon,$start = 0,$num = 20)
	{
		$params = array();
		$params['lat']		= $lat;		
		$params['lon']		= $lon;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('lbs/near_friends', $params);
	}

 	/**
	 * 附近陌生人
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=附近陌生人}
	 *
	 * @param $lat			string		纬度 必选
	 * @param $lon			string		经度 必选
	 * @param $start		int			起始位置，默认为0 可选
	 * @param $num			int			返回陌生人的个数，默认为20 可选
	 *
	 * @access public
	 * @return array
	 */
	public function lbs_near_users( $lat, $lon,$start = 0,$num = 20)
	{
		$params = array();
		$params['lat']		= $lat;		
		$params['lon']		= $lon;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('lbs/near_users', $params);
	}

 	/**
	 * 获取附近的照片
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=附近的照片}
	 *
	 * @param $lat			string		纬度 必选
	 * @param $lon			string		经度 必选
	 * @param $start		int			起始位置，默认为0 可选
	 * @param $num			int			返回照片的个数，默认为20 可选
	 *
	 * @access public
	 * @return array
	 */
	public function lbs_photo( $lat, $lon,$start = 0,$num = 20)
	{
		$params = array();
		$params['lat']		= $lat;		
		$params['lon']		= $lon;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('lbs/photo', $params);
	}

 	/**
	 * 新增签到地点
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=添加新位置}
	 *
	 * @param $lat			string		纬度
	 * @param $lon			string		经度
	 * @param $loc_name		string		地点名称
	 * @param $addr			string		地址
	 *	以上参数均为必选参数
	 *
	 * @access public
	 * @return array
	 */
	public function lbs_create( $lat, $lon,$loc_name,$addr )
	{
		$params = array();
		$params['lat']		= $lat;		
		$params['lon']		= $lon;		
		$params['loc_name']	= $loc_name;		
		$params['addr']		= $addr;		
		return $this->oauth->get('lbs/create', $params);
	}

 	/**
	 * 搜索电影
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=电影搜索}
	 *
	 * @param $keyword		string		搜索关键字
	 * @param $start		int			起始位置，默认为0
	 * @param $num			int			返回电影的个数，默认为10
	 *
	 * @access public
	 * @return array
	 */
	public function film_search( $keyword, $start = 0,$num = 10)
	{
		$params = array();
		$params['keyword']	= $keyword;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		return $this->oauth->get('film/search', $params);
	}

 	/**
	 * 将电影标记为已看过，并对该电影进行打分、评论等
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=看过电影}
	 *
	 * @param $mid			int			电影id
	 * @param $score		int			给电影打分(范围0-5)
	 * @param $comment		string		影评内容
	 * @param $media		int			观看介质(0.其他1.电影 2.DVD 3.网上)
	 * @param $place		string		观看地点
	 * @param $friends		int			与谁一起看(填写uid，多个用逗号隔开，最多30个)
	 * @param $privacy		string		隐私设置(可选，1.所有人可见 2.仅好友可见 默认为1)
	 * @param $send_news	int			是否发布动态（1.发布 0.不发布，默认为1）
	 *
	 * @access public
	 * @return array
	 */
	public function film_watched( $mid, $score = '',$comment = '',$media = '', $place = '',$friends = '',$privacy = 1,$send_news = 1)
	{
		$params = array();
		$params['mid']			= $mid;		
		$params['score']		= $score;		
		$params['comment']		= $comment;		
		$params['media']		= $media;		
		$params['place']		= $place;		
		$params['friends']		= $friends;		
		$params['privacy']		= $privacy;		
		$params['send_news']	= $send_news;		
		return $this->oauth->post('film/watched', $params);
	}

 	/**
	 * 将电影标记为想看
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=想看电影}
	 *
	 * @param $mid			int			电影id
	 * @param $send_news	int			是否发布动态（1.发布 0.不发布，默认为1）
	 *
	 * @access public
	 * @return array
	 */
	public function film_wish( $mid, $send_news = 1)
	{
		$params = array();
		$params['mid']			= $mid;		
		$params['send_news']	= $send_news;		
		return $this->oauth->post('film/wish', $params);
	}

 	/**
	 * 我想看的电影列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=我想看的电影列表}
	 *
	 * @param $start		int			起始位置，默认为0
	 * @param $num			int			返回电影的个数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function film_wishlist( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('film/wishlist', $params);
	}

 	/**
	 * 我推荐的电影列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=我推荐的电影列表}
	 *
	 * @param $start		int			起始位置，默认为0
	 * @param $num			int			返回电影的个数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function film_recommendlist( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('film/recommendlist', $params);
	}

 	/**
	 * 我看过的电影列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=我看过的电影列表}
	 *
	 * @param $start		int			起始位置，默认为0
	 * @param $num			int			返回电影的个数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function film_sawlist( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('film/sawlist', $params);
	}

 	/**
	 * 推荐电影
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=推荐电影}
	 *
	 * @param $mid			int			电影id（必填）
	 * @param $reason		string		推荐理由（必填）
	 * @param $uids			int			起始位置，默认为0
	 * @param $firstpage	int			是否在首页个人资料栏里推荐此电影（0不 1是，默认0）
	 *
	 * @access public
	 * @return array
	 */
	public function film_recommend( $mid, $reason, $uids = '', $firstpage = 0)
	{
		$params = array();
		$params['mid']			= $mid;		
		$params['reason']		= $reason;		
		$params['uids']			= $uids;		
		$params['firstpage']	= $firstpage;		
		
		return $this->oauth->post('film/recommend', $params);
	}

 	/**
	 * 好友们看过和想看的电影
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=好友的电影}
	 *
	 * @param $start		int			起始位置，默认为0
	 * @param $num			int			返回转帖的个数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function film_friendtouch( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('film/friendtouch', $params);
	}

 	/**
	 * 重置游戏通知页右侧用户未处理的游戏通知数目
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=重置游戏通知}
	 *
	 * @param $uid		int			未阅游戏通知需要被重置用户的UID
	 * @param $num		int			未阅游戏通知需要被重置的数目
	 *
	 * @access public
	 * @return array
	 */
	public function gamenotice_set( $uid, $num)
	{
		$params = array();
		$params['uid']	= $uid;		
		$params['num']	= $num;		
		
		return $this->oauth->post('gamenotice/set', $params);
	}

 	/**
	 * 更新在游戏通知页右侧用户未处理的游戏通知数目
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=增减游戏通知数}
	 *
	 * @param $uid		int			未阅游戏通知需要被重置用户的UID
	 * @param $num		int			未阅游戏通知需要被重置的数目
	 *
	 * @access public
	 * @return array
	 */
	public function gamenotice_update( $uid, $num)
	{
		$params = array();
		$params['uid']	= $uid;		
		$params['num']	= $num;		
		
		return $this->oauth->post('gamenotice/update', $params);
	}

 	/**
	 * 参与投票
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=参与投票}
	 *
	 * @param $vid		int			投票ID
	 * @param $answers	int			投票项，多个选项用“,”分隔
	 *
	 * @access public
	 * @return array
	 */
	public function vote_reply( $vid, $answers)
	{
		$params = array();
		$params['vid']		= $vid;		
		$params['answers']	= $answers;		
		
		return $this->oauth->post('vote/reply', $params);
	}

 	/**
	 * 发起投票
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=发起投票}
	 *	=
	 * @param array $vinfo			投票选项。格式：array('key0'=>'value0', 'key1'=>'value1', ....)。支持的key:
	 *  - maxnum		int			最大允许投票数（1-20） 必填参数
	 *  - gender		int			性别限制 0：无限制 1：限制女性 2：限制男性 必填参数
	 *  - answer1		string		选项 必填参数
	 *  - answer2		string		选项 必填参数
	 *  - answer3		string		选项
	 *  - answer4		string		选项
	 *  - answer5		string		选项
	 *  - answer6		string		选项
	 *  - answer7		string		选项
	 *  - answer8		string		选项
	 *  - answer9		string		选项
	 *  - answer10		string		选项
	 *  - answer11		string		选项
	 *  - answer12		string		选项
	 *  - answer13		string		选项
	 *  - answer14		string		选项
	 *  - answer15		string		选项
	 *  - answer16		string		选项
	 *  - answer17		string		选项
	 *  - answer18		string		选项
	 *  - answer19		string		选项
	 *  - answer20		string		选项
	 *  - enddate		string		截止时间（格式是2011-11-12 10:11:12） 必填参数
	 *  - friendonly	string		是否只允许好友投票 必填参数
	 *  - topic			string		投票标题 必填参数
	 *  - intor			string		投票简介
	 *
	 * @access public
	 * @return array
	 */
	public function vote_post( $vinfo)
	{
		$params = $vinfo;
		
		return $this->oauth->post('vote/post', $params);
	}

 	/**
	 * 好友们的最新投票
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=获取最新的好友投票动态}
	 *
	 * @param $start		int			投票列表起始，默认为0
	 * @param $num			int			投票列表展示数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function vote_feed( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('vote/feed', $params);
	}

 	/**
	 * 获取好友参与过的投票
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=好友的投票列表}
	 *
	 * @param $uid			int			好友的uid
	 * @param $start		int			投票列表起始，默认为0
	 * @param $num			int			投票列表展示数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function vote_friend( $uid,$start = 0, $num = 20)
	{
		$params = array();
		$params['uid']		= $uid;		
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('vote/friend', $params);
	}

 	/**
	 * 我发起的投票
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=我发起的投票}
	 *
	 * @param $start		int			投票列表起始，默认为0
	 * @param $num			int			投票列表展示数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function vote_mypost( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('vote/mypost', $params);
	}

 	/**
	 * 我的投票
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=我的投票}
	 *
	 * @param $start		int			投票列表起始，默认为0
	 * @param $num			int			投票列表展示数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function vote_me( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('vote/me', $params);
	}

 	/**
	 * 投票详情
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=投票详情}
	 *
	 * @param $vid		int		投票ID
	 *
	 * @access public
	 * @return array
	 */
	public function vote_detail( $vid )
	{
		$params = array();
		$params['vid']	= $vid;		
		
		return $this->oauth->get('vote/detail', $params);
	}

 	/**
	 * 获取开心网全站的最新投票
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=最新投票}
	 *
	 * @param $start		int			投票列表起始，默认为0
	 * @param $num			int			投票列表展示数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function vote_newlist( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('vote/newlist', $params);
	}

 	/**
	 * 热门投票
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=热门投票}
	 *
	 * @param $start		int			投票列表起始，默认为0
	 * @param $num			int			投票列表展示数，默认为20
	 *
	 * @access public
	 * @return array
	 */
	public function vote_hot( $start = 0, $num = 20)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('vote/hot', $params);
	}

 	/**
	 * 普通礼物列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=普通礼物列表}
	 *
	 * @param $start		int			取得礼物列表起始值，默认为0
	 * @param $num			int			取得礼物的数目，默认为10
	 *
	 * @access public
	 * @return array
	 */
	public function gift_list( $start = 0, $num = 10)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('gift/list', $params);
	}

 	/**
	 * 赠送礼物
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=赠送礼物}
	 *
	 * @param $gid			int			礼物id
	 * @param $reveuids		string		接收礼物的好友uid，多个uid用半角逗号隔开	
	 * @param $words		string		礼物id
	 * @param $anonymous	int			是否匿名赠送（0不是，1是，默认为0）
	 * @param $invisible	int			是否悄悄赠送（0不是，1是，默认为0）
	 *
	 * @access public
	 * @return array
	 */
	public function gift_send( $gid, $reveuids, $words = '', $anonymous = 0, $invisible = 0)
	{
		$params = array();
		$params['gid']			= $gid;		
		$params['reveuids']		= $reveuids;		
		$params['words']		= $words;		
		$params['anonymous']	= $anonymous;		
		$params['invisible']	= $invisible;		
		
		return $this->oauth->post('gift/send', $params);
	}

 	/**
	 * 获取当前用户收到的礼物列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=收到的礼物列表}
	 *
	 * @param $start		int			用前户收到的礼物列表起始值，默认为0
	 * @param $num			int			用前户收到的礼物的数目，默认为10
	 *
	 * @access public
	 * @return array
	 */
	public function gift_received( $start = 0, $num = 10)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('gift/received', $params);
	}

 	/**
	 * 当前用户送出的礼物列表
	 *
	 * 对应API：{@link http://wiki.open.kaixin001.com/index.php?id=送出礼物列表}
	 *
	 * @param $start		int			用前户送出礼物列表起始值，默认为0
	 * @param $num			int			用前户送出礼物的数目，默认为10
	 *
	 * @access public
	 * @return array
	 */
	public function gift_delivered( $start = 0, $num = 10)
	{
		$params = array();
		$params['start']	= $start;		
		$params['num']		= $num;		
		
		return $this->oauth->get('gift/delivered', $params);
	}


}
