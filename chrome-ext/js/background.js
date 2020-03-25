/**
 * 标签列表，用于生成关闭tab的命令
 */
let tabs = {
   /*'id' : {
      'url' : 'tab的url连接',
      'title' : 'tab标题',
   }*/
};

let commandReceiver = new WebSocket('ws://127.0.0.1:60000/');
commandReceiver.onclose = function() { console.log("命令接收器已断开"); }
commandReceiver.onmessage = function(e) {console.log(e.data);}

/**
 * 
 */
commandReceiver.onopen = function() { 
   chrome.browserAction.setBadgeText({text:'rec..'});
}

/**
 * 
 */
commandReceiver.onerror = function( error ) {
   chrome.browserAction.setBadgeText({text:'error'});
   alert("命令接收器连接失败");
};

/**
 * 将命令发送到接收者
 * @param {string|string[]} commands
 */
function XYSendCommandsToReceiver( commands ) {
   if ( 'string' == typeof(commands) ) {
      commands = [commands];
   }
   console.log(`send : ${JSON.stringify(commands)}`);
   if ( commandReceiver.readyState != commandReceiver.OPEN ) {
      return;
   }
   commandReceiver.send(JSON.stringify({action:'NEW-COMMANDS',commands:commands}));
}

/**
 * 刷新缓存的命令到命令接收器
 */
function XYFlushCommandToReceiver() {
   let edittingCommands = localStorage.getItem("EdittingCommands");
   if ( null == edittingCommands || "" == edittingCommands ) {
      edittingCommands = "[]";
   }
   if ( null != edittingCommands ) {
      edittingCommands = JSON.parse(edittingCommands);
   } 
   
   if ( 0 == edittingCommands.length ) {
      let catchedCommand = localStorage.getItem("CachedCommand");
      if ( null != catchedCommand ) {
         edittingCommands.push(catchedCommand);
      }
   }
   
   // 把命令通过websocket发送给接收端
   if (0 < edittingCommands.length) {
      XYSendCommandsToReceiver(edittingCommands);
   }
}

/**
 * 
 */
function XYMessageHandlerNewCommand( request, sendResponse ) {
   if ( commandReceiver.readyState != commandReceiver.OPEN ) {
      return;
   }

   XYFlushCommandToReceiver();

   localStorage.setItem("CachedCommand", request.command);
   localStorage.setItem("EdittingCommands", JSON.stringify([]));

   chrome.browserAction.setBadgeText({text:''});
   setTimeout(function() {
      chrome.browserAction.setBadgeText({text: request.command.substr(0,4)});
      chrome.browserAction.setBadgeBackgroundColor({color: [255, 0, 0, 255]});
   }, 200);

   sendResponse({success:true});
}

/**
 * 结束录制
 */
function XYDoneRecording() {
   XYFlushCommandToReceiver();
   commandReceiver.send(JSON.stringify({action:'STOP-RECORDING'}));
   setTimeout(function() {
      commandReceiver.close();
   }, 200);
   chrome.browserAction.setBadgeText({text:'stop'});
}

/**
 * 获取一个Tab的唯一属性
 */
function XYTabGetUniqueAttribute( tabId ) {
   if ( 'undefined' == typeof(tabs[`tab-${tabId}`]) ) {
      return null;
   }
   let tab = tabs[`tab-${tabId}`];
   let titleCount = 0;
   for ( let tabKey in tabs ) {
      if ( tab.title == tabs[tabKey].title ) {
         titleCount ++;
      }
   }
   if ( 1==titleCount && 0 < tab.title.trim().length) {
      return {attr:'title', title:tab.title};
   } else {
      return {attr:'url', url:tab.url};
   }
}

/**
 * 监听来自content的消息
 */
chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
  if ( !('undefined' != request.source && 'xunyu' == request.source) ) {
    return ;
  }
  
  switch ( request.action ) {
  case 'NEW-COMMAND' : 
    XYMessageHandlerNewCommand(request, sendResponse);
    break;
  }
});

/**
 * 监听新tab
 */
chrome.tabs.onCreated.addListener(function( tab ) {
   tabs[`tab-${tab.id}`] = {
      url : tab.url,
      title : tab.title
   };
});
/**
 * 监听tab更新
 */
chrome.tabs.onUpdated.addListener(function( tabId, changeInfo, tab ) {
   tabs[`tab-${tabId}`] = {
      url : tab.url,
      title : tab.title
   };
});
/**
 * 监听tab关闭
 */
chrome.tabs.onRemoved.addListener(function( tabId, removeInfo ) {
   let tabInfo = XYTabGetUniqueAttribute(tabId);
   if ( null == tabInfo ) {
      return;
   }

   let closeCmd = null;
   if ( 'title' == tabInfo.attr ) {
      closeCmd = `close-tab-by-title "${tabInfo.title}"`;
   } else {
      closeCmd = `close-tab-by-url "${tabInfo.url}"`;
   }
   XYSendCommandsToReceiver(closeCmd);
   delete tabs[`tab-${tabId}`];
});
/**
 * 监听tab激活事件
 */
chrome.tabs.onActivated.addListener(function( activeInfo ) {
   let tabInfo = XYTabGetUniqueAttribute(activeInfo.tabId);
   if ( null == tabInfo ) {
      return;
   }

   let switchCmd = null;
   if ( 'title' == tabInfo.attr ) {
      switchCmd = `switch-tab-by-title "${tabInfo.title}"`;
   } else {
      switchCmd = `switch-tab-by-url "${tabInfo.url}"`;
   }
   XYSendCommandsToReceiver(switchCmd);
});

localStorage.removeItem("CachedCommand");
localStorage.removeItem("EdittingCommands");
chrome.browserAction.setBadgeText({text: 'con..'});
chrome.browserAction.setBadgeBackgroundColor({color: [255, 0, 0, 255]});