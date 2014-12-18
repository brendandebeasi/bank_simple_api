<?php
class Simple {
    private $_endpoint = 'https://bank.simple.com/';
    private $_username;
    private $_password;
    private $_csrf;
    private $_cookieFile = 'cookie.txt';

    public function __construct($username, $password) {
        $this->_username = $username;
        $this->_password = $password;
        $this->_fetchCSRF();
        $this->_login();
    }

    private function _login() {
        $post = http_build_query(array('username'=>$this->_username, 'password'=>$this->_password, '_csrf'=>$this->_csrf));
        $response = $this->_request('signin',false,false,$post);
    }

    private function _fetchCSRF() {
        $response = $this->_request('signin',true);
        if(!preg_match('~name="_csrf" content="(.*?)"~', $response, $meta)) throw new Exception('Cannot find CSRF');
        $this->_csrf = $meta[1];
    }

    private function _request($location, $newSession = false, $returnBody = true, $postData = NULL) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_endpoint . $location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if($newSession) curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        else curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_cookieFile);

        if(!empty($postData)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        if(!$returnBody) curl_setopt($ch, CURLOPT_HEADER, 1);

        $response = curl_exec($ch);

        return $response;
    }

    public function card() {
        return $this->_request('card',false);
    }

    public function balance() {
        return $this->_request('account/balances',false);
    }

    public function linkedAccounts() {
        return $this->_request('linked-accounts',false);
    }

    public function transactions() {
        return $this->_request('transactions/data',false);
    }
}