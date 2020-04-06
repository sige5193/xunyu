import { DebugProtocol } from 'vscode-debugprotocol';
/**
 * 
 */
export interface XunyuLaunchRequestArguments extends DebugProtocol.LaunchRequestArguments {
  /**
   * 
   */
  program: string;
  /**
   * 
   */
  stopOnEntry?: boolean;
  /**
   * 
   */
  trace?: boolean;
  /**
   * 
   */
  envname?:string;
  /**
   * 
   */
  docroot?:string;
}