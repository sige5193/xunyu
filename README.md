# Xunyu (荀彧)

yet another automation testing tool



### Installing

Install xunyu for Windows, Mac and Linux from release page, then get start

- download the last release [here](https://github.com/sige-chen/xunyu/releases) .
- unzip the package,  and enter the folder
- click `install.bat` for windows or `install.sh` for linux and mac 



**code example**

```
% an example text case
% @author sige
% @link https://sahala.suanhetao.com/issues-132 

func login username password
  open https://sahala.suanhetao.com/login.html
  input #user $username
  input #pwd $password
  click #btn-login
  wait-url https://sahala.suanhetao.com/
endfunc

# login as a shop owner
use browser firefox as shop
call login $env.username $env.password
open https://sahala.suanhetao.com/search.html

# click top 10 items in search page
loop 10 => $gindex
  set gid #G00
  string.append gid $gindex
  click $gid 
endloop

# login as a customer
use browser chrome 80 as customer
call login $env.cusername $env.cpassword
open https://sahala.suanhetao.com/search.html
input #q burton
click _search
assert-elem-text #res-count 12

# close all browsers
close customer
close shop
```



**code recording**

\~\~\~
