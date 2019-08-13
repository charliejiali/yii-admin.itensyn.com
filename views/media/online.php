<?php
function csubstr( $str, $start = 0, $length, $charset = "utf-8", $suffix = true ) {
    if ( function_exists( "mb_substr" ) ) {
        if ( mb_strlen( $str, $charset ) <= $length ) {
            return $str;
        }
        $slice = mb_substr( $str, $start, $length, $charset );
    } else {

        $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

        preg_match_all( $re[ $charset ], $str, $match );
        if ( count( $match[0] ) <= $length ) {
            return $str;
        }
        $slice = join( "", array_slice( $match[0], $start, $length ) );
    }
    if ( $suffix ) {
        return $slice . "⋯";
    }

    return $slice;
}

$system_status=array("2"=>"审批通过","3"=>"待删除");
$system_year=array("2017","2018","2019","2020","2021");
$system_season=array("Q1","Q2","Q3","Q4");

$type_status=array(
    -1=>"delete",
    0=>"new",
    1=>"same",
    2=>"update"
);

$this->registerJsFile('/jquery-ui-1.12.1/jquery-ui.min.js');
$this->registerCssFile('/jquery-ui-1.12.1/jquery-ui.min.css');
$this->registerJsFile('/js/pages/movie_list.js');

$start_date=isset($filters["start_date"])?$filters["start_date"]:"";
$end_date=isset($filters["end_date"])?$filters["end_date"]:"";
$type=isset($filters["type"])?$filters["type"]:"";
$year=isset($filters["year"])?$filters["year"]:"";
$season=isset($filters["season"])?$filters["season"]:"";
$status=isset($filters["status"])?$filters["status"]:"";
$program_name=isset($filters["program_name"])?$filters["program_name"]:"";
?>
<?= $this->render("../module/head_tag.php"); ?>
<div class="wrap">
    <div class="owl-mode">
        <?= $this->render("../module/mode.php",array("pageNavSub"=>$pageNavSub)); ?>
    </div>
    <div class="owl-content">
        <?= $this->render('../module/header'); ?>
        <div class="content">
            <h3 class="title">剧目列表</h3>
            <div class="form-eval">
                <div class="pure-g">
                    <div class="pure-u-1-3">
                        <label for="name">时　　间</label>
                        <input id="start_date" value="<?php echo $start_date;?>" type="text" placeholder="" class="input-label" style="width:98px;"> -
                        <input id="end_date" value="<?php echo $end_date;?>" type="text" placeholder="" class="input-label" style="width:98px">
                    </div>
                    <div class="pure-u-1-3">
                        <label for="type">资源类型</label>
                        <input id="type" value="<?php echo $type;?>" type="text" placeholder="" class="input-label" style="width:150px">
                    </div>
                    <div class="pure-u-1-3">
                        <label for="position">播出时间</label>
                        <select class="form-control" id="select_year" style="width:90px">
                            <option value="">全部</option>
                            <?php foreach($system_year as $sy){ ?>
                                <option value="<?php echo $sy;?>" <?php if($sy==$year&&trim($year)!==""){echo "selected";}?> ><?php echo $sy;?></option>
                            <?php } ?>
                        </select>
                        年
                        <select class="form-control" id="select_season" style="width:90px">
                            <option value="">全部</option>
                            <?php foreach($system_season as $ss){ ?>
                                <option value="<?php echo $ss;?>" <?php if($ss==$season&&trim($season)!==""){echo "selected";}?> ><?php echo $ss;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="pure-u-1-3" style="width:230px;">
                        <label for="company">状　　态</label>
                        <select class="form-control" id="select_status" style="width:90px;">
                            <option value="">全部</option>
                            <?php foreach($system_status as $k=>$v){ ?>
                                <option value="<?php echo $k;?>" <?php if($k==$status){echo "selected";}?> ><?php echo $v;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="pure-u-1-3">
                        <input id="program_name" value="<?php echo $program_name;?>" type="text" placeholder="剧目名称" class="input-label" style="width: 170px;">
                        <button id="search" type="button" class="pure-btn btn-red" style="width:60px;padding-left: 1em; padding-right: 1em; ">查询</button>
                        <!--                        <button id="export" type="button" class="pure-btn btn-red" style="width:60px;padding-left: 1em; padding-right: 1em; ">导出</button>-->
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="table-box">
                <div class="table-list">
                    <div id="table-data">
                        <table class="pure-table pure-table-line pure-table-striped pure-table-hover" style="width: 3500px">
                            <thead>
                            <tr>
                                <th class="td-head" style="width: 62px;"></th>
                                <th class="td-head" style="width: 80px;">状态</th>
                                <th class="td-head" style="width: 120px;">剧目名称</th>
                                <th class="td-head" style="width: 170px;">腾信名称</th>
                                <th>剧目原名</th>
                                <th>资源类型</th>
                                <th>播出时间</th>
                                <th>媒体平台</th>
                                <th>开播时间</th>
                                <th>版权情况</th>
                                <th>播出状态</th>
                                <th>播出卫视</th>
                                <th>主创/嘉宾</th>
                                <th>内容类型</th>
                                <th>制作团队</th>
                                <th width="500">简介</th>
                                <th>本季预估播放量</th>
                                <th>集数/期数</th>
                                <th>本季预估单机播放量</th>
                                <th class="td-control" style="width:145px;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $upload_buttons=array("poster","resource","video");
                            foreach($list as $l){
                                $media_id=$l["media_id"];
                                ?>
                                <tr>
                                    <td class="td-head">
                                        <span class="movie-tag <?php echo $type_status[$l["type_status"]];?>"></span>
                                    </td>
                                    <td class="td-head"><?php echo $system_status[$l["status"]];?></td>
                                    <td class="td-head"><?php echo $l["program_name"];?></td>
                                    <td class="td-head"><input style="width:90px;" type="text" id="name_<?php echo $media_id;?>" value="<?php echo $l["tensyn_name"];?>";><button name="update" id="<?php echo "update_".$media_id;?>" type="button">更新</button></td>
                                    <td><?php echo $l["program_default_name"];?></td>
                                    <td><?php echo $l["type"];?></td>
                                    <td><?php echo $l["play_time"];?></td>
                                    <td><?php echo $l["platform"];?></td>
                                    <td><?php echo $l["start_time"];?></td>
                                    <td><?php echo $l["copyright"];?></td>
                                    <td><?php echo $l["start_type"];?></td>
                                    <td><?php echo $l["satellite"];?></td>
                                    <td><?php echo $l["creator"];?></td>
                                    <td><?php echo $l["content_type"];?></td>
                                    <td><?php echo $l["team"];?></td>
                                    <td><?php echo csubstr($l["intro"],0,80);?></td>
                                    <td><?php echo $l["play1"];?></td>
                                    <td><?php echo $l["play3"];?></td>
                                    <td><?php echo $l["play6"];?></td>
                                    <td class="td-control">
                                        <!-- <a id="edit_<?php echo $l["media_id"];?>" type="button" class="pure-btn btn-min">编辑</a> -->
                                        <a id="delete_<?php echo $media_id;?>" type="button" class="pure-btn btn-min">删除</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-head" id="table-head" style="width: 410px;"></div>
            </div>
            <br>
            <?= $this->render('../module/page',$page_info); ?>
        </div>
    </div>
</div>
        <?= $this->render('../module/footer'); ?>
<style>
    #table-data .td-control, #table-control .td-control{
        width: 150px;
    }
</style>
<script type="text/javascript">
//    var page=parseInt('<?php //echo $page;?>//');
//    var page_count='<?php //echo $page_count;?>//';
//    // var start_date=$('#start_date').val();
//    // var end_date=$('#end_date').val();
//    // var status=$('#select_status option:selected').val();
//    // var year=$('#select_year option:selected').val();
//    // var season=$('#select_season option:selected').val();
//    // var type=$('#type').val();
//    // var program_name=$('#program_name').val();
//    var params={};
//    params["p"]=parseInt('<?php //echo $page;?>//');
//    params["c"]='<?php //echo $pagecount;?>//';
//    params["start_date"]='<?php //echo $start_date;?>//';
//    params["end_date"]='<?php //echo $end_date;?>//'
//    params["status"]='<?php //echo $status;?>//'
//    params["year"]='<?php //echo $year;?>//'
//    params["season"]='<?php //echo $season;?>//'
//    params["type"]='<?php //echo $type;?>//'
//    params["program_name"]='<?php //echo $program_name;?>//'
//
    $('#search').on('click',function(){
        var params={};
        params["start_date"]=$('#start_date').val();
        params["end_date"]=$('#end_date').val();
        params["status"]=$('#select_status option:selected').val();
        params["year"]=$('#select_year option:selected').val();
        params["season"]=$('#select_season option:selected').val();
        params["type"]=$('#type').val();
        params["program_name"]=$('#program_name').val();

        window.location.href='/media/online-list?'+$.param(params);
    });
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
//        window.location.href='movie_list.php?'+$.param(params);
//    });
//    $('#pageNum').on('keypress',function(e){
//        var value=parseInt($(this).val());
//        if(e.keyCode==13){
//            if(value<=0||value>page_count||isNaN(value)){
//                return false;
//            }else{
//                params["p"]=value;
//                window.location.href='movie_list.php?'+$.param(params);
//            }
//        }
//    });
//    $('a[id^="edit_"]').on('click',function(){
//        var id=$(this).attr('id').split('_')[1];
//        window.location.href='movie_edit.php?id='+id;
//    });
    // 删除
   $('a[id^="delete"]').on('click',function(){
       var id=$(this).attr('id').split('_')[1];
       __BDP.alertBox("提示",'确定删除当前剧目？','','',function(){
           $.post('/media/pre-delete',{id:id},function(json){
               __BDP.alertBox("提示",json.msg,'','',function(){
                   if(json.r==1){window.location.reload();}
               });
           },'json');
       });

       // $.post('/media/pre-delete',{id:$(this).attr('id').split('_')[1]},function(json){
       //     __BDP.alertBox("提示",json.msg,'','',function(){
       //         if(json.r==1){window.location.reload();}
       //     });
       // },'json');
   });
//    $('#add').on('click',function(){
//        window.location.href='movie_edit.php';
//    });
//    $('#export').on('click',function(){
//        window.open('export/media_movie_list.php?'+$.param(params));
//    });
   $('#start_date').datepicker({
       dateFormat:'yy-mm-dd'
   });
   $('#end_date').datepicker({
       dateFormat:'yy-mm-dd'
   });
    // 更新腾信名称
   $('#table-head').on('click','button[name="update"]',function(){
       var id=$(this).attr('id').split('_')[1];
       var value=$('#table-head').find('input[id="name_'+id+'"]').val();

       $.post('/media/update-tensyn',{id:id,value:value},function(json){
           __BDP.alertBox("提示",json.msg,'','',function(){
               if(json.r==1){window.location.reload();}
           });
       },'json');
   })
</script>

</body>
</html>
