<?php
$this->registerJsFile('/jquery-ui-1.12.1/jquery-ui.min.js');
$this->registerCssFile('/jquery-ui-1.12.1/jquery-ui.min.css');
?>
<?= $this->render("../module/head_tag.php"); ?>
<div class="wrap">
    <div class="owl-mode">
        <?= $this->render("../module/mode.php",array("pageNavSub"=>$pageNavSub)); ?>
    </div>
    <div class="owl-content">
        <?= $this->render('../module/header'); ?>
        <div class="content">
            <div class="pull-right">
                <button id="new" type="button" class="pure-btn btn-large btn-red">创建</button>
            </div>
            <h3 class="title">微博列表</h3>

            <div class="form-eval">
                <div class="pure-g">
                    <div class="pure-u-1-3">
                        爬虫状态：
                        <select class="form-control" id="crawler_status" style="width:90px">
                            <option value="">全部</option>
                            <?php foreach($crawler_status as $k=>$v){ ?>
                                <option value="<?php echo $k;?>" <?php if(array_key_exists("s",$filters)&&trim($_GET["s"])===trim($k)){echo "selected";}?> ><?php echo $v;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="pure-u-1-3">
                        <input id="q" value="<?php echo array_key_exists("q",$filters)?$filters["q"]:"";?>" type="text" placeholder="名称" class="input-label" style="width: 200px;">
                        <button id="search" type="button" class="pure-btn btn-red" style="width:60px;padding-left: 1em; padding-right: 1em; ">查询</button>
                    </div>
                    <div class="pure-u-1-3">
                        最近更新时间：<?php echo $crawler_update["update_time"];?>
                        <button id="update" type="button" class="pure-btn btn-red <?php if($crawler_update["status"]==1||$crawler_update["status"]==0){echo "pure-btn-disabled";}?> " style="width:60px;padding-left: 1em; padding-right: 1em; "><?php echo $crawler_update["status"]==-1?"更新":"更新中";?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="table-box">
                <div class="table-list">
                    <div id="table-data">
                        <table class="pure-table pure-table-line pure-table-striped pure-table-hover" style="width: 1500px">
                            <thead>
                            <tr>
                                <th class="td-head" style="width: 250px;">名称</th>
                                <th>关注度（万）</th>
                                <th>发帖量（万）</th>
                                <th>原著贴吧关注度与发帖量之比</th>
                                <th>异常原因</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($list as $l){ ?>
                                <tr>
                                    <td class="td-head"><a style="cursor:pointer;" id="edit_<?php echo $l["name"];?>"><?php echo $l["name"];?></a></td>
                                    <td><?php echo $l["follow"];?></td>
                                    <td><?php echo $l["post"];?></td>
                                    <td><?php echo $l["per"];?></td>
                                    <td><?php echo $l["error_msg"];?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-head" id="table-head" style="width: 250px;"></div>
            </div>
            <br>
            <?= $this->render('../module/page',$page_info); ?>
        </div>
        <?= $this->render('../module/footer'); ?>
<style>
    #table-data .td-control, #table-control .td-control{
        width: 150px;
    }
</style>
<script type="text/javascript">
    var win = {};

    $(document).ready(function () {
        onResize();
        $(window).bind('resize', onResize);

        function onResize() {
            win.w = $(window).width();
            win.h = $(window).height();
        }

        $('.form-eval select').each(function (i, e) {
            selectbox(this);
        });

        $("#table-head").html($("#table-data").html());
        $(".table-list").mCustomScrollbar({
            axis: "x",
            theme: "dark",
            // scrollInertia: 160,
            mouseWheel: {enable: false}
        });

        $('#new').on('click',function(){
            window.location.href='/crawler/tieba-edit?act=add'
        });
        $('#table-head').on('click','a[id^="edit_"]',function(){
            window.location.href='/crawler/tieba-edit?act=edit&name='+$(this).attr('id').split('_')[1];
        });
        $('#search').on('click',function(){
            window.location.href='/crawler/tieba?q='+$.trim($('#q').val())+'&s='+$('#crawler_status option:selected').val()
        });
        $('#q').on('keypress',function(e){
            if(e.keyCode==13){
                window.location.href='/crawler/tieba?q='+$.trim($(this).val())+'&s='+$('#crawler_status option:selected').val()
            }
        });
        $('#update').on('click',function(){
            $.post('ajax/crawler_update_status',{
                name:'tieba'
            },function(json){
                __BDP.alertBox("提示",json.msg,'','',function(){
                    if(json.r==1){
                        window.location.reload();
                    }
                });
            },'json');
        });
    });
</script>
