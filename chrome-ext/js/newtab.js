document.getElementById("btn-start").onclick = function() {
  let url = document.getElementById("txt-address").value.trim();
  if ( 0 == url.length ) {
    alert("请输入目标地址");
    return false;
  }

  localStorage.setItem("IsRecording", "YES");
  localStorage.setItem("CachedCommand", `open ${url}`);
  window.location.href = url;
};