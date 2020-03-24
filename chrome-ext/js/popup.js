new Vue({
  el: '#app',
  data: {
    isRecording : false,
    commands : [],
    customCommandIndex : null,
    customCommand : '',
  },
  watch : {
    commands : function () {
      localStorage.setItem("EdittingCommands", JSON.stringify(this.commands));
    },
  },
  methods : {
    /**
     * 
     */
    saveCustomCommand : function() {
      if ( 0 == this.customCommand.trim().length ) {
        return false;
      }

      if ( null == this.customCommandIndex ) {
        this.commands.push(this.customCommand);
      } else {
        this.commands[this.customCommandIndex] = this.customCommand;
        this.customCommandIndex = null;
      }
      
      this.customCommand = '';
    },
    /**
     * @param {*} index 
     */
    deleteCommand : function( index ) {
      this.commands.splice(index, 1);
    },
    /**
     * @param {*} index 
     */
    editCommand : function ( index ) {
      this.customCommandIndex = index;
      this.customCommand = this.commands[index];
    },
    /**
     * 
     */
    openCommandEditor : function () {
      window.open('editor.html','命令编辑','height=500,width=500,top=650,left=860,menubar=no,scrollbars=yes,resizable=yes');
      window.close();
    },
    /**
     * 
     */
    doneRecording: function() {
      // 通知后台将所有命令刷新到服务器
      let bg = chrome.extension.getBackgroundPage();
      bg.XYDoneRecording();

      // 关闭所有窗口， 这将导致浏览器退出
      setTimeout(function() {
        chrome.tabs.query({}, function(tabs) {
          let responesHandler = function( response ) { console.log(response); };
          debugger;
          let messageData = {source:'xunyu', action:'DONE-RECORDING'};
          for ( let i=0; i<tabs.length; i++ ) {
            chrome.tabs.sendMessage(tabs[i].id, messageData, responesHandler);
            chrome.tabs.remove(tabs[i].id);
          }
        });
      }, 500);
    }
  },

  /**
   * 
   */
  mounted : function() {
    this.isRecording = localStorage.getItem("IsRecording");
    if ( null == this.isRecording ) {
      this.isRecording = false;
    }

    // 获取编辑中的命令列表
    let edittingCmds = localStorage.getItem("EdittingCommands");
    if ( null != edittingCmds ) {
      this.commands = JSON.parse(edittingCmds);
    }

    let catchedCommand = localStorage.getItem("CachedCommand");
    if ( null != catchedCommand && 0 == this.commands.length ) {
      this.commands.push(catchedCommand);
    }
  },
});