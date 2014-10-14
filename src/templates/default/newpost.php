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

 
if($tip){
    echo '<p class="red">',$tip,'</p>';
}

?>
<div id="content" class="w900 border-style1 bg">
        <div class="section">
			<p>
				<input type="text" style="width:520px" name="title" value="<?php htmlspecialchars($p_title);?>" class="sll" />
			</p>
            <div class="details">
                <div>
                <script type="text/plain" id="id-content"></script>

                </div>
            </div>
        </div>
</div>
<script type="text/javascript">
    //实例化编辑器
    var ue = UE.getEditor('id-content', window.UEDITOR_CONFIG);
    var domUtils = UE.dom.domUtils;
    ue.addListener("ready", function () {
        ue.focus(true);
    });
    UE.getEditor('id-content').setContent('点击输入');
</script>


<?php 

// <textarea id="id-content" name="content" class="mll tall">',htmlspecialchars($p_content),'</textarea>
if(!$options['close_upload']){
    include(dirname(__FILE__) . '/upload.php');
}
echo '
<p><div class="float-left"><input type="submit" value=" 发 表 " name="submit" class="textbtn" /></div><div class="c"></div></p>
</form>

</div>';


?>