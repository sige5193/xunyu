import * as vscode from 'vscode';
import { XunyuDebugSession } from './XunyukDebugSession';
/**
 * 
 */
export class XunyuDebugAdapterFactory implements vscode.DebugAdapterDescriptorFactory {
  /**
   * @param _session 
   */
  createDebugAdapterDescriptor(_session: vscode.DebugSession): vscode.ProviderResult<vscode.DebugAdapterDescriptor> {
    let debugSession = new XunyuDebugSession();
    return new vscode.DebugAdapterInlineImplementation(debugSession);
  }

  /**
   * 
   */
  dispose () {
    // NOTHING TO DO NOW
  }
}