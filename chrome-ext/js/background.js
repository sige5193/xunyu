/**
 * 
 */
class XunyuBackgroundHandler {
  /**
   * 
   */
  constructor() {
    this.server = null;
    this.currentTab = null;
    this.tabs = {};
    this.cachedCommand = null;
    this.editCommands = [];
    this.status = 'initted';

    let $this = this;
    chrome.runtime.onMessage.addListener(function(request,sender,sendResponse) {
      $this.onRuntimeMessage(request,sender,sendResponse);
    });
    chrome.tabs.onCreated.addListener(function( tab ) {
      $this.onTabCreated(tab);
    });
    chrome.tabs.onUpdated.addListener(function( tabId, changeInfo, tab ) {
      $this.onTabUpdated(tabId,changeInfo,tab );
    });
    chrome.tabs.onActivated.addListener(function( activeInfo ) {
      $this.onTabActivated(activeInfo);
    });
    chrome.tabs.onRemoved.addListener(function( tabId, removeInfo ) {
      $this.onTabRemoved(tabId, removeInfo);
    });
  }

  /**
   * @param {*} tab 
   */
  onTabCreated( tab ) {
    this.currentTab = {id:tab.id,hasSend:false};
    this.tabs[`TAB:${tab.id}`] = {url:tab.url};
  }

  /**
   * 
   */
  onTabActivated( activeInfo ) {
    this.currentTab = {id:activeInfo.tabId,hasSend:false};

    let $this = this;
    chrome.tabs.get(activeInfo.tabId, function( tab ) {
      if ( "" == tab.url ) {
        $this.currentTab = {id:activeInfo.tabId,hasSend:false};
        return;
      }

      $this.currentTab = {id:activeInfo.tabId,hasSend:true};
      $this.cacheCommand(`switch-tab ${tab.url}`);
    });
  }

  /**
   * 
   */
  onTabUpdated( tabId, changeInfo, tab ) {
    this.tabs[`TAB:${tabId}`] = {url:tab.url};

    if ( 'complete' == tab.status 
    && tabId == this.currentTab.id 
    && !this.currentTab.hasSend ) {
      this.currentTab.hasSend = true;
      this.cacheCommand(`switch-tab ${tab.url}`);
    }
  }

  /**
   * @param {*} tabId 
   * @param {*} removeInfo 
   */
  onTabRemoved( tabId, removeInfo ) {
    let url = this.tabs[`TAB:${tabId}`].url;
    this.cacheCommand(`close-tab-by-url ${url}`);
    delete this.tabs[`TAB:${tabId}`];
  }

  /**
   * 
   */
  handlerNewCommand( request, sendResponse ) {
    this.cacheCommand(request.command);
    sendResponse({success:true});
  }

  /**
   * @param {string|array} commands 
   */
  sendCommands( commands ) {
    if ( 'string' == typeof(commands) ) {
      commands = [commands];
    }
    
    if ( 0 == commands.length 
    || null == this.server 
    || this.server.readyState != this.server.OPEN 
    || 'recording' != this.status
    ) {
      console.log('ignore commands : '+commands.join('; '));
      return;
    }

    this.server.send(JSON.stringify({action:'NEW-COMMANDS',commands:commands}));
    console.log('send commands : '+commands.join('; '));
  }

  /**
   * 
   */
  onRuntimeMessage(request, sender, sendResponse) {
    if ( !('undefined' != request.source && 'xunyu' == request.source) ) {
      return ;
    }
    
    switch ( request.action ) {
    case 'NEW-COMMAND' : this.handlerNewCommand(request, sendResponse); break;
    }
  }

  /**
   * 
   */
  serverConnect(callback) {
    let $this = this;
    let port = this.getConfig('RecordServerPort', 60000);
    this.server = new WebSocket(`ws://127.0.0.1:${port}`);
    this.server.onerror = this.onServerError;
    this.server.onclose = this.onServerClose;
    this.server.onopen = function() {
      $this.onServerOpen(callback);
    };
  }

  /**
   * 
   */
  onServerOpen(callback) {
    console.log('==> server opened');
    chrome.browserAction.setBadgeText({text:'rec..'});
    this.status = 'recording';
    this.cacheCommand('use browser chrome');
    setTimeout(function() {
      callback();
    }, 200);
  }
  
  /**
   * @param {*} message 
   */
  onServerMessage ( e ) {
    console.log(e.data);
  }

  /**
   * 
   */
  onServerClose() {
    chrome.browserAction.setBadgeText({text:'disco'});
    alert('record server disconnected.');
  }

  /**
   * 
   */
  onServerError( error ) {
    chrome.browserAction.setBadgeText({text:'error'});
    alert("failed to connect to server.");
  }

  /**
   * @param {*} name 
   * @param {*} value 
   */
  getConfig(name, value = null) {
    return value;
  }

  /**
   * 结束录制
   */
  close() {
    let commands = this.editCommands;
    if ( 0 == commands.length  && null != this.cachedCommand) {
      commands.push(this.cachedCommand);
    }
    this.sendCommands(commands);

    this.server.send(JSON.stringify({action:'STOP-RECORDING'}));
    setTimeout(function() {
      this.server.close();
    }, 200);
    chrome.browserAction.setBadgeText({text:'stop'});
    this.status = 'closed';
  }

  /**
   * 
   */
  start(callback) {
    chrome.browserAction.setBadgeText({text: 'con..'});
    chrome.browserAction.setBadgeBackgroundColor({color: [255, 0, 0, 255]});

    background.serverConnect(callback);
  }

  /**
   * @param {*} command 
   */
  cacheCommand( command ) {
    // flush old commands
    let commands = this.editCommands;
    if ( 0 == commands.length  && null != this.cachedCommand) {
      commands.push(this.cachedCommand);
    }
    this.sendCommands(commands);

    // cache new commands
    this.cachedCommand = command;
    this.editCommands = [];
    chrome.browserAction.setBadgeText({text:''});

    setTimeout(function() {
      chrome.browserAction.setBadgeText({text: command.substr(0,4)});
      chrome.browserAction.setBadgeBackgroundColor({color: [255, 0, 0, 255]});
    }, 200);
  }

  /**
   * - initted
   * - recording
   * - closed
   */
  getStatus() {
    return this.status;
  }

  /**
   * @returns {string}
   */
  getCachedCommand() {
    return this.cachedCommand;
  }

  /**
   * @returns {string[]}
   */
  getEditCommands() {
    return this.editCommands;
  }

  /**
   * @param {*} commands 
   */
  setEditCommands( commands ) {
    if ( this.status == 'recording' ) {
      this.editCommands = commands;
    }
  }

  /**
   * @param {*} command 
   */
  pushEditCommand(command) {
    if ( this.status == 'recording' ) {
      this.editCommands.push(command);
    }
  }
}

/**
 * 
 */
let background = new XunyuBackgroundHandler();

/**
 * 
 */
function getBackground() {
  return background;
}