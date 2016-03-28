<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

class TestSsrf
{
	const DEFAULT_TOLERANCE = 5;
	const INJECTION_DELIM = '$';

	/**
	 * @var TestIdorRequest
	 *
	 * reference request
	 */
	private $reference = null;

	/**
	 * @var int
	 *
	 * tolerance for output result
	 */
	private $tolerance = self::DEFAULT_TOLERANCE; // percent
	private $_tolerance = 0; // real value

	/**
	 * @var string
	 *
	 * ip to test
	 */
	private $ip = null;

	/**
	 * @var integer
	 *
	 * port to test
	 */
	private $port = null;

	/**
	 * @var string
	 *
	 * payload
	 */
	private $payload = '';

	/**
	 * @var array
	 *
	 * results table
	 */
	private $t_result = array();


	public function getTolerance() {
		return $this->tolerance;
	}
	public function setTolerance( $v ) {
		$this->tolerance = (float)$v;
		return true;
	}


	public function getIp() {
		return $this->ip;
	}
	public function setIp( $v ) {
		$this->ip = $v;
		return true;
	}


	public function getPort() {
		return $this->port;
	}
	public function setPort( $v ) {
		$this->port = (int)$v;
		return true;
	}


	public function getReference() {
		return $this->reference;
	}
	public function setReference( $v ) {
		$this->reference = $v;
		$this->reference->setSanitizer( self::INJECTION_DELIM );
		return true;
	}


	public function runReference()
	{
		$this->reference->request();
		//var_dump( $this->reference );
		//$this->reference->export();

		$this->_tolerance = (int)($this->reference->getResultLength() * $this->getTolerance() / 100);
		echo "\n-> Reference: RC=" . $this->reference->getResultCode() . ', RL=' . $this->reference->getResultLength() . ', T=' . $this->getTolerance() . '%, T2=' . $this->_tolerance . "\n";
		//exit();
	}


	public function run()
	{
		echo "\n";

		$r = clone $this->reference;

		$this->payload = $this->ip;
		if( $this->port ) {
			$this->payload = $this->ip.':'.$this->port;
		}

		$n_injection = 0;
		$n_injection += $this->inject( $r, 'getOriginalUrl', 'setUrl' );
		foreach( $this->reference->getHeaders() as $k=>$v ) {
			$n_injection += $this->inject( $r, 'getOriginalHeader', 'setHeader', $k );
		}
		$n_injection += $this->inject( $r, 'getOriginalCookies', 'setCookies' );
		$n_injection += $this->inject( $r, 'getOriginalParams', 'setParams' );

		if( $n_injection ) {
			$r->request();
			//var_dump( $r );
			//$r->export();
			$this->result( $r );
			$this->t_result[] = $r;
		}

		unset( $r );

		echo $n_injection ." injection point found\n\n";
	}


	private function inject( $r, $getter, $setter, $param='' )
	{
		preg_match_all('#\\' . self::INJECTION_DELIM . '([^\\' . self::INJECTION_DELIM . ']+)\\' . self::INJECTION_DELIM . '#', $this->reference->$getter($param), $matches); // original values cannot be empty
		//var_dump( $matches );
		$cnt = count($matches[0]);

		foreach( $matches[0] as $k=>$m ) {
			$r->$setter(str_replace($m, $this->payload, $r->$getter($param)), $param);
			//var_dump( $r->$getter($param) );
		}

		return $cnt;
	}


	private function result( $r )
	{
		$color = 'white';
		$diff = $r->getResultLength() - $this->reference->getResultLength();
		$text = 'U=' . $r->getUrl() . ', C=' . $r->getResultCode() . ', L=' . $r->getResultLength() . ', D=' . $diff;

		if( abs($diff) <= $this->_tolerance )
		{
			// match ?!
			if( $this->isReference($r) ) {
				// this is the reference
				$color = 'dark_grey';
				$text .= ' -> REFERENCE';
			} else {
				$r->setSsrf(true);
				$text .= ' -> LENGTH OK';
			}
		}
		else
		{
			// no match !!
			if( $this->isReference($r) ) {
				// this is the reference
				$color = 'red';
				$text .= ' -> ERROR';
			} else {
				//echo ' -> NORMAL';
			}
		}

		if( $r->getSsrf() ) {
			if( $r->getResultCode() == $this->reference->getResultCode() ) {
				$color = 'green';
				$text .= ' AND CODE MATCH!';
			} else {
				$color = 'yellow';
				$text .= ' BUT CODE DO NOT MATCH!';
			}
		}

		Utils::_print( $text, $color );
		echo "\n";
	}


	private function isReference( $request )
	{
		if( $request->getUrl(true)!=$this->reference->getUrl(true) || $request->getHeaders(true)!=$this->reference->getHeaders(true)
			|| $request->getCookies(true)!=$this->reference->getCookies(true) || $request->getParams(true)!=$this->reference->getParams(true) ) {
			return false;
		}

		return true;
	}
}

?>
