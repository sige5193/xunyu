# Variable

it's very easy to define a variable in test case, here is an example

```
set var-name string-value
```

name of variable supports a-z, 0-9 and the character `-`. all variables are string in xunyu, then you don't have to think about the data type.



you can use variable in params to command by add `$` before the variable name.

```
input #input-elem $var-name
```

also, you are able to put variable into a string to setup a param, like this

```
input $input-elem http://{$website}/index.php
```





### Environment

environment file use to set up different configuration values for different environment,  you can change env name by `--env`  param. as default, xunyu would try to find the env file named `env.ini`

```bash
$ xunyu example.xy --env=test.ini
```

**env file**

evn file is a normal ini file. for example

```in
username = acc001
password = pass001

[server]
address = https://127.0.0.1:8088
```

**how to use env in test script**

before the test start, xunyu will load env vars into global var list, you can use env value by `$env`

```
log $env.username
log $env.server.address
```





### System Variables

there are some system variables defined inside the xunyu. and here is the list

| Name         | Variable      | Example  |
| ------------ | ------------- | -------- |
| Current date | Data.yyyymmdd | 20200101 |
| Current time | Time.hhiiss   | 125959   |



