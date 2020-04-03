# 语法手册

- [Variable](./variable.md)
- [Include](./keywords.md#include)

**浏览器测试**

启动/停止

```
# use browser {浏览器} {版本}
use browser ie
close browser
```

支持的浏览器

| 名称    | 标识    | 版本 |
| ------- | ------- | ---- |
| IE      | ie      |      |
| Chrome  | chrome  |      |
| Firefox | firefox |      |

支持的动作列表

| 名称       | 指令          | 参数                                                         | 说明 |
| ---------- | ------------- | ------------------------------------------------------------ | ---- |
| 点击       | click         | - elem 元素选择符                                            |      |
| 双击       | dblclick      | - elem 元素选择符                                            |      |
| 输入       | input         | - elem 元素选择符   <br />- text 文本内容                    |      |
| 停留       | hover         | - elem 元素选择符                                            |      |
| 选择       | select        | - elem 元素选择符<br />- type : [name\|value] 选择类型<br />- value 选择的名称或者取值 |      |
| 上传       | upload        | - elem 元素选择符<br />- file 文件路径<br />- ... 当上传多个文件时， 这里继续追加文件路径 |      |
| 弹框：确认 | alert-accept  |                                                              |      |
| 弹框：取消 | alert-dismiss |                                                              |      |
| 弹框：输入 | alert-input   | - text 输入文本                                              |      |

支持的等待列表

| 名称       | 指令                | 参数                                   | 说明 |
| ---------- | ------------------- | -------------------------------------- | ---- |
| 元素可见   | wait-elem-visiable  | - elem 元素选择符                      |      |
| 元素不可见 | wait-elem-invisible | - elem 元素选择符                      |      |
| 当前URL    | wait-url            | - url 等待的url / 正则匹配             |      |
| 元素文本   | wait-elem-text      | - elem 元素选择符<br />- text 文本内容 |      |
| 标题       | wait-title          | - title 标题内容 / 正则匹配            |      |
| 元素存在   | wait-elem-exists    | - elem 元素选择符                      |      |
| 弹框       | wait-alert-present  |                                        |      |

支持 的验证列表

| 名称       | 指令                   | 参数                                   | 说明 |
| ---------- | ---------------------- | -------------------------------------- | ---- |
| 元素可见   | assert-elem-visiable   | - elem 元素选择符                      |      |
| 元素不可见 | assert-elem-invisiable | - elem 元素选择符                      |      |
| 当前URL    | assert-url             | - url 等待的url                        |      |
| 元素文本   | assert-elem-text       | - elem 元素选择符<br />- text 文本内容 |      |
| 弹框出现   | assert-alert-present   |                                        |      |
| 页面标题   | assert-title           | - title 标题                           |      |
| 元素存在   | assert-exists          | - elem 元素选择符                      |      |

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



**系统变量**

| 名称     | 变量          | 示例     |
| -------- | ------------- | -------- |
| 当前日期 | Date.yyyymmdd | 20121225 |
| 当前时间 | Time.hhiiss   | 100012   |



- Date.now

- Date.today

- Date.year

- Date.month

- Date.date

- Time.hour

- Time.second

- Math.rand

  

**环境变量**

- Env.os
- Env.params