<?php
if(!array_key_exists("ps",$filters)){
    header('Location:/crawler/video-list?ps=待播出');
}
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
            <h3 class="title">剧目列表</h3>

            <div class="form-eval">
                <div class="pure-g">
                    <div class="pure-u-1-3">
                        <input id="q" value="<?php echo array_key_exists("q",$filters)?$filters["q"]:"";?>" type="text" placeholder="剧目/男主演/女主演/主持人/团队" class="input-label" style="width: 200px;">
                    </div>
                    <div class="pure-u-1-3">
                        剧目状态：
                        <select class="form-control" id="play_status" style="width:90px">
                            <option value="">全部</option>
                            <?php foreach($play_status as $ps){ ?>
                                <option value="<?php echo $ps;?>" <?php if(array_key_exists("ps",$filters)&&trim($filters["ps"])===$ps){echo "selected";}?> ><?php echo $ps;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="pure-u-1-3">
                        爬虫状态：
                        <select class="form-control" id="crawler_status" style="width:90px">
                            <option value="">全部</option>
                            <?php foreach($crawler_status as $k=>$v){ ?>
                                <option value="<?php echo $k;?>" <?php if(array_key_exists("s",$filters)&&trim($filters["s"])===trim($k)){echo "selected";}?> ><?php echo $v;?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="pure-u-1-3">
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
                        <table class="pure-table pure-table-line pure-table-striped pure-table-hover" style="width: 6000px">
                            <thead>
                            <tr>
                                <th class="td-head" style="width: 250px;">剧目名称</th>

                                <th>剧目状态</th>
                                <th>爬虫状态</th>
                                <th>上季剧目名称</th>
                                <th>剧目URL</th>
                                <th>单集播放量（万）</th>
                                <th>预告片播放量（万）</th>
                                <th>男主演</th>
                                <th>男主演代表作</th>
                                <th>男主演代表作URL</th>
                                <th>男主演代表作单集播放量（万）</th>
                                <th>女主演</th>
                                <th>女主演代表作</th>
                                <th>女主演代表作URL</th>
                                <th>女主演代表作单集播放量（万）</th>
                                <th>主持人</th>
                                <th>主持人代表作</th>
                                <th>主持人代表作URL</th>
                                <th>主持人代表作单集播放量（万）</th>
                                <th>制作团队</th>
                                <th>制作团队代表作</th>
                                <th>制作团队代表作URL</th>
                                <th>制作团队代表作单集播放量（万）</th>
                                <th>常驻嘉宾</th>
                                <th>异常原因</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($list as $l){
                                $status=$list_status[$l["id"]];
                                ?>
                                <tr>
                                    <td class="td-head"><a style="cursor:pointer;" id="edit_<?php echo $l["id"];?>"><?php echo $l["program_name"];?></a></td>

                                    <td><?php echo $l["play_status"];?></td>
                                    <td><?php echo $crawler_status[$status];?></td>
                                    <td><?php echo $l["ex_program_name"];?></td>
                                    <td><?php echo $l["url"];?></td>
                                    <td><?php echo $l["pv_avg"];?></td>
                                    <td><?php echo $l["preview_pv_avg"];?></td>
                                    <?php
                                    foreach($list_masterpiece[$l["id"]] as $k=>$v){
                                        ?>
                                        <td><?php echo $l[$k];?></td>
                                        <td><?php echo $v["program_name"];?></td>
                                        <td><?php echo $v["url"];?></td>
                                        <td><?php echo $v["pv_avg"];?></td>
                                    <?php } ?>
                                    <td><?php echo $l["guest"];?></td>
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


    });

//    var page=parseInt('<?php //echo $page;?>//');
//    var page_count='<?php //echo $page_count;?>//';
//    var params={};
//    params["p"]=parseInt('<?php //echo $page;?>//');
//    params["c"]='<?php //echo $pagecount;?>//';
//    params['q']=$.trim($('#q').val());
//    params['ps']=$('#play_status option:selected').val();
//    params['s']=$('#crawler_status option:selected').val();
//
//    $('a[id^="page"]').on('click',function(){
//        var type=$(this).attr('id').split('_')[1];
//
//        switch(type){
//            case "first":
//                params["p"]=1;
//                break;
//            case "pre":
//                if(page-1<=0){return false;}
//                params["p"]=page-1;
//                break;
//            case "next":
//                if(page+1>page_count){return false;}
//                params["p"]=page+1;
//                break;
//            case "last":
//                params["p"]=page_count;
//                break;
//        }
//        window.location.href='crawler_video.php?'+$.param(params);
//    });
//    $('#pageNum').on('keypress',function(e){
//        var value=parseInt($(this).val());
//
//        if(e.keyCode==13){
//            if(value<=0||value>page_count||isNaN(value)){
//                return false;
//            }else{
//                params["p"]=value;
//                window.location.href='crawler_video.php?'+$.param(params);
//            }
//        }
//    });
//
//    $('#new').on('click',function(){
//        window.open('crawler_video_edit.php?act=add');
//    });
//    $('#table-head').on('click','a[id^="edit_"]',function(){
//        window.open('crawler_video_edit.php?act=edit&id='+$(this).attr('id').split('_')[1]);
//    });
    $('#search').on('click',function(){
        window.location.href='/crawler/video-list?q='+$.trim($('#q').val())+'&ps='+$('#play_status option:selected').val()+'&s='+$('#crawler_status option:selected').val();
    });
//    $('#q').on('keypress',function(e){
//        if(e.keyCode==13){
//            window.open('crawler_video.php?q='+$.trim($(this).val())+'&ps='+$('#play_status option:selected').val()+'&s='+$('#crawler_status option:selected').val());
//        }
//    });
//    $('#update').on('click',function(){
//        $.post('ajax/crawler_update_status.php',{
//            name:'video'
//        },function(json){
//            __BDP.alertBox("提示",json.msg,'','',function(){
//                if(json.r==1){
//                    window.location.reload();
//                }
//            });
//        },'json');
//    });
</script>
