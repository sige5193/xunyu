import * as vscode from 'vscode';
import { WorkspaceFolder } from 'vscode';
/**
 * 
 */
export class XunyuConfigurationProvider implements vscode.DebugConfigurationProvider{
  /**
   * Massage a debug configuration just before a debug session is being launched,
   * e.g. add all missing attributes to the debug configuration.
   */
  resolveDebugConfiguration(
    folder: WorkspaceFolder | undefined, 
    config: vscode.DebugConfiguration, 
    token?: vscode.CancellationToken
  ): vscode.ProviderResult<vscode.DebugConfiguration> {
    if (!config.type && !config.request && !config.name) { // if launch.json is missing or empty
      const editor = vscode.window.activeTextEditor;
      if (editor && editor.document.languageId === 'xunyu') {
        config.type = 'xunyu';
        config.name = 'Launch';
        config.request = 'launch';
        config.program = '${file}';
        config.stopOnEntry = true;
        config.envname = 'env.ini';
        config.docroot = '${workspaceFolder}';
      }
    }

    if (!config.program) {
      return vscode.window.showInformationMessage("Cannot find a program to debug").then(_ => {
        return undefined; // abort launch
      });
    }

    return config;
  }
}