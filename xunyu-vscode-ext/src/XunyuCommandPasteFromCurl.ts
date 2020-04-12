import * as vscode from 'vscode';
export class XunyuCommandPasteFromCurl {
  /**
   * 
   */
  private commandRaw:string = '';

  /**
   * 
   */
  private processingCmd:string = '';

  /**
   * 
   */
  private curl:any = {
    location : false,
    method : 'GET',
    address : null,
    headers : {},
    bodyType : 'x-www-form-urlencoded',
    bodyData : {},
  };

  /**
   * 
   */
  static handle() {
    let handler = new XunyuCommandPasteFromCurl();
    vscode.env.clipboard.readText().then((text)=>{
      handler.parse(text);
      handler.convertToTestCommands();
    });
  }

  /**
   * 
   */
  convertToTestCommands() {
    let commands:string[] = [];
    commands.push('use poster');
    commands.push(`method ${this.curl.method}`);
    commands.push(`address ${this.curl.address}`);
    
    for ( let headerKey in this.curl.headers ) {
      commands.push(`header-add ${headerKey} ${this.curl.headers[headerKey]}`);
    }

    if ( 'x-www-form-urlencoded' === this.curl.bodyType ) {
      for ( let bodyKey in this.curl.bodyData ) {
        commands.push(`body-add ${bodyKey} ${this.curl.bodyData[bodyKey]}`);
      }
    } else if ( 'raw' === this.curl.bodyType ) {
      commands.push(`body-set ${this.curl.bodyData}`);
    } else if ( 'file' === this.curl.bodyType ) {
      commands.push(`body-add-file ${this.curl.bodyData}`);
    }

    commands.push('send');
    commands.push('close');

    let editor = vscode.window.activeTextEditor;
    if ( undefined === editor ) {
      return;
    }

    editor.edit((editBuilder: vscode.TextEditorEdit) => {
      for ( let i=0; i<commands.length; i++ ) {
        let lineCount = vscode.window.activeTextEditor?.document.lineCount ? vscode.window.activeTextEditor?.document.lineCount : 0;
        let pos = new vscode.Position(lineCount+1, 0);
        editBuilder.insert(pos, `${commands[i]}\n`);
      }
    });
  }

  /**
   * 
   */
  parse( text : string ) {
    this.commandRaw = text;
    this.processingCmd = text + ' ';
    
    let part = this.readUntil(' ');
    while ( null !== part ) {
      switch( part ) {
      case 'curl' : case '--compressed' : case '\\' : break;
      case '--location' : this.curl.location = true; break;
      case '--request' : this.curl.method = this.readUntil(' '); break;
      case '-H' : case '--header' : this.addHeader(); break;
      case '--data-urlencode' : this.addUrlencodedBodyItem(); break;
      case '--data-binary' : this.addFile(); break;
      case '--data' : this.setRawBodyData(); break;
      default : this.curl.address = this.strTrimChar(this.strTrimChar(part, "'"), '"'); break;
      }
      part = this.readUntil(' ');
    }
  }

  /**
   * 
   */
  setRawBodyData() {
    this.curl.bodyType = 'raw'; 
    this.curl.bodyData = this.quoteString(this.readQuoted());
  }

  /**
   * 
   */
  addFile() {
    this.curl.bodyType = 'file'; 
    let file = this.readQuoted();
    if ( '@' === file[0] ) {
      file = file.substr(1);
    }
    this.curl.bodyData = this.quoteString(file);
  }
  
  /**
   * 
   */
  quoteString( str:string ) {
    str = str.replace(/ /g, '\\ ');
    return str;
  }

  /**
   * 
   */
  readQuoted() {
    let quoteChar = this.processingCmd[0];
    this.processingCmd = this.processingCmd.substr(1);

    let endpos = 0;
    for ( let i=0; i<this.processingCmd.length; i++ ) {
      endpos ++;
      if ( quoteChar === this.processingCmd[i] ) {
        break;
      }
    }
    let str = this.processingCmd.substr(0, endpos-1);
    this.processingCmd = this.processingCmd.substr(endpos).trim();
    return str;
  }

  /**
   * 
   */
  addUrlencodedBodyItem() {
    let bodyItem = this.readQuoted();

    let pos = bodyItem.indexOf('=');
    let key = bodyItem.substr(0, pos);
    let value = this.quoteString(decodeURI(bodyItem.substr(pos+1)));
    this.curl.bodyData[key] = value;
  }

  /**
   * 
   */
  addHeader( ) {
    let header = this.readQuoted();
    let pos = header.indexOf(': ');
    let key = header.substr(0, pos);
    let value = this.quoteString(header.substr(pos+2));
    this.curl.headers[key] = value;
  }

  /**
   * 
   */
  private strTrimChar( str:any, char:string ) : string {
    if ( char === str.substr(0, char.length) ) {
      str = str.substr(char.length);
    }
    if ( char === str.substr(char.length*-1) ) {
      str = str.substr(0, str.length - char.length);
    }
    return str;
  }

  /**
   * @param chars 
   */
  private readUntil( char : string ) : string|null {
    let pos = this.processingCmd.indexOf(char);
    if ( -1 === pos || ' ' === this.processingCmd ) {
      return null;
    }
    pos ++;

    let part = this.processingCmd.substr(0, pos).trim();
    this.processingCmd = this.processingCmd.substr(pos).trim() + " ";
    return part;
  }
}