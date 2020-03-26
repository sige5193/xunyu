/**
 * 
 */
const vscode = require('vscode');

/**
 * 
 */
let terminal = null;

/**
 * Run all selected tests
 */
function start( fileUri ) {
  if ( null == terminal ) {
    terminal = vscode.window.createTerminal("执行测试脚本");
  }
  terminal.show();
  terminal.sendText(`xunyu "${fileUri.fsPath}"\n`);
}
module.exports = {start};