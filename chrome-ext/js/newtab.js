document.getElementById("btn-start").onclick = function() {
  let url = document.getElementById("txt-address").value.trim();
  if ( 0 == url.length ) {
    alert("please input target address");
    return false;
  }

  let background = chrome.extension.getBackgroundPage().getBackground();
  background.start(function() {
    background.cacheCommand(`open ${url}`);
    setTimeout(function() {
      window.location.href = url;
    }, 300);
  });
};