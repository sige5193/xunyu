import * as vscode from 'vscode';
const fs = require('fs');
const path = require('path');
/**
 * @link https://github.com/sitegui/nodejs-websocket#readme
 */
export class XunyuCommandRecorder {
  /**
   * 
   */
  private server:any = null;

  /**
   * 
   */
  private hasClosed:boolean = false;

  /**
   * start recorder
   */
  public start() : void {
    let $this = this;
    const recordServerPort = vscode.workspace.getConfiguration().get('recordServerPort');
    let ws = require('nodejs-websocket');
    this.server = ws.createServer(function(connection:any){
      connection.on('text', function(str:string) {
        $this.onMessage(JSON.parse(str));
        connection.sendText(JSON.stringify({success:true}));
      });
      connection.on("close", function (code:any, reason:any) {
        $this.writeCommandsToEditor(['close']);
        $this.stop();
      });
    }).listen(recordServerPort);

    console.log('start record client');
    const chromePath = `"${vscode.workspace.getConfiguration().get('chromePath')}"`;
    const chromeExtPath = `"${vscode.workspace.getConfiguration().get('chromeExtPath')}"`;
    const chromeExtId = vscode.workspace.getConfiguration().get('chromeExtId');
    let clientCmd = `${chromePath} --load-extension=${chromeExtPath} chrome-extension://${chromeExtId}/newtab.html`;
    const childProcess = require("child_process");
    const chrome = childProcess.exec(clientCmd);
    chrome.on('exit', (code:any) => {
      $this.stop();
      console.log(`Child exited with code ${code}`);
    });
    console.log('record client start succefully');
    vscode.window.setStatusBarMessage('> Recording ...');
  }

  /**
   * handle message
   * @param {*} message 
   */
  public onMessage( message : any ) {
    switch ( message.action ) {
    case 'NEW-COMMANDS' : this.writeCommandsToEditor(message.commands); break;
    case 'STOP-RECORDING' : this.stop(); break;
    }
  }

  /**
   * write command to editor
   */
  public writeCommandsToEditor( commands : any[] ) {
    // close recorder if editor has no active text editor
    if ( undefined === vscode.window.activeTextEditor ) {
      this.stop();
      return;
    }

    let editor = vscode.window.activeTextEditor;
    editor.edit((editBuilder: vscode.TextEditorEdit) => {
      for ( let i=0; i<commands.length; i++ ) {
        if ( '-' === commands[i][0] ) {
          this.handleXCommand(editBuilder, commands[i]);
        } else {
          let lineCount = vscode.window.activeTextEditor?.document.lineCount ? vscode.window.activeTextEditor?.document.lineCount : 0;
          let pos = new vscode.Position(lineCount+1, 0);
          editBuilder.insert(pos, `${commands[i]}\n`);
          console.log(`${lineCount+1} : ${commands[i]}`);
        }
      }
    });
  }

  /**
   * handle complex command
   * @param {string} command 
   */
  public handleXCommand( editBuilder : vscode.TextEditorEdit, command : any ) {
    command = command.substr(1);
    command = JSON.parse(command);
    switch ( command.action ) {
    case 'upload' : 
      let file = vscode.window.activeTextEditor?.document.uri.fsPath;
      let folder = path.dirname(file);
      
      let resourcefolder = fs.mkdirSync(`${folder}/resource`);
      if ( undefined === resourcefolder ) {
        vscode.window.showErrorMessage(`资源目录创建失败："${folder}/resource"`);
        return false;
      }

      let fileContent = command.content.substr(command.content.indexOf('base64,')+7);
      let fileBitmap = new Buffer(fileContent, 'base64');
      fs.writeFileSync(`${folder}/resource/${command.filename}`, fileBitmap);

      let lineCount = vscode.window.activeTextEditor?.document.lineCount ? vscode.window.activeTextEditor?.document.lineCount : 0;
      let pos = new vscode.Position(lineCount+1, 0);
      editBuilder.insert(pos, `upload ${command.elem} "resource/${command.filename}"\n`);
      break;
    }
  }

  /**
   * 
   */
  public stop() : void {
    if ( this.hasClosed ) {
      return;
    }
    this.hasClosed = true;
    this.server.close();
    vscode.window.setStatusBarMessage('');
    console.log('stop recording');
  }
}