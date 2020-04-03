# environment

environment file use to set up different configuration values for different environment,  you can change env name by `--env`  param. as default, xunyu would try to find the env file named `env.ini`

```bash
$ xunyu example.xy --env=test
#or
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

