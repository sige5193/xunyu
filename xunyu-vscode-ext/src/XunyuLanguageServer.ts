import {createConnection,ProposedFeatures,InitializeParams,InitializeResult} from 'vscode-languageserver';
import {XunyuLanguageServerCompletionItems} from './XunyuLanguageServerCompletionItems';
/**
 * 
 */
let connection = createConnection(ProposedFeatures.all);
connection.onCompletion(XunyuLanguageServerCompletionItems.getCompletionItems);
connection.onCompletionResolve(XunyuLanguageServerCompletionItems.completionItemResolve);
connection.onInitialize((params: InitializeParams) => {
  const result: InitializeResult = {
    capabilities: {
      completionProvider: {
        resolveProvider: true,
        triggerCharacters : "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$".split('')
      }
    }
  };
  return result;
});
connection.onInitialized(() => {});
connection.listen();
