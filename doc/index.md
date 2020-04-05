# 语法手册

**Script**

- [Variable](./variable.md)
- [Include](./keywords.md#include)



**Operators**

- [Browser](./operator-browser.md)



**函数**

```
func 函数名 参数名1 参数名2 ...
  # 函数体
  return 返回值
endfunc

# 获取函数返回值
call 函数名 参数名1 参数名2
set myFuncReturns $函数名.return-value

# 系统函数调用
call DateStringToTime "2020-01-01 00:00:00"
set time $return-value
```

- return是可选的， 如果没有返回值， 则返回空字符串，如果没有return语句则返回空字符串
- 返回值的使用方式为`$return-value`
- 自定义函数不得与系统函数重名， 否则会出错
- 函数和指令的区分：首先匹配指令， 然后匹配函数， 这样可以省略`call`关键字



**控制语句**

```
if 条件
  # BODY
endif

if 条件
  # BODY
elseif 条件
  # BODY
else 
  # BODY
endif
```

```
loop 次数 => 索引变量名
  # BODY
endloop

loop 次数
  # BODY
endloop
```

```
while 条件
  # BODY
endwhile
```

```
# 暂时没有支持
each 列表 => 项目变量名 : 索引变量名
  # BODY
endeach

each 列表 => 项目变量名
  # BODY
endeach
```



**系统指令**

- **use / active /close** ： 

  ```
  use chrome 1.5 as 采购商
  use chrome 1.5 as 销售商
  active 采购商
  active 销售商
  close 采购商
  close 销售商
  close
  ```
 `use` 用于启动对应的Operator， `active`用于在Operator之间进行切换，     `close`用于关闭对应的Operator
  



**系统函数**

| 名称       | 命令          | 参数                                         |
| ---------- | ------------- | -------------------------------------------- |
| 字符串追加 | string.append | - var-name 变量名<br />- string 追加的字符串 |


