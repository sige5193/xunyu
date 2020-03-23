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
      edittingCommands.push(catchedCommand);
   }
   
   // 把命令通过websocket发送给接收端
   commandReceiver.send(JSON.stringify({action:'NEW-COMMANDS',commands:edittingCommands}));
}

/**
 * 
 */
function XYMessageHandlerNewCommand( request, sendResponse ) {
   if ( commandReceiver.readyState != commandReceiver.OPEN ) {
      console.log("命令接收器未连接，忽略处理");
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
   commandReceiver.close();
   chrome.browserAction.setBadgeText({text:'stop'});
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

localStorage.removeItem("CachedCommand");
localStorage.removeItem("EdittingCommands");
chrome.browserAction.setBadgeText({text: 'con..'});
chrome.browserAction.setBadgeBackgroundColor({color: [255, 0, 0, 255]});