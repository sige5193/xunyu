/**
 * 
 */
const vscode = require('vscode');

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
 * 将命令写入当前文件
 */
function writeCommandsToEditor( commands ) {
  vscode.window.activeTextEditor.edit( editBuilder => {
    for ( let i=0; i<commands.length; i++ ) {
      let pos = new vscode.Position(vscode.window.activeTextEditor.document.lineCount+1, 0);
      editBuilder.insert(pos, `${commands[i]}\n`);
    }
  });
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