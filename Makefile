build-windows : 
	mkdir build/windows -p
	cp src/* build/windows/ -r
	rm -f build/windows/composer.json
	rm -f build/windows/composer.lock
	cp resource/windows/php build/windows/ -r
	cp resource/windows/webdriver build/windows/ -r
	cp resource/windows/xunyu.bat build/windows/xunyu.bat
