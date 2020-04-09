/**
 * 
 */
import * as vscode from 'vscode';
import { XunyuConfigurationProvider } from './XunyuConfigurationProvider';
import { XunyuDebugAdapterFactory } from './XunyuDebugAdapterFactory';
import {LanguageClient,LanguageClientOptions,ServerOptions,TransportKind} from 'vscode-languageclient';
import { workspace } from 'vscode';
import { XunyuCommandRecorder } from './XunyuCommandRecorder';
const path = require('path');

/**
 * 
 */
let client: LanguageClient;
/**
 * 
 */
let recorder : XunyuCommandRecorder;

/**
 * @param context 
 */
export function activate(context: vscode.ExtensionContext) {
  console.log('extension "xunyu-vscode-ext" is now active!');

  // register a configuration provider for 'xunyu' debug type
  let xcp = new XunyuConfigurationProvider();
  let rdcp = vscode.debug.registerDebugConfigurationProvider('xunyu', xcp);
  context.subscriptions.push(rdcp);

  // register a debug adapter descriptior factory
  let zdaf = new XunyuDebugAdapterFactory();
  let rdadf = vscode.debug.registerDebugAdapterDescriptorFactory('xunyu', zdaf);
  context.subscriptions.push(rdadf);
  context.subscriptions.push(zdaf);

  // register a record command
  recorder = new XunyuCommandRecorder();
  let disposable = vscode.commands.registerCommand('extension.xunyuRecorderStart', function () {
    recorder.start();
  });
  context.subscriptions.push(disposable);

  // start language server
  let serverModule = context.asAbsolutePath(path.join('out','XunyuLanguageServer.js'));
  let serverOptions: ServerOptions = {
    run: { module: serverModule, transport: TransportKind.ipc },
    debug: {module: serverModule,transport: TransportKind.ipc,options:{execArgv:['--nolazy','--inspect=6009']}}
  };
  let clientOptions : LanguageClientOptions = {
    documentSelector: [{ scheme: 'file', language: 'xunyu' }],
    synchronize: {fileEvents: workspace.createFileSystemWatcher('**/.clientrc')}
  };
  client = new LanguageClient('XunyuLanguageServer','Xunyu Language Server',serverOptions,clientOptions);
  client.start();
}

/**
 * 
 */
export function deactivate() {
  if (client) { client.stop(); }
  if (recorder) {recorder.stop();}
  console.log('extension "xunyu-vscode-ext" is now deactivate!');
}
