new Vue({
  el: '#app',
  data: {
    category : 'action',
    name : 'click',
    param1 : null,
    param2 : null,
    param3 : null,
    elemSelectParamName : null,
  },
  watch : {
    category : function() {
      this.name = this.param1 = this.param2 = this.param3 = null;
    },
    name : function() {
      this.param1 = this.param2 = this.param3 = null;
    },
  },
  methods : {
    /**
     * @param {*} param 
     */
    formatParam( param ) {
      param = param.replace(/ /g, '\\ ');
      return param;
    },
    /**
     * 
     */
    addCommand() {
      let command = (null == this.name) ? this.category : this.name;
      if ( null != this.param1 ) {
        command += ` ${this.formatParam(this.param1)}`;
      }
      if ( null != this.param2 ) {
        command += ` ${this.formatParam(this.param2)}`;
      }
      if ( null != this.param3 ) {
        command += ` ${this.formatParam(this.param3)}`;
      }
      chrome.extension.getBackgroundPage().getBackground().pushEditCommand(command);
      window.close();
    },
    /**
     * 
     */
    selectElem( paramName ) {
      this.elemSelectParamName = paramName;
      chrome.tabs.query({}, function(tabs) {
        let responesHandler = function( response ) { console.log(response); };
        let messageData = {source:'xunyu', action:'MODEL-SWITCH', model:'ELEM-SELECT'};
        for ( let i=0; i<tabs.length; i++ ) {
          chrome.tabs.sendMessage(tabs[i].id, messageData, responesHandler);
        }
      });
    },
    /**
     * 
     */
    afterElemSelected( request, sendResponse ) {
      switch ( this.elemSelectParamName ) {
      case 'param1' : this.param1 = request.selector; break;
      case 'param2' : this.param2 = request.selector; break;
      case 'param3' : this.param3 = request.selector; break;  
      }
      this.elemSelectParamName = null;
      sendResponse({success:1});
    }
  },
  /**
   * 
   */
  mounted : function() {
    let $this = this;
    chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
      if ( !('undefined' != request.source && 'xunyu' == request.source) ) {
        return ;
      }
      switch ( request.action ) {
      case 'ELEM-SELECTED' : 
        $this.afterElemSelected(request, sendResponse);
        break;
      }
    });
  },
});