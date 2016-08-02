<form id="pagerForm" method="post" action="<?php echo site_url('manage/list_sum_log')?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum;?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage;?>" />
    <input type="hidden" name="company" value="<?php echo $company;?>" />
    <input type="hidden" name="style" value="<?php echo $style;?>" />
    <input type="hidden" name="user" value="<?php echo $user;?>" />
    <input type="hidden" name="demo" value="<?php echo $demo;?>" />
    <input type="hidden" name="orderField" value="<?php echo $this->input->post('orderField');?>" />
    <input type="hidden" name="orderDirection" value="<?php echo $this->input->post('orderDirection');?>" />
</form>

<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php site_url('manage/list_sum_log')?>" method="post">
        <div class="searchBar">
            <table class="searchContent" id="search_purchase_order">
                <tr>
                    <td><label>公司：</label><input type="text" size="16" name="company" value="<?php echo $company;?>" /></td>
                    <td><label>转入/转出：</label>
                        <select class="combox" name="style">
                            <option value="">全部</option>
                           <option value="1" <?php if($style==1){echo 'selected';}?>>转入</option>
                            <option value="2" <?php if($style==2){echo 'selected';}?>>转出</option>
                        </select>
                    </td>
                    <td><label>操作人员：</label><input type="text" size="16" name="user" value="<?php echo $user;?>" /></td>
                    <td><label>操作事项：</label><input type="text" size="16" name="demo" value="<?php echo $demo;?>" /></td>
                </tr>
                <tr>
                    <td><label>时间：</label>
                        <input name="start_time" value="<?php echo $user;?>" type="text"
                                style="background:#fff url(/static/js/My97DatePicker/skin/datePicker.gif) no-repeat right;"
                               onfocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})">
                         到</td>
                    <td>
                       <!-- <input id="date" name="end_time" value="<?php /*echo $user;*/?>" type="text"
                        style="background:#fff url(/static/js/My97DatePicker/skin/datePicker.gif) no-repeat right;"
                               onfocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})">-->
                        </td>
                </tr>
            </table>
            <div class="subBar">
                <ul>
                    <li><div class="button"><div class="buttonContent"><button id="clear_search">清除查询</button></div></div></li>
                    <li><div class="buttonActive"><div class="buttonContent"><button type="submit">执行查询</button></div></div></li>
                </ul>
            </div>
        </div>
    </form>
</div>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <?php if($this->session->userdata('permission_id') == 1): ?>
                <li><a class="delete" href="<?php echo site_url('manage/delete_sum_log')?>/{id}" target="ajaxTodo"  title="确定要删除？" warn="请选择一条记录"><span>删除</span></a></li>
            <?php endif ?>
        </ul>
    </div>

    <div layoutH="54" id="list_warehouse_in_print">
        <table class="list" width="100%" targetType="navTab" asc="asc" desc="desc">
            <thead>
            <tr>
                <th width="60">ID</th>
                <th>公司名称</th>
                <th>转入</th>
                <th>转出</th>
                <th>操作事项</th>
                <th>操作人员</th>
                <th>操作时间</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($res_list)):
                foreach ($res_list as $row):
                    ?>
                    <tr target="id" rel=<?php echo $row->id; ?>>
                        <td><?php echo $row->id;?></td>
                        <td><?php echo $row->name;?></td>
                        <td>
                            <?php
                            if($row->style==1){
                                echo $row->qty;
                            }
                            ?>
                        </td>
                        <td><?php
                            if($row->style==2){
                                echo $row->qty;
                            }
                            ?>
                        </td>
                        <td><?php echo $row->demo;?></td>
                        <td><?php echo $row->rel_name;?></td>
                        <td><?php echo $row->created;?></td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            </tbody>
        </table>
    </div>
    <div class="panelBar" >
        <div class="pages">
            <span>显示</span>
            <select name="numPerPage" class="combox" onchange="navTabPageBreak({numPerPage:this.value})">
                <option value="20" <?php echo $this->input->post('numPerPage')==20?'selected':''?>>20</option>
                <option value="50" <?php echo  $this->input->post('numPerPage')==50?'selected':''?>>50</option>
                <option value="100" <?php echo $this->input->post('numPerPage')==100?'selected':''?>>100</option>
                <option value="200" <?php echo $this->input->post('numPerPage')==200?'selected':''?>>200</option>
            </select>
            <span>条，共<?php  echo $countPage;?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $countPage;?>" numPerPage="<?php echo $numPerPage;?>" pageNumShown="10" currentPage="<?php echo $pageNum;?>"></div>
    </div>
</div>