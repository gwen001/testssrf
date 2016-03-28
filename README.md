# TestIdor
PHP tool to test Server Side Request Forgery aka SSRF.  
Note that this is an automated tool, manual check is still required.  

```
Usage: php testssrf.php [OPTIONS] -i <ip> -f <request_file>

Options:
	-cl	force Content-Length header
	-f	source file of the orignal request
	-h	print this help
	-i	ip adress to test
	-p	port to test, default=none
	-r	do not follow redirection
	-s	force https
	-t	set tolerance for result output, default=5%

Examples:
	testssrf.php -i 127.0.0.7 -f request.txt
	testssrf.php -s -r -127.0.0.1 -f request.txt
	testssrf.php -t 1 -127.0.0.1 -p 22 -f request.txt
```

I don't believe in license.  
You can do want you want with this program.  
