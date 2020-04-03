build-windows : 
	mkdir build/windows -p
	cp src/* build/windows/ -r
	rm -f build/windows/composer.json
	rm -f build/windows/composer.lock
	powershell "Compress-Archive build/windows build/windows/build.zip"

clean :
	rm -fr build/windows
