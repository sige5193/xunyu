# Keywords

### include

the include command use to include another test case into current case. 

```
include t001.xy

# other commands
```

also, you are able to pass arguments to inlcuded file

```
include t001.xy p1=v1;p2=v2
```

and, in current case, you can use `$argv` to access these params.

```
log $argv.p1
# output : v1
log $argv.p2
# output : v2
log $argv.p3
# output : (empty)
```

