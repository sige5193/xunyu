/**
 * 事件处理模式
 * RECORD : 记录行为
 * ELEM-SELECT : 元素选择
 */
let XYEventHandlerMode = 'RECORD';

/**
 * 解析元素的选择器
 * @param {*} elem 
 * @returns string
 */
function XYgetElemSelector( elem ) {
  // 使用ID作为选择符
  if ( '' != elem.id ) {
    return `#${elem.id}`;
  }

  // 使用唯一class作为选择符
  if ( '' != elem.className ) {
    for ( let i=0; i<elem.classList.length; i++ ) {
      let className = elem.classList[i];
      if ( 1 == document.getElementsByClassName(className).length ) {
        return `.${className}`;
      }
    }
    if ( 1 == document.getElementsByClassName(elem.className).length ) {
      return `.${elem.className}`;
    }
  }
  
  // 使用 link text 作为选择符
  if ( 'a' == elem.tagName.toLowerCase() ) {
    let linkText = elem.innerText.trim();
    let links = document.querySelectorAll("a");
    let linkCount = 0;
    for ( let i=0; i<links.length; i++ ) {
      if ( links[i].innerText.trim() == linkText ) {
        linkCount ++;
      }
    }
    if ( 1 == linkCount ) {
      return `>${linkText}`;
    }
  }

  // 使用 name 作为选择符
  if ( 'undefined' != typeof(elem.name) ) {
    if ( 1 == document.querySelectorAll(`[name="${elem.name}"]`).length ) {
      return `@${elem.name}`;
    }
  }
  
  // 使用 tag 作为选择符 
  if ( 1 == document.getElementsByTagName(elem.tagName).length ) {
    return elem.tagName.toLowerCase();
  }

  // 使用full xpath作为选择符 
  let xpathGenerator = function( xpathelem ) {
    if ( 'html' == xpathelem.tagName.toLowerCase() ) {
      return ['/html'];
    }

    let path = xpathGenerator(xpathelem.parentNode);
    
    //find index
    let elemIndex = 1;
    let parentChildren = xpathelem.parentNode.children;
    for ( let i=0; i<parentChildren.length; i++ ) {
      if ( parentChildren[i] == xpathelem ) {
        break;
      }
      if ( parentChildren[i].tagName == xpathelem.tagName ) {
        elemIndex ++;
      }
    }
    
    let tagCount = 0;
    for ( let i=0; i<parentChildren.length; i++ ) {
      if ( parentChildren[i].tagName == xpathelem.tagName ) {
        tagCount ++;
      }
    }

    if ( 1 == elemIndex && 1 == tagCount ) {
      path.push(xpathelem.tagName.toLowerCase());
    } else {
      path.push(`${xpathelem.tagName.toLowerCase()}[${elemIndex}]`);
    }
    
    return path;
  };
  let xpathPart = xpathGenerator(elem);
  return xpathPart.join('/');
}

/**
 * 将选中的元素发送给编辑器
 * @param {*} elem 
 */
function XYElemSelected( elem ) {
  let selector = XYgetElemSelector(elem);
  chrome.runtime.sendMessage({
    source : 'xunyu',
    action : 'ELEM-SELECTED',
    selector : selector,
  }, function(response) {
    if ( response.success ) {
      XYEventHandlerMode = 'RECORD';
      console.log("元素选取成功, 切换为录制模式");
    } else {
      alert(`荀彧选取元素失败：${response.message}`);
    }
  });
}

/**
 * 发送命令到 background
 * @param {*} cmd 
 */
function XYCommandSend( cmd ) {
  chrome.runtime.sendMessage({
    source : 'xunyu',
    action : 'NEW-COMMAND',
    command : cmd,
  }, function(response) {
    if ( response.success ) {
      console.log(`录制命令：${cmd}`);
    } else {
      alert(`录制命令失败：${response.message}`);
    }
  });
}

 /**
   * @param {*} param 
   */
function XYFormatParam( param ) {
  param = param.replace(/"/g, '\\"');
  if ( false != param.indexOf(' ') ) {
    param = `"${param}"`;
  }
  return param;
}

/**
 * 记录点击事件
 */
function XYRecordActionClick ( event ) {
  if ( 'input' == event.target.tagName.toLowerCase() && 'file' == event.target.type ) {
    // input file 不再触发click事件的记录
    return;
  }
  XYCommandSend(`click ${XYgetElemSelector(event.target)}`);
}

/**
 * 记录输入字符串事件
 * @param {*} event 
 */
function XYRecordActionInput ( event ) {
  XYCommandSend(`input ${XYgetElemSelector(event.target)} ${XYFormatParam(event.target.value)}`);
}

/**
 * 记录文件选择
 */
function XYRecordActionInputFile( event ) {
  const reader = new FileReader()
  reader.readAsDataURL(event.target.files[0]);
  reader.onload = ()=> {
    XYCommandSend('-'+JSON.stringify({
      'action' : 'upload',
      'elem' : XYgetElemSelector(event.target),
      'filename' : event.target.files[0].name,
      'content' : reader.result,
    }));
  }
}

/**
 * 记录下拉选择事件
 * @param {*} event 
 */
function XYRecordActionSelect ( event ) {
  XYCommandSend(`select ${XYgetElemSelector(event.target)} ${XYFormatParam(event.target.value)}`);
}

/**
 * 点击事件触发后， 需要根据点击的对象来做一些额外的事情
 * @param {*} event 
 */
function XYAfterClicked ( event ) {
  switch ( event.target.tagName.toLowerCase() ) {
  case 'input' :
    switch ( event.target.type ) {
    case 'password' :
    case 'text' : event.target.addEventListener('change',XYRecordActionInput, true); break;
    case 'file' : event.target.addEventListener('change',XYRecordActionInputFile, true); break;
    }
    break;
  case 'select' :
    event.target.addEventListener('change',XYRecordActionSelect, true); 
    break;
  }
}

/**
 * 监听来自各地的消息
 */
chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
  if ( 'undefined' == typeof(request.source) ) {
    return;
  }
  switch ( request.action ) {
  case 'MODEL-SWITCH' : 
    console.log("模式切换 : 元素选择");
    XYEventHandlerMode = request.model;
    break;
  case 'DONE-RECORDING' :
    console.log("录制完成");
    XYEventHandlerMode = 'STOP';
    break;
  }
  sendResponse({success:true});
});

/**
 * 响应所有的点击事件
 */
window.addEventListener('click', function( event ) {
  if ( 'RECORD' == XYEventHandlerMode ) {
    XYRecordActionClick(event);
    XYAfterClicked(event);
  } else if ( 'ELEM-SELECT' == XYEventHandlerMode ) {
    XYElemSelected(event.target);
  } else if ( 'STOP' == XYEventHandlerMode ) {
    return;
  }
}, true);