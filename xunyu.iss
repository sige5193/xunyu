; Installer maker for windows
; @author sige

#define MyAppName "Xunyu"
#define MyAppVersion "0.2.1"
#define MyAppPublisher "Sige Chen"
#define MyAppURL "https://github.com/sige-chen/xunyu"

[Setup]
AppId={{896B0378-719C-46D9-926B-2AD9B13CB3B9}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
AppPublisherURL={#MyAppURL}
AppSupportURL={#MyAppURL}
AppUpdatesURL={#MyAppURL}
DefaultDirName=C:\ProgramFiles\xunyu
DefaultGroupName=xunyu
DisableProgramGroupPage=yes
; Uncomment the following line to run in non administrative install mode (install for current user only.)
;PrivilegesRequired=lowest
OutputDir=build\
OutputBaseFilename=xunyu
SetupIconFile=icon.ico
Compression=lzma
SolidCompression=yes
WizardStyle=modern

[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"

[Files]
Source: "build\windows\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs
; NOTE: Don't use "Flags: ignoreversion" on any shared system files

[Registry]
Root: HKLM; \
Subkey: "SYSTEM\CurrentControlSet\Control\Session Manager\Environment"; \
ValueType: expandsz; \
ValueName: "Path"; \
ValueData: "{olddata};{app}"; \
Check: NeedsAddPath('{app}')

[Code]
function NeedsAddPath(Param: string): boolean;
var
  OrigPath: string;
begin
  if not RegQueryStringValue(HKEY_LOCAL_MACHINE,
    'SYSTEM\CurrentControlSet\Control\Session Manager\Environment',
    'Path', OrigPath)
  then begin
    Result := True;
    exit;
  end;
  Result := Pos(';' + ExpandConstant(Param) + ';', ';' + OrigPath + ';') = 0;
end;

var 
  // install vscode ext or not, default to yes
  VsCodeExtInsConfrimPage: TInputOptionWizardPage;
  // select chrome executable path
  ChromeFilePage: TInputFileWizardPage;

procedure InitializeWizard;
begin
  VsCodeExtInsConfrimPage := CreateInputOptionPage(wpWelcome,
    'Vscode extension', 'Would you like to install vscode extension for xunyu?',
    'xunyu vscode extension makes vscode to be a best editor to write and manage testcases.',
    False, False);
  VsCodeExtInsConfrimPage.Add('&install vscode extension');
  VsCodeExtInsConfrimPage.Values[0] := True;
  
  ChromeFilePage := CreateInputFilePage(wpWelcome,
    'Select Chrome Location', 'Where is Chrome located?',
    'Select where Chrome is located, then click Next.');
  ChromeFilePage.Add('&Location of Chrome.exe:', 'Executable files|*.exe|All files|*.*', '.exe');  
end;

procedure CurStepChanged(CurStep: TSetupStep);
var
  VsCodeExtInstallErrorCode: Integer;
  VsCodeCommandCheckErrorCode: Integer;
begin
  if CurStep = ssPostInstall then begin 
    { Save chrome path to conf.ini }
    SetIniString('Browsers', 'ChromePath', '"'+ChromeFilePage.Values[0]+'"', ExpandConstant('{app}/conf.ini'));

    { Install VS Code Extension }
    if VsCodeExtInsConfrimPage.Values[0] = True then begin
      { Check code command }
      if ShellExec('','where','code', '', SW_HIDE, ewWaitUntilTerminated, VsCodeCommandCheckErrorCode) then begin
        if VsCodeCommandCheckErrorCode = 0 then begin
          SaveStringToFile(ExpandConstant('{app}/init.bat'), 'code', False);
          SaveStringToFile(ExpandConstant('{app}/init.bat'), ' --install-extension', True);
          SaveStringToFile(ExpandConstant('{app}/init.bat'), ' sige-chen.xunyu-vscode-ext', True);
          if not Exec(ExpandConstant('{cmd}'), ExpandConstant('/C {app}/init.bat'), '', SW_HIDE, ewWaitUntilTerminated, VsCodeExtInstallErrorCode) then
          begin
            MsgBox('failed to install vscode extension.', mbInformation, MB_OK);
          end;
          DeleteFile(ExpandConstant('/C {app}/init.bat'));
        end else begin
          MsgBox('code is not available to execute. vs code extension is not installed', mbInformation, MB_OK);
        end
      end else begin
        MsgBox('code is not available to execute. vs code extension is not installed', mbInformation, MB_OK);
      end;
    end
  end;
end;