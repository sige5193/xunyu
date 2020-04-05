# Operator Browser

#### **Use Command**

```
use browser {browser-name} {version}
```



#### **System**

Windows 10



#### **Browser**

| Type    | Name    | Version |
| ------- | ------- | ------- |
| Chrome  | chrome  | 80      |
| FIrefox | firefox |         |
| IE      | ie      |         |



#### **Commands**

| Command                 | Name              | Params                     |
| ----------------------- | ----------------- | -------------------------- |
| open url                | open              | - url                      |
| click an elem           | click             | - selector                 |
| double click an elem    | dblclick          | - selector                 |
| input string            | input             | - selector <br />- content |
| upload file             | upload            | - selector<br />- file     |
| hover an elem           | hover             | - selector                 |
| select                  | select            | - selector<br />- text     |
| accept an alert         | alert-accept      |                            |
| dismiss an alert        | alert-dismiss     |                            |
| input string to alert   | alert-input       | - content                  |
| take an screenshot      | take-screenshot   | - path                     |
| switch to frame         | switch-to-frame   |                            |
| switch to parent window | switch-to-parent  |                            |
| close current tab       | close-current-tab |                            |
| blur from an elem       | blur              | - selector                 |
| close tab               | close-tab         | - index                    |
| close tab by url        | close-tab-by-url  | - url                      |



**Waiting**

| Command                         | Name                 | Params                                 |
| ------------------------------- | -------------------- | -------------------------------------- |
| wait for page title             | wait-title           | - title <br />- timeout                |
| wait for alert present          | wait-alert-present   |                                        |
| wait for current url            | wait-url             | - url<br />- timeout                   |
| wait for elem exists in dom     | wait-elem-exists     | - selector<br />- timeout              |
| wait for elem not exists in dom | wait-elem-not-exists | - selector<br />- timeout              |
| wait for elem visiable          | wait-elem-visiable   | - selector<br />- timeout              |
| wait for elem invisible         | wait-elem-invisible  | - selector<br />- timeout              |
| wait for elem text              | wait-elem-text       | - selector<br />- text <br />- timeout |



**Assertion**

| Command                   | Name                  | Params                                |
| ------------------------- | --------------------- | ------------------------------------- |
| assert current page title | assert-title          | - title<br />- message                |
| assert current url        | assert-url            | - url<br />- message                  |
| assert elem exists        | assert-elem-exists    | - selector<br />- message             |
| assert elem visiable      | assert-elem-visiable  | - selector<br />- message             |
| assert elem invisible     | assert-elem-invisible | - selector<br />- message             |
| assert elem text          | assert-elem-text      | - selector<br />- text<br />- message |
| assert alert present      | assert-alert-present  | - message                             |

