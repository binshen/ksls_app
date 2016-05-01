<style type="text/css">
    .file-box{ position:relative;width:340px}
    .btn{ background-color:#FFF; border:1px solid #CDCDCD;height:21px; width:70px;}
    .file{ position:absolute; top:0; right:80px; height:24px; filter:alpha(opacity:0);opacity: 0;width:300px }
</style>
<div class="pageContent">
    <form method="post" enctype="multipart/form-data" action="<?php echo site_url('manage/save_role');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="55">
            <fieldset>
                <legend>用户信息</legend>
                <dl>
                    <dt>用户名：</dt>
                    <dd>
                        <input type="hidden" name="id" value="<?php if(!empty($id)) echo $id;?>">
                        <input name="username" type="text" class="required" value="<?php if(!empty($username)) echo $username;?>" />
                    </dd>
                </dl>
                <dl>
                    <dt>密码：</dt>
                    <dd>
                        <input name="password" type="password" class="required" value="" />
                    </dd>
                </dl>
                <dl>
                    <dt>真实姓名：</dt>
                    <dd>
                        <input name="rel_name" type="text" class="required" value="<?php if(!empty($rel_name)) echo $rel_name;?>" />
                    </dd>
                </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" class="icon-save">保存</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close icon-close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>
<script>

</script>
