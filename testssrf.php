<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

include( 'Utils.php' );
include( 'HttpRequest.php' );
include( 'TestSsrf.php' );
include( 'TestSsrfRequest.php' );


// parse command line
{
	$testssrf = new TestSsrf();
	$reference = new TestSsrfRequest();

	$ssl = false;
	$argc = $_SERVER['argc'] - 1;

	for ($i = 1; $i <= $argc; $i++) {
		switch ($_SERVER['argv'][$i]) {
			case '-cl':
				$reference->setContentLength(true);
				break;

			case '-f':
				$request_file = $_SERVER['argv'][$i + 1];
				$i++;
				break;

			case '-i':
				$testssrf->setIp($_SERVER['argv'][$i + 1]);
				$i++;
				break;

			case '-h':
				Utils::help();
				break;

			case '-r':
				$reference->setRedirect(false);
				break;

			case '-s':
				$reference->setSsl(true);
				break;

			case '-t':
				$testssrf->setTolerance($_SERVER['argv'][$i + 1]);
				$i++;
				break;

			case '-p':
				$testssrf->setPort($_SERVER['argv'][$i + 1]);
				$i++;
				break;

			default:
				Utils::help('Unknown option: '.$_SERVER['argv'][$i]);
		}
	}

	if( !$testssrf->getIp() ) {
		Utils::help('IP adress not found!');
	}
}
// ---


// init
{
	$testssrf->setReference( $reference );

	if( !$reference->loadFile($request_file) ) {
		Utils::help('Request file not found!');
	}

	$testssrf->runReference();
}
// ---


// main loop
{
	$testssrf->run();
}
// ---


exit();

?>
