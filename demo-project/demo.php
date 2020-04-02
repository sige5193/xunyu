<html>
<head>
<title>测试标题</title>
</head>
<body>
  <table border="1">
    <caption>断言测试</caption>
    <thead>
      <tr>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>元素存在</td>
        <td><div id="elem-exists">元素存在</div></td>
      </tr>
      <tr>
        <td>元素可见</td>
        <td><div id="elem-visiable" stle="display:block;">元素可见</div></td>
      </tr>
      <tr>
        <td>元素不可见</td>
        <td><div id="elem-invisible" stle="display:none;">元素不可见</div></td>
      </tr>
      <tr>
        <td>元素文本</td>
        <td><div id="elem-text">断言测试</div></td>
      </tr>
      <tr>
        <td>alert断言</td>
        <td><button id="assert-alert-test">弹框断言</button></td>
      </tr>
    </tbody>
  </table>
  
  <table border="1">
    <caption>等待验证</caption>
    <thead>
      <tr>
        <th>等待事件</th>
        <th>结果</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><button id="wait-start-tfm">标题全匹配</button></td>
        <td><input id="title-full-match" type="text"></td>
      </tr>
      <tr>
        <td><button id="wait-start-trm">标题正则匹配</button></td>
        <td><input id="title-reg-match" type="text"></td>
      </tr>
      <tr>
        <td><button id="wait-start-ufm">连接全匹配</button></td>
        <td><input id="url-full-match" type="text"></td>
      </tr>
      <tr>
        <td><button id="wait-start-urm">连接正则匹配</button></td>
        <td><input id="url-reg-match" type="text"></td>
      </tr>
      <tr>
        <td>
          <button id="wait-start-ep">元素出现</button>
          <div id="wait-start-ep-container"></div>
        </td>
        <td><input id="elem-present" type="text"></td>
      </tr>
      <tr>
        <td>
          <button id="wait-start-ev">元素可见</button>
          <div id="elem-visiable-test" style="display:none;">X</div>
        </td>
        <td>
          <input id="elem-visiable" type="text">
        </td>
      </tr>
      <tr>
        <td>
          <button id="wait-start-ei">元素不可见</button>
          <div id="elem-invisiable-test" style="display:block;">X</div>
        </td>
        <td>
          <input id="elem-invisiable" type="text">
        </td>
      </tr>
      <tr>
        <td>
          <button id="wait-start-etfm">元素文本全匹配</button>
          <div id="elem-txt-full-match"></div>
        </td>
        <td>
          <input id="elem-text-full-match" type="text">
        </td>
      </tr>
      <tr>
        <td>
          <button id="wait-start-etrm">元素文本正则匹配</button>
          <div id="elem-txt-reg-match"></div>
        </td>
        <td>
          <input id="elem-text-reg-match" type="text">
        </td>
      </tr>
      <tr>
        <td>
          <button id="wait-start-ap">弹窗</button>
        </td>
        <td>
          <input id="alert-present" type="text">
        </td>
      </tr>
    </tbody>
  </table>

  <table border="1">
    <caption>操作验证</caption>
    <thead>
      <tr>
        <th>目的</th>
        <th>操作</th>
        <th>结果</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>点击事件</td>
        <td><button id="btn-click-test">点击事件</button></td>
        <td><span id="btn-click-test-result"></span></td>
      </tr>
      <tr>
        <td>双击事件</td>
        <td><button id="btn-dblclick-test">双击事件</button></td>
        <td><span id="btn-dblclick-test-result"></span></td>
      </tr>
      <tr>
        <td>输入事件</td>
        <td><input id="txt-input-test" type="text"></td>
        <td><span id="txt-input-test-result"></span></td>
      </tr>
      <tr>
        <td>上传事件</td>
        <td><input id="file-upload-test" type="file"></td>
        <td><span id="file-upload-test-result"></span></td>
      </tr>
      <tr>
        <td>停留事件</td>
        <td><div id="blk-hover-test" style="width: 100%;height: 20px;background-color:red;"></div></td>
        <td><span id="blk-hover-test-result"></span></td>
      </tr>
      <tr>
        <td>选择标题事件</td>
        <td>
          <select id="slt-select-name-test">
            <option value="1">选项1
            <option value="2">选项2
          </select>
        </td>
        <td><span id="slt-select-name-test-result"></span></td>
      </tr>
    </tbody>
  </table>
</body>
<script type="text/javascript">
/**
 * 断言验证
 */
document.getElementById('assert-alert-test').addEventListener('click', function() {
  alert("断言弹框");
});
/**
 * 等待验证
 */
document.getElementById('wait-start-tfm').addEventListener('click', function() {
  setTimeout(function() {document.title = '标题完整匹配';}, 5000);
});
document.getElementById('wait-start-trm').addEventListener('click', function() {
  setTimeout(function() {document.title = '标题正则匹配';}, 5000);
});
document.getElementById('wait-start-ufm').addEventListener('click', function() {
  setTimeout(function() {window.location.search = "test-full-match=1";}, 5000);
});
document.getElementById('wait-start-urm').addEventListener('click', function() {
  setTimeout(function() {window.location.search = "test-reg-match=1";}, 5000);
});
document.getElementById('wait-start-ep').addEventListener('click', function() {
  setTimeout(function() {document.getElementById("wait-start-ep-container").innerHTML = '<span id="ep-new-elem">NewElem</span>';}, 5000);
});
document.getElementById('wait-start-ev').addEventListener('click', function() {
  setTimeout(function() {document.getElementById("elem-visiable-test").style.display = 'block';}, 5000);
});
document.getElementById('wait-start-ei').addEventListener('click', function() {
    setTimeout(function() {document.getElementById("elem-invisiable-test").style.display = 'none';}, 5000);
  });
document.getElementById('wait-start-etfm').addEventListener('click', function() {
  setTimeout(function() {document.getElementById("elem-txt-full-match").innerText = '全文匹配';}, 5000);
});
document.getElementById('wait-start-etrm').addEventListener('click', function() {
  setTimeout(function() {document.getElementById("elem-txt-reg-match").innerText = '正则匹配';}, 5000);
});
document.getElementById('wait-start-ap').addEventListener('click', function() {
  setTimeout(function() {alert("测试弹窗");}, 5000);
});

/**
 * 操作验证
 */
document.getElementById('file-upload-test').addEventListener('change', function() {
  document.getElementById('file-upload-test-result').innerText = '文件已选择';
});
document.getElementById('slt-select-name-test').addEventListener('change', function() {
  document.getElementById('slt-select-name-test-result').innerText = document.getElementById('slt-select-name-test').value;
});
document.getElementById('blk-hover-test').addEventListener('mouseenter', function() {
  document.getElementById('blk-hover-test-result').innerText = 'mouseenter被触发';
});
document.getElementById('file-upload-test').addEventListener('change', function() {
  document.getElementById('file-upload-test-result').innerText = '文件已选择';
});
document.getElementById('txt-input-test').addEventListener('change', function() {
  document.getElementById('txt-input-test-result').innerText = document.getElementById('txt-input-test').value;
});
document.getElementById('btn-click-test').addEventListener('click', function() {
  document.getElementById('btn-click-test-result').innerText = '按钮被点击';
});
document.getElementById('btn-dblclick-test').addEventListener('dblclick', function() {
  document.getElementById('btn-dblclick-test-result').innerText = '按钮被双击';
});
</script>
</html>