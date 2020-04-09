import {CompletionItem, CompletionItemKind, TextDocumentPositionParams} from 'vscode-languageserver';
/**
 * 
 */
export class XunyuLanguageServerCompletionItems {
  /**
   * 
   */
  static getCompletionItems( _textDocumentPosition: TextDocumentPositionParams ) : CompletionItem[] {
    let items : CompletionItem[] = [];

    // keywords
    items.push({label:'use',kind: CompletionItemKind.Keyword});
    items.push({label:'active',kind: CompletionItemKind.Keyword});
    items.push({label:'close',kind: CompletionItemKind.Keyword});
    items.push({label:'call',kind: CompletionItemKind.Keyword});
    items.push({label:'func',kind: CompletionItemKind.Keyword});
    items.push({label:'return',kind: CompletionItemKind.Keyword});
    items.push({label:'endfunc',kind: CompletionItemKind.Keyword});
    items.push({label:'if',kind: CompletionItemKind.Keyword});
    items.push({label:'elseif',kind: CompletionItemKind.Keyword});
    items.push({label:'else',kind: CompletionItemKind.Keyword});
    items.push({label:'endif',kind: CompletionItemKind.Keyword});
    items.push({label:'loop',kind: CompletionItemKind.Keyword});
    items.push({label:'endloop',kind: CompletionItemKind.Keyword});
    items.push({label:'include',kind: CompletionItemKind.Keyword});
    items.push({label:'log',kind: CompletionItemKind.Keyword});
    items.push({label:'set',kind: CompletionItemKind.Keyword});

    // operator : browser
    items.push({label:'open',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'click',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'dblclick',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'input',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'upload',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'hover',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'select',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'alert-accept',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'alert-dismiss',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'alert-input',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'take-screenshot',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'switch-to-frame',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'switch-to-parent',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'blur',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'wait-title',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'wait-url',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'wait-elem-exists',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'wait-elem-not-exists',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'wait-elem-visiable',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'wait-elem-invisible',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'wait-elem-text',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'wait-alert-present',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'assert-title',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'assert-url',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'assert-elem-exists',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'assert-elem-visiable',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'assert-elem-invisible',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'assert-elem-text',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'assert-alert-present',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'close-tab',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'close-tab-by-url',kind: CompletionItemKind.Method, data:{operator:'browser'}});
    items.push({label:'close-current-tab',kind: CompletionItemKind.Method, data:{operator:'browser'}});
  
    items.push({label:'method',kind: CompletionItemKind.Method, data:{operator:'poster'}});
    items.push({label:'address',kind: CompletionItemKind.Method, data:{operator:'poster'}});
    items.push({label:'body-add',kind: CompletionItemKind.Method, data:{operator:'poster'}});
    items.push({label:'body-get',kind: CompletionItemKind.Method, data:{operator:'poster'}});
    items.push({label:'pre-request-handler',kind: CompletionItemKind.Method, data:{operator:'poster'}});
    items.push({label:'send',kind: CompletionItemKind.Method, data:{operator:'poster'}});
    items.push({label:'response-read-as-json',kind: CompletionItemKind.Method, data:{operator:'poster'}});

    // functions
    items.push({label:'string.append',kind: CompletionItemKind.Function});
    items.push({label:'string.read-file',kind: CompletionItemKind.Function});
    items.push({label:'string.md5',kind: CompletionItemKind.Function});
    items.push({label:'string.uppercase',kind: CompletionItemKind.Function});
    items.push({label:'system.sleep',kind: CompletionItemKind.Function});

    // const vars
    items.push({label:'date.yyyymmdd',kind: CompletionItemKind.Property});
    items.push({label:'date.timestamp',kind: CompletionItemKind.Property});
    items.push({label:'string.uuid',kind: CompletionItemKind.Property});
    items.push({label:'time.hhiiss',kind: CompletionItemKind.Property});

    return items;
  }

  /**
   * 
   */
  static completionItemResolve ( item: CompletionItem ) : CompletionItem {
    let rmap : any = {
      // keywords
      'use'     : {detail:'start an operator',documentation:'start an operator and set to active\n\nexample : use browser chrome as customer1\n\n - name : browser | poster\n - version : optional'},
      'active'  : {detail:'switch operator',documentation:'active an operator\n\nexample : active customer1\n\n- name : name of operator'},
      'close'   : {detail:'close an operator',documentation:'close an operator, if no name is given, current operator would be closed\n\nexample : close customer1\n\n- name : optional, name of operator'},
      'call'    : {detail:'call user function',documentation:'call an user defined function\n\nexample : call my-function param1 param2\n\n - name : name of function\n - params,... param list to function'},
      'func'    : {detail:'define an user function',documentation:'define an function\n\nexample : \nfunc my-function-login account password\n  open https://www.example.com/login\n  input #password $account\n  input #account $password\n  click #btn-login\nendfunc\n\n - name : name of function\n - params,.. list of params to function'},
      'return'  : {detail:'exit function and return',documentation:'return an value to function caller, if no value to return, empty string would be returned.\n\nexample : return some-value\n\n - value optional, the value return to caller'},
      'endfunc' : {detail:'end function defination',documentation:'end the function defination\n\nexample : \nfunc my-func\n  # TODO\nendfun'},
      'if'      : {detail:'if',documentation:'the if construct,It allows for conditional execution of code fragments.\n\nexample : \nif $action = demo\n  # TODO\nendif'},
      'elseif'  : {detail:'else if',documentation:'elseif, as its name suggests, is a combination of if and else. Like else, it extends an if statement to execute a different statement in case the original if expression evaluates to FALSE. However, unlike else, it will execute that alternative expression only if the elseif conditional expression evaluates to TRUE.\n\nexample : \nif $action = demo\n  # TODO\nelseif $action = haha\n  #TODO\nendif'},
      'else'    : {detail:'else',documentation:'else extends an if statement to execute a statement in case the expression in the if statement evaluates to FALSE.\n\nexample :\nif $action = demo\n # TODO\nelse\n  # TODO\nendif'},
      'endif'   : {detail:'end if',documentation:'endif use to end if construct\n\nexample : \nif $action = demo\n # TODO\nendif'},
      'loop'    : {detail:'loop',documentation:'the loop construct to run commands for given times.\n\nexample : \nloop $count => $index\n # TODO\nendloop\n\n - count : time to execute commands\n - index : optional, the number of index in the loop.'},
      'endloop' : {detail:'end loop',documentation:'endloop use to end loop construct\n\n example : \nloop $count => $index\n # TODO\nendloop'},
      'include' : {detail:'include file to current script',documentation:'include use to load another testcase into current script as a part of it.\n\nexample : include path/to/script.xy\n\n - path : path of script file'},
      'log'     : {detail:'log message',documentation:'log message to log handler\n\nexample : log my message\n\n - params,... params to log command'},
      'set'     : {detail:'setup a variable',documentation:'set up an variable\n\nexample : set var-name value\n\n - name : name of variable\n - value : value of variable'},

      // functions
      'string.append'    : {detail:'append more chars',documentation:'append more chars to end of string\n\nexample : string.append my-var content\n\n - name : name of variable\n - content : content to append to string'},
      'string.read-file' : {detail:'load file into variable',documentation:'read file and set file content into a variable\n\nexample : string.read-file my-var path/to/file\n\n - name : name of variable\n - path : path of file to read'},
      'string.md5'       : {detail:'md5 the variable content',documentation:'md5 the variable\'s content and set it back to variable.\n\nexample : string.md5 my-var\n\n - name : name of variable'},
      'string.uppercase' : {detail:'upper case an variable',documentation:'uppercase the vaiable and set value back to variable.\n\nexample : string.uppercase my-var\n\n - name : name of variable'},
      'system.sleep'     : {detail:'delay for given seconds',documentation:'wait seconds to run commands\n\nexample : system.sleep 10\n\n - seconds : seconds to wait'},
      
      // const vars
      'variable:date.yyyymmdd'  : {detail:'get date',documentation:'get date of today\n\nexample : 20200202'},
      'variable:date.timestamp' : {detail:'get timestamp',documentation:'get timestamp of now'},
      'variable:string.uuid'    : {detail:'get uuid',documentation:'get a new uuid string'},
      'variable:time.hhiiss'    : {detail:'get time',documentation:'get time of now\n\nexample : 125959'},
      
      // operator : browser
      'operator:poster:method'   : {detail:'set method',documentation:'set method to poster\n\nexample : method POST\n\n - metehod : name of request method'},
      'operator:poster:address'  : {detail:'set address',documentation:'set address to poster\n\nexample : address https://www.example.com\n - address : address to request'},
      'operator:poster:body-add' : {detail:'add body param',documentation:'add a parameter to body\n\nexample : body-add username sige\n\n - name : name of parameter\n - value : value of parameter'},
      'operator:poster:body-get' : {detail:'get body param',documentation:'get parameter from body and set value to variable\n\nexample : body-get username my-var\n\n - name : name of parameter\n - varname : name of variable to store value'},
      'operator:poster:send'     : {detail:'send request',documentation:'send the request\n\nexample : send'},
      'operator:poster:pre-request-handler'   : {detail:'set pre request handler',documentation:'set a user function to poster where would be executed before send\n\nexample : pre-request-handler my-func\n\n - func : name of function'},
      'operator:poster:response-read-as-json' : {detail:'read response json',documentation:'read response into an variable\n\nexample : response-read-as-json\n\n - varname : name of variable'},
      
      'operator:browser:open' : {detail:'open a page',documentation:'open a page in current page\n\nexample : open https://www.example.com\n\n - url : url address to open'},
      'operator:browser:click'    : {detail:'click an elem',documentation:'click an element in the page\n\nexample : click #elem-id\n\n - elem : an elem selector'},
      'operator:browser:dblclick' : {detail:'double click an elem',documentation:'double click an element in the page\n\nexample : dblclick $elem-id\n\n - elem : an elem selector'},
      'operator:browser:input'    : {detail:'input string to an elem',documentation:'input string to an element in the page\n\nexample : input $elem-id string-value\n\n - elem : an elem selector\n - content : content to elem'},
      'operator:browser:upload'   : {detail:'set file to upload',documentation:'select an file an set to upload elem in the page\n\nexample : upload $elem-id path/to/file\n\n - elem : an elem selector\n - file : file path'},
      'operator:browser:hover'    : {detail:'hover an elem',documentation:'move pointer to an elem\n\nexample : hover $elem-id\n\n - elem : an elem selector'},
      'operator:browser:select'   : {detail:'set select value by name',documentation:'update select value by option text\n\nexample : select $elem-id option-text\n\n - elem : an elem selector\n - option : name of option'},
      'operator:browser:blur'     : {detail:'blur an elem',documentation:'trigger blur event for an elem\n\nexample : blur $elem-id\n\n - elem : an elem selector'},
      'operator:browser:alert-accept'   : {detail:'accept an alert',documentation:'click yes button to an alert\n\nexample : alert-accept'},
      'operator:browser:alert-dismiss'  : {detail:'dismiss an alert',documentation:'click cancel buttong to an alert\n\nexampel : alert-dismiss'},
      'operator:browser:alert-input'    : {detail:'input string to alert',documentation:'send string to an alert\n\nexample : alert-input my-string-value\n\n - value : value to alert'},
      'operator:browser:close-tab'      : {detail:'close tab by index',documentation:'close the tab by given index\n\nexample : close-tab 1\n\n - index : index of tab to close'},
      'operator:browser:close-tab-by-url' : {detail:'close tab by url',documentation:'close the tab by given url\n\nexample : close-tab-by-url https://www.example.com\n\n - url : url of tab to close'},
      'operator:browser:close-current-tab' : {detail:'close current tab and switch to first tab',documentation:'close current tab\n\nexample : close-current-tab'},
      'operator:browser:switch-to-frame' : {detail:'focus to iframe',documentation:'focus to an iframe by given selector\n\nexample : switch-to-frame #frame-id\n\n - elem : an elem selector'},
      'operator:browser:switch-to-parent' : {detail:'switch to parent window',documentation:'switch to parent widow\n\nexample : switch-to-parent'},
      'operator:browser:take-screenshot' : {detail:'take a screenshot',documentation:'take a screenshot an save it path\n\nexample : take-screenshot path/to/file\n\n - path : path to save screenshot'},
      'operator:browser:wait-title' : {detail:'wait for title match',documentation:'wait until page title matched\n\nexample : wait-title test-page\n\n - title : content of title'},
      'operator:browser:wait-url' : {detail:'wait for url match',documentation:'wait until current page url matched\n\nexample : wait-url http://www.example.com\n\n - url : the url to match'},
      'operator:browser:wait-elem-exists' : {detail:'wait for elem exists',documentation:'wait until the given eleme exists in page dom\n\nexample : wait-elem-exists $elem-id\n\n - elem : the elem selector'},
      'operator:browser:wait-elem-not-exists' : {detail:'wait for elem not exists',documentation:'wait until the given elem does not exists in the page dome\n\nexample : wait-elem-not-exists\n\n - elem : the elem selector'},
      'operator:browser:wait-elem-visiable' : {detail:'wait for elem visiable',documentation:'wait until the given elem is visiable in page\n\nexample : wait-elem-visiable $elem-id\n\n - elem : the elem selector'},
      'operator:browser:wait-elem-invisible' : {detail:'wait for elem invisible',documentation:'wait until the given elem is invisible in the page\n\nexample : wait-elem-invisible $elem-id\n\n -- elem : the elem selector'},
      'operator:browser:wait-elem-text' : {detail:'wait for elem text match',documentation:'wait until the given elem\'s text matched given content\n\nexample : wait-elem-text $elem-id elem-text-here\n\n - elem : the elem selector\n - text : the text to be matched'},
      'operator:browser:wait-alert-present' : {detail:'wait alert present',documentation:'wait until the alert present in the window\n\nexample : wait-alert-present'},
      'operator:browser:assert-title' : {detail:'assert page title',documentation:'assert that current page title matched given text\n\nexample : assert-title page-title title-not-matched\n\n - title : the title of page\n - message : optional, message on assert failed'},
      'operator:browser:assert-url' : {detail:'assert page url',documentation:'assert that current page url matched given url\n\nexample : assert-url https://www.example.com message-here\n\n - url : the url of page\n - message : optional, message on assertion failed'},
      'operator:browser:assert-elem-exists' : {detail:'assert that elem exists',documentation:'assert that given elem exists in current page\n\nexample : assert-elem-exists $elem-id message-here\n\n - elem : the elem selector\n - message : optional, message on assertion failed'},
      'operator:browser:assert-elem-visiable' : {detail:'assert that elem visiable',documentation:'assert that given elem visible in currrent page\n\nexample : assert-elem-visiable $elem-id message-here\n\n\ - elem : the elem selector\n - message : optional, message on assertion failed'},
      'operator:browser:assert-elem-invisible' : {detail:'assert that elem invisible',documentation:'assert that given elem invisible in current page\n\nexample : assert-elem-invisible $elem-id message-here\n\n - elem : the elem selector\n - message : optional, message on assertion failed'},
      'operator:browser:assert-elem-text' : {detail:'assert that elem text matched given text',documentation:'assert that given elem\'s text matched given text\n\nexample : assert-elem-text $elem-id text-here message-here\n\n - elem : the elem selector\n - text : text to match\n - message : optional, message on assertion failed'},
      'operator:browser:assert-alert-present' : {detail:'assert alert present',documentation:'assert that alert present on current page\n\nexample : assert-alert-present'}
    };

    let key : string = '';
    if ( CompletionItemKind.Keyword === item.kind ) {
      key = item.label;
    } else if ( CompletionItemKind.Method === item.kind ) {
      key = `operator:${item.data.operator}:${item.label}`;
    } else if ( CompletionItemKind.Function === item.kind ) {
      key = item.label;
    } else if ( CompletionItemKind.Property === item.kind ){
      key = `variable:${item.label}`;
    }

    if ( undefined !== rmap[key] ) {
      item.detail = rmap[key].detail;
      item.documentation = rmap[key].documentation;
    }
    return item;
  }
}