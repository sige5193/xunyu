build-windows : 
	mkdir build/windows -p
	cp src/* build/windows/ -r
	cp resource/windows/php build/windows/ -r
	cp resource/windows/webdriver build/windows/ -r
