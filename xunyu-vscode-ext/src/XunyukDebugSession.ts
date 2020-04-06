import {LoggingDebugSession, InitializedEvent, TerminatedEvent, OutputEvent} from 'vscode-debugadapter';
import { DebugProtocol } from 'vscode-debugprotocol';
import { XunyuLaunchRequestArguments } from './XunyuLaunchRequestArguments';
const { Subject } = require('await-notify');
const child_process = require('child_process');
const path = require('path');
const fs = require('fs');

/**
 * 
 */
export class XunyuDebugSession extends LoggingDebugSession{
  /**
   * 
   */
  private _configurationDone = new Subject();

  /**
   * 
   */
  private _xunyuExe : any;

  /**
   * 
   */
  private _docRoot : string|null|undefined = null;

  /**
   * 
   */
  public constructor () {
    super("xunyu-debug.log");
  }

  /**
   * The 'initialize' request is the first request called by the frontend
   * to interrogate the features the debug adapter provides.
   */
  protected initializeRequest(
    response: DebugProtocol.InitializeResponse, 
    args: DebugProtocol.InitializeRequestArguments
  ): void {
    response.body = response.body || {};
    response.body.supportsConfigurationDoneRequest = true;
    response.body.supportsTerminateRequest = true;
    
    this.sendResponse(response);
    this.sendEvent(new InitializedEvent());
  }

  /**
   * Called at the end of the configuration sequence.
   * Indicates that all breakpoints etc. have been sent to the DA and that the 'launch' can start.
   */
  protected configurationDoneRequest(response: DebugProtocol.ConfigurationDoneResponse, args: DebugProtocol.ConfigurationDoneArguments): void {
    super.configurationDoneRequest(response, args);
    this._configurationDone.notify();
  }
  
  /**
   * handle 'launch' request
   */
  protected async launchRequest(response: DebugProtocol.LaunchResponse, args: XunyuLaunchRequestArguments) {
    await this._configurationDone.wait(1000);

    let testPath = args.program;
    if ( 'xunyu.json' === path.basename(testPath) ) {
      testPath = path.dirname(testPath);
    }

    this._docRoot = args.docroot;
    
    let cmdParams = [];
    cmdParams.push('/c');
    cmdParams.push('xunyu.bat');
    cmdParams.push(testPath);
    cmdParams.push('--env');
    cmdParams.push(args.envname);
    cmdParams.push('--doc-root');
    cmdParams.push(this._docRoot);

    let $this = this;
    const xunyuExec = child_process.spawn('cmd.exe', cmdParams);
    xunyuExec.stdout.on('data', (data:Buffer) => {
      $this.sendEvent(new OutputEvent(data.toString()));
    });
    xunyuExec.stderr.on('data', (data:Buffer) => {
      $this.sendEvent(new OutputEvent(data.toString()));
    });
    xunyuExec.on('exit', (code:any) => {
      $this.sendEvent(new TerminatedEvent());
    });
    this._xunyuExe = xunyuExec;

    this.sendResponse(response);
  }

  /**
   * handle 'terminate' request
   * @param response 
   */
  protected async terminateRequest ( response: DebugProtocol.TerminateResponse, args: DebugProtocol.TerminateArguments ) {
    this.sendEvent(new OutputEvent("<<<< stopping debugger ...\n"));
    let path = `${this._docRoot}/xunyu-exit.signal`;
    try {
      fs.appendFileSync(path, 'exit');
    } catch (err) {
      throw new Error(`failed to exit xunyu runtime : ${err}`);
    }

    this.sendResponse(response);
  }
}