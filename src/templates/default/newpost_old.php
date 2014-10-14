<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

// 导入ueditor
echo '
<script type="text/javascript" src="../../ueditor/editor_config.js"></script>
<script type="text/javascript" src="../../ueditor/editor_all.js"></script>
';

// 
echo '
<form action="',$_SERVER["REQUEST_URI"],'" method="post">
<input type="hidden" name="formhash" value="',$formhash,'" />
<div class="title">
    <a href="/">',$options['name'],'</a> &raquo; ';
if($options['main_nodes']){
    echo '<select name="select_cid">';
    foreach($main_nodes_arr as $n_id=>$n_name){
        if($cid == $n_id){
            $sl_str = ' selected="selected"';
        }else{
            $sl_str = '';
        }
        echo '<option value="',$n_id,'"',$sl_str,'>',$n_name,'</option>';
    }
    echo '</select>';
}else{
    echo '    <a href="/n-',$c_obj['id'],'">',$c_obj['name'],'</a> (',$c_obj['articles'],')';
}
echo '
     - 发新帖
</div>

<div class="main-box">';

// 
if($tip){
    echo '<p class="red">',$tip,'</p>';
}
?>
<div id="content" class="w900 border-style1 bg">
        <div class="section">
            <h3>UEditor - 完整示例</h3>

            <p class="note">注：线上演示版上传图片功能一次只能上传一张，涂鸦功能不能将背景和图片合成，而下载版没有限制</p>
<p>
	<input type="text" style="width:520px" name="title" value="<?php htmlspecialchars($p_title);?>" class="sll" />
</p>
            <div class="details">
                <div>
                <script type="text/plain" id="editor"></script>

                </div>
            </div>
        </div>
        <div class="section">
            <h4>语言切换</h4>

            <div class="details">
                <input type="button" value="zh-cn" onclick="setLanguage(this)">
                <input type="button" value="en" onclick="setLanguage(this)">
            </div>
        </div>
        <div class="section">
            <h4>常用API</h4>

            <div id="allbtn" class="details">
                <div id="btns">
                    <div>
                        <input type="button" value="获得整个html的内容" onclick="getAllHtml()">
                        <input type="button" value="获得内容" onclick="getContent()">
                        <input type="button" value="写入内容" onclick="setContent()">
                        <input type="button" value="获得纯文本" onclick="getContentTxt()">
                        <input type="button" value="获得带格式的纯文本" onclick="getPlainTxt()">
                        <input type="button" value="判断是否有内容" onclick="hasContent()">
                        <input type="button" value="使编辑器获得焦点" onclick="setFocus()">
                    </div>
                    <div>
                        <input type="button" value="获得当前选中的文本" onclick="getText()">
                        <input id="enable" type="button" value="可以编辑" onclick="setEnabled()">
                        <input type="button" value="不可编辑" onclick="setDisabled()">
                        <input type="button" value="隐藏编辑器" onclick=" UE.getEditor('editor').setHide()">
                        <input type="button" value="显示编辑器" onclick=" UE.getEditor('editor').setShow()">
                    </div>

                </div>
                <div>
                    <input type="button" value="创建编辑器" onclick="createEditor()">
                    <input type="button" value="删除编辑器" onclick="deleteEditor()">
                </div>
            </div>
        </div>
    </div>
	<script type="text/javascript">
    //实例化编辑器

/**
    var options = {
        imageUrl:  "http://ueditor.baidu.com/yunserver/yunImageUp.php",
        imagePath:"http://",

        scrawlUrl: "http://ueditor.baidu.com/yunserver/yunScrawlUp.php",
        scrawlPath:"http://",

        fileUrl: "http://ueditor.baidu.com/yunserver/yunFileUp.php",
        filePath:"http://",

        catcherUrl: "php/getRemoteImage.php",
        catcherPath: "php/",

        imageManagerUrl: "../yunserver/yunImgManage.php",
        imageManagerPath:"http://",

        snapscreenHost:'ueditor.baidu.com',
        snapscreenServerUrl:UEDITOR_HOME_URL + "../yunserver/yunSnapImgUp.php",
        snapscreenPath:"http://",

        wordImageUrl:UEDITOR_HOME_URL + "../yunserver/yunImageUp.php",
        wordImagePath:"http://", //

        getMovieUrl:UEDITOR_HOME_URL + "../yunserver/getMovie.php",

        lang:/^zh/.test(navigator.language || navigator.browserLanguage || navigator.userLanguage) ? 'zh-cn' : 'en',
        langPath:UEDITOR_HOME_URL + "lang/",

        webAppKey:"9HrmGf2ul4mlyK8ktO2Ziayd",
        initialFrameWidth:860,
        initialFrameHeight:420,
        focus:true
    };*/
    var ue = UE.getEditor('editor', window.UEDITOR_CONFIG);
    var domUtils = UE.dom.domUtils;
    ue.addListener("ready", function () {
        ue.focus(true);
    });
    UE.getEditor('editor').setContent('欢迎使用ueditor');
    function setLanguage(obj) {
        var value = obj.value,
                opt = {
                    lang:value
                };
        UE.utils.extend(opt, window.UEDITOR_CONFIG, true);

        UE.getEditor("editor").destroy();
        UE.getEditor('editor', opt);
    }
    function createEditor() {
        enableBtn();
        UE.getEditor('editor', {
            initialFrameWidth:"100%"
        })
    }
    function getAllHtml() {
        alert(UE.getEditor('editor').getAllHtml())
    }
    function getContent() {
        var arr = [];
        arr.push("使用editor.getContent()方法可以获得编辑器的内容");
        arr.push("内容为：");
        arr.push(UE.getEditor('editor').getContent());
        alert(arr.join("\n"));
    }
    function getPlainTxt() {
        var arr = [];
        arr.push("使用editor.getPlainTxt()方法可以获得编辑器的带格式的纯文本内容");
        arr.push("内容为：");
        arr.push(UE.getEditor('editor').getPlainTxt());
        alert(arr.join('\n'))
    }
    function setContent() {
        var arr = [];
        arr.push("使用editor.setContent('欢迎使用ueditor')方法可以设置编辑器的内容");
        UE.getEditor('editor').setContent('欢迎使用ueditor');
        alert(arr.join("\n"));
    }
    function setDisabled() {
        UE.getEditor('editor').setDisabled('fullscreen');
        disableBtn("enable");
    }

    function setEnabled() {
        UE.getEditor('editor').setEnabled();
        enableBtn();
    }

    function getText() {
        //当你点击按钮时编辑区域已经失去了焦点，如果直接用getText将不会得到内容，所以要在选回来，然后取得内容
        var range = UE.getEditor('editor').selection.getRange();
        range.select();
        var txt = UE.getEditor('editor').selection.getText();
        alert(txt)
    }

    function getContentTxt() {
        var arr = [];
        arr.push("使用editor.getContentTxt()方法可以获得编辑器的纯文本内容");
        arr.push("编辑器的纯文本内容为：");
        arr.push(UE.getEditor('editor').getContentTxt());
        alert(arr.join("\n"));
    }
    function hasContent() {
        var arr = [];
        arr.push("使用editor.hasContents()方法判断编辑器里是否有内容");
        arr.push("判断结果为：");
        arr.push(UE.getEditor('editor').hasContents());
        alert(arr.join("\n"));
    }
    function setFocus() {
        UE.getEditor('editor').focus();
    }
    function deleteEditor() {
        disableBtn();
        UE.getEditor('editor').destroy();
    }
    function disableBtn(str) {
        var div = document.getElementById('btns');
        var btns = domUtils.getElementsByTagName(div, "input");
        for (var i = 0, btn; btn = btns[i++];) {
            if (btn.id == str) {
                domUtils.removeAttributes(btn, ["disabled"]);
            } else {
                btn.setAttribute("disabled", "true");
            }
        }
    }
    function enableBtn() {
        var div = document.getElementById('btns');
        var btns = domUtils.getElementsByTagName(div, "input");
        for (var i = 0, btn; btn = btns[i++];) {
            domUtils.removeAttributes(btn, ["disabled"]);
        }
    }
</script>
<?php 
echo '


<p>
	<textarea id="id-content" name="content">',htmlspecialchars($p_content),'</textarea>
</p>
';

echo '
	<script type="text/javascript">
		UE.getEditor("id-content",{
            //这里可以选择自己需要的工具按钮名称,此处仅选择如下五个
            toolbars:[[\'FullScreen\', \'Source\', \'Undo\', \'Redo\',\'Bold\',\'test\']],
            //focus时自动清空初始化时的内容
            autoClearinitialContent:true,
            //关闭字数统计
            wordCount:false,
            //关闭elementPath
            elementPathEnabled:false
            //更多其他参数，请参考editor_config.js中的配置项
        })
	</script>
';

// <textarea id="id-content" name="content" class="mll tall">',htmlspecialchars($p_content),'</textarea>
if(!$options['close_upload']){
    include(dirname(__FILE__) . '/upload.php');
}
echo '
<p><div class="float-left"><input type="submit" value=" 发 表 " name="submit" class="textbtn" /></div><div class="c"></div></p>
</form>

</div>';


?>