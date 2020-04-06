/**
 * 
 */
import * as vscode from 'vscode';
import { XunyuConfigurationProvider } from './XunyuConfigurationProvider';
import { XunyuDebugAdapterFactory } from './XunyuDebugAdapterFactory';

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
}

/**
 * 
 */
export function deactivate() {
  console.log('extension "xunyu-vscode-ext" is now deactivate!');
}
