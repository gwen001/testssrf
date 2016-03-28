<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

class TestSsrfRequest extends HttpRequest
{
	private $sanitizer = array();

	private $_url = '';

	private $_headers = '';

	private $_cookies = '';

	private $_params = '';

	private $ssrf = false;


	public function setSanitizer( $v ) {
		if( !is_array($v) ) {
			$v = array( $v );
		}
		$this->sanitizer = $v;
	}


	private function sanitize($v) {
		return str_replace( $this->sanitizer, '', $v );
	}


	public function getOriginalUrl( $null='' ) {
		return $this->_url;
	}
	public function setUrl($v, $null='' ) {
		parent::setUrl( $this->sanitize($v) );
		$this->_url = $v;
	}


	public function getOriginalHeader( $key ) {
		return $this->_headers[$key];
	}
	public function setHeader($v, $key) {
		parent::setHeader( $this->sanitize($v), $key );
		$this->_headers[$key] = $v;
	}


	public function getOriginalCookies( $null='' ) {
		return $this->_cookies;
	}
	public function setCookies($v, $null = '') {
		parent::setCookies( $this->sanitize($v) );
		$this->_cookies = $v;
	}


	public function getOriginalParams( $null='' ) {
		return $this->_cookies;
	}
	public function setParams($v, $null = '')
	{
		parent::setParams( $this->sanitize($v) );
		$this->_params = $v;
	}


	public function getSsrf() {
		return $this->ssrf;
	}
	public function setSsrf($v) {
		$this->ssrf = (bool)$v;
	}
}

?>
