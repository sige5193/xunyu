build-windows : 
	mkdir build/windows -p
	cp src/* build/windows/ -r
	rm -f build/windows/composer.json
	rm -f build/windows/composer.lock

pack-windows :
	iscc xunyu.iss

clean :
	rm -fr build/windows
