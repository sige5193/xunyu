/**
 * 
 */
const vscode = require('vscode');
const path = require('path');
const fs = require('fs');
/**
 * 
 */
let recordServer = null;

/**
 * @param {*} message 
 */
function log( message ) {
  console.log(`[XunYu] : ${message}`);
}

/**
 * 处理复杂的命令
 * @param {string} command 
 */
function handleXCommand( command ) {
  command = command.substr(1);
  command = JSON.parse(command);
  switch ( command.action ) {
  case 'upload' : 
    let file = vscode.window.activeTextEditor.document.uri._fsPath;
    let folder = path.dirname(file);
    
    fs.mkdir(`${folder}/resource`, function( err ) {
      if ( null != err && 'EEXIST' != err.code ) {
        vscode.window.showErrorMessage(`资源目录创建失败："${folder}/resource"`);
        return false;
      }
      let fileContent = command.content.substr(command.content.indexOf('base64,')+7);
      let fileBitmap = new Buffer(fileContent, 'base64');
      fs.writeFileSync(`${folder}/resource/${command.filename}`, fileBitmap);

      vscode.window.activeTextEditor.edit( editBuilder => {
        let pos = new vscode.Position(vscode.window.activeTextEditor.document.lineCount+1, 0);
        editBuilder.insert(pos, `upload ${command.elem} "resource/${command.filename}"\n`);
      });
    });
    break;
  }
}

/**
 * 将命令写入当前文件
 */
function writeCommandsToEditor( commands ) {
  for ( let i=0; i<commands.length; i++ ) {
    if ( '-' == commands[i][0] ) {
      handleXCommand(commands[i]);
    } else {
      vscode.window.activeTextEditor.edit( editBuilder => {
        let pos = new vscode.Position(vscode.window.activeTextEditor.document.lineCount+1, 0);
        editBuilder.insert(pos, `${commands[i]}\n`);
      });
    }
  }
}

/**
 * 处理来自客户端的消息
 * @param {*} message 
 */
function handleClientMessage( message ) {
  switch ( message.action ) {
  case 'NEW-COMMANDS' : 
    writeCommandsToEditor(message.commands);
    break;
  case 'STOP-RECORDING' :
    recordServer.close();
    vscode.window.setStatusBarMessage('');
    log("停止记录服务器");
    break;
  }
}

/**
 * 启动录制服务器
 */
function start() {
  log("启动记录服务器");
  let ws = require('nodejs-websocket');
  recordServer = ws.createServer(function(connection){
    connection.on('text', function(str) {
      handleClientMessage(JSON.parse(str));
      connection.sendText(JSON.stringify({success:true}));
    });
  }).listen(60000);

  log("启动记录客户端");
  const chromePath = vscode.workspace.getConfiguration().get('xunyu.chromePath');
  const chromeExtPath = vscode.workspace.getConfiguration().get('xunyu.chromeExtPath');
  const chromeExtId = vscode.workspace.getConfiguration().get('xunyu.chromeExtId');
  let clientCmd = `${chromePath} --load-extension=${chromeExtPath} chrome-extension://${chromeExtId}/newtab.html`;

  const childProcess = require("child_process");
  childProcess.exec(clientCmd);
  log("启动记录客户端：成功");
  vscode.window.setStatusBarMessage('> 测试脚本录制中');
  return;
}

module.exports = {start};