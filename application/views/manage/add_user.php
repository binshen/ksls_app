<style type="text/css">
    .file-box{ position:relative;width:340px}
    .btn{ background-color:#FFF; border:1px solid #CDCDCD;height:21px; width:70px;}
    .file{ position:absolute; top:0; right:80px; height:24px; filter:alpha(opacity:0);opacity: 0;width:300px }
</style>
<div class="pageContent">
    <form method="post" enctype="multipart/form-data" action="<?php echo site_url('manage/save_user');?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="55">
            <fieldset>
                <legend>用户信息</legend>
                <dl>
                    <dt>姓名：</dt>
                    <dd>
                        <input type="hidden" name="id" value="<?php if(!empty($id)) echo $id;?>">
                        <input name="rel_name" type="text" class="required" value="<?php if(!empty($rel_name)) echo $rel_name;?>" />
                    </dd>
                </dl>
                <dl>
                    <dt>电话：</dt>
                    <dd>
                        <input name="tel" type="text" class="required" value="<?php if(!empty($tel)) echo $tel;?>" />
                    </dd>
                </dl>
                <dl>
                    <dt>所属公司：</dt>
                    <dd>
                        <select name="company_id" class="combox" id="selectCompany" ref="selectSubSidiary" refUrl="/manage/get_subsidiary_list/{value}" >
                            <?php
                            if (!empty($company_list)):
                                foreach ($company_list as $row):
                                    $selected = !empty($company_id) && $row->id == $company_id ? "selected" : "";
                                    ?>
                                    <option value="<?php echo $row->id; ?>" <?php echo $selected; ?>><?php echo $row->name; ?></option>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>所属分店：</dt>
                    <dd>
                        <select name="subsidiary_id" class="combox" id="selectSubSidiary">
                            <?php
                            if (!empty($subsidiary_list)):
                                foreach ($subsidiary_list as $row):
                                    $selected = !empty($subsidiary_id) && $row['id'] == $subsidiary_id ? "selected" : "";
                                    ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>><?php echo $row['name']; ?></option>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>角色：</dt>
                    <dd>
                        <select name="role_id" class="combox" id="selectRole">
                            <?php
                            if (!empty($role_list)):
                                foreach ($role_list as $row):
                                    $selected = !empty($role_id) && $row['id'] == $role_id ? "selected" : "";
                                    ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>><?php echo $row['name']; ?></option>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>头像：</dt>
                    <dd>
                        <div class="file-box">
                            <input type="hidden" name="old_img" value="<?php if(!empty($pic)) echo $pic;?>" />
                            <input type='text' id='textfield' class='txt' value="<?php if(!empty($pic)) echo $pic;?>" />
                            <input type='button' class='btn' value='浏览...' />
                            <input type="file" name="userfile" class="file" id="fileField"  onchange="document.getElementById('textfield').value=this.value" />
                        </div>
                    </dd>
                </dl>
                <dl class="nowrap">
                    <dt>头像预览：</dt>
                    <dd id="img"><?php if(!empty($pic)):?><img height="50px" width="50px" src="<?php echo base_url().$pic;?>" /><?php endif;?></dd>
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
    $("#fileField").change(function(){
        var objUrl = getObjectURL(this.files[0]);
        if (objUrl) {
            html = '<img height="50px" width="50px" src="'+objUrl+'" />';
            $("#img").html(html) ;
        }
    }) ;
    //建立一個可存取到該file的url
    function getObjectURL(file) {
        var url = null ;
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    }
</script>
