<?php
class TradeGecoClient {
    public $shop_domain;
    private $token;
    private $api_key;
    private $secret;
    private $last_response_headers = null;
    
    public function __construct($shop_domain, $token, $api_key, $secret) {
        $this->name = "TradeGecoClient";
        $this->shop_domain = $shop_domain;
        $this->token = $token;
        $this->api_key = $api_key;
        $this->secret = $secret;
    }
    
    // Get the URL required to request authorization
    public function getAuthorizeUrl($scope, $redirect_url='') {
        require 'properties/application.properties.php';
        require 'properties/' . $profile . '-application.properties.php';
        $url = "{$this->shop_domain}/oauth/authorize?client_id={$this->api_key}";
        $url .= "&redirect_uri=$serverUrl/index.php&response_type=code";
        return $url;
    }
    
    // Once the User has authorized the app, call this with the code to get the access token
    public function getAccessToken($code) {
        require 'properties/application.properties.php';
        require 'properties/' . $profile . '-application.properties.php';
        // POST to  POST https://SHOP_NAME.myshopify.com/admin/oauth/access_token
        $url = "{$this->shop_domain}/oauth/token";
        $payload = "client_id={$this->api_key}&client_secret={$this->secret}&code=$code&grant_type=authorization_code";
        $payload .= "&redirect_uri=$serverUrl/index.php";
        $response = $this->curlHttpApiRequest('POST', $url, '', $payload, array());
        $response = json_decode($response, true);
        //echo $response['access_token'];
        require_once 'util/loggerUtil.php';
        m_log("Response : ".json_encode($response));
        if (isset($response['access_token']))
            return $response;
            return '';
            
    }
    
    public function callsMade(){
        return $this->shopApiCallLimitParam(0);
    }
    
    public function callLimit(){
        return $this->shopApiCallLimitParam(1);
    }
    
    public function callsLeft($response_headers){
        return $this->callLimit() - $this->callsMade();
    }
    
    public function call($method, $path, $params=array()){
        require 'properties/application.properties.php';
        require 'properties/' . $profile . '-application.properties.php';
        
        $baseurl = "$tradegecoURL/";
        $url = $baseurl.ltrim($path, '/');
        $query = in_array($method, array('GET','DELETE')) ? $params : array();
        $payload = in_array($method, array('POST','PUT')) ? stripslashes(json_encode($params)) : array();
        $request_headers = in_array($method, array('POST','PUT','GET')) ? array("Content-Type: application/json; charset=utf-8", 'Expect:') : array();
        
        $request_headers[] = 'Authorization: Bearer ' . $this->token;
        $response = $this->curlHttpApiRequest($method, $url, $query, $payload, $request_headers);
        $response =  json_decode($response,TRUE);
        m_log("Response : ".json_encode($response));
        if(isset($response['type']) && $response['type'] === 'unauthorized'){
            
            $refreshToken = getRefreshToken($this->token);
            m_log("Refresh Token pulled from DB". $refreshToken);
            $newUrl = "{$this->shop_domain}/oauth/token";
            $refresh_Token = $refreshToken['RefreshToken'];
            $newPayload = "client_id={$this->api_key}&client_secret={$this->secret}&refresh_token=$refresh_Token&grant_type=refresh_token";
            $newPayload .= "&redirect_uri=$serverUrl/index.php";
            $response = $this->curlHttpApiRequest('POST', $newUrl, '', $newPayload, array());
            $response = json_decode($response, true);
            m_log("Refreshing Expired Token Response : ".json_encode($response));
            if (isset($response['access_token'])){
                saveTokens($response['access_token'], $response['refresh_token'], $refreshToken['StoreUrl']);
                $_SESSION['token'] = $response['access_token'];
                $this->token = $response['access_token'];
                $request_headers = in_array($method, array('POST','PUT','GET')) ? array("Content-Type: application/json; charset=utf-8", 'Expect:') : array();
                $request_headers[] = 'Authorization: Bearer ' . $this->token;
                $response = $this->curlHttpApiRequest($method, $url, $query, $payload, $request_headers);
                $response =  json_decode($response,TRUE);
            }
            
        }
        return $response;
        
     }
     
    private function curlHttpApiRequest($method, $url, $query='', $payload='', $request_headers=array())
    {
        m_log("API Request: ".$url);
        $url = $this->curlAppendQuery($url, $query);
        $ch = curl_init($url);
        $this->curlSetopts($ch, $method, $payload, $request_headers);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($errno) throw new TradeGecoCurlException($error, $errno);
        list($message_headers, $message_body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
        $this->last_response_headers = $this->curlParseHeaders($message_headers);
        m_log("API Response: ".$message_body);
        return $message_body;
    }
    
    private function curlAppendQuery($url, $query)
    {
        if (empty($query)) return $url;
        if (is_array($query)) return "$url?".http_build_query($query);
        else return "$url?$query";
    }
    
    private function curlSetopts($ch, $method, $payload, $request_headers)
    {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ohShopify-php-api-client');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($request_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        
        if ($method != 'GET' && !empty($payload))
        {
            if (is_array($payload)) $payload = http_build_query($payload);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $payload);
        }
    }
    
    private function curlParseHeaders($message_headers)
    {
        $header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
        $headers = array();
        list(, $headers['http_status_code'], $headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
        foreach ($header_lines as $header_line)
        {
            list($name, $value) = explode(':', $header_line, 2);
            $name = strtolower($name);
            $headers[$name] = trim($value);
        }
        
        return $headers;
    }
    
    private function shopApiCallLimitParam($index)
    {
        if ($this->last_response_headers == null)
        {
            throw new Exception('Cannot be called before an API call.');
        }
        $params = explode('/', $this->last_response_headers['http_x_shopify_shop_api_call_limit']);
        return (int) $params[$index];
    }
}

class TradeGecoCurlException extends Exception { }
class TradeGecoApiException extends Exception
{
    protected $method;
    protected $path;
    protected $params;
    protected $response_headers;
    protected $response;
    
    function __construct($method, $path, $params, $response_headers, $response)
    {
        $this->method = $method;
        $this->path = $path;
        $this->params = $params;
        $this->response_headers = $response_headers;
        $this->response = $response;
        
        parent::__construct($response_headers['http_status_message'], $response_headers['http_status_code']);
    }
    
    function getMethod() { return $this->method; }
    function getPath() { return $this->path; }
    function getParams() { return $this->params; }
    function getResponseHeaders() { return $this->response_headers; }
    function getResponse() { return $this->response; }
}
?>
