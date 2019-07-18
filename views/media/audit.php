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
                <a href="movie_log.php" type="submit" class="pure-btn btn-red">返回</a>
            </div>
            <div class="pure-g">
                <div class="pure-u-1-6"><span class="label-tag">审核中</span></div>
                <div class="pure-u-2-3">
                    <table class="pure-table pure-table-none">
                        <tr>
                            <td>供应商：<?php echo $input["supplier"];?></td>
                            <td>提交日期：<?php echo $input["create_date"];?></td>
                            <td>备注信息：<?php echo $input["remark"];?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div style="padding-top: 2em;text-align: center">
                <button id="audit" type="button" class="pure-btn btn-large btn-red">审 核</button>
            </div>
        </div>
        <div class="content">
            <div class="pull-right">
                <button id="batch_yes" type="button" class="pure-btn">批量通过</button>
                <button id="batch_no" type="button" class="pure-btn">批量拒绝</button>
            </div>
            <h3 class="title">录入单: <?php echo $input["name"];?></h3>
            <br class="clear">
            <div class="table-box">
                <div class="table-list">
                    <div id="table-data">
                        <table class="pure-table pure-table-line pure-table-striped pure-table-hover" style="width: 3600px">
                            <thead>
                            <tr>
                                <th class="td-head" style="width: 100px;"><div class="input-checkbox-all" style="width:30px;">标记</div></th>
                                <th class="td-head" style="width: 90px;">状态</th>
                                <th class="td-head" style="width: 120px;">剧目名称</th>
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
                                <th>海报</th>
                                <th>资源</th>
                                <th>视频</th>
                                <th class="td-control">编辑</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($list as $l){
                                    ?>
                                <tr>
                                    <td class="td-head">
                                        <div name="select" id="<?php echo $l["media_id"];?>" class="input-checkbox"></div>
                                        <span class="movie-tag <?php echo $type_status[$l["type_status"]];?>"></span></td>
                                    <td class="td-head" id="<?php echo $l["media_id"];?>"><?php echo $status[$l["status"]];?></td>
                                    <td class="td-head"><?php echo $l["program_name"];?></td>
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

                                    <?php foreach($attachs[$l["media_id"]] as $k=>$v){?>
                                        <td>
                                            <?php echo $v;?>
                                        </td>
                                    <?php } ?>

                                    <td class="td-control">
                                        <?php if($l["status"]!=3){ ?>
                                            <a name="yes" id="<?php echo $l["media_id"];?>" type="button" class="pure-btn btn-min">通过</a>
                                            <a name="no" id="<?php echo $l["media_id"];?>" type="button" class="pure-btn btn-min">拒绝</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-control" id="table-control" style="width: 150px;"></div>
                <div class="table-head" id="table-head" style="width: 310px;"></div>
            </div>
            <br>
            <?= $this->render('../module/page',$page_info); ?>
        </div>
        <?= $this->render('../module/footer'); ?>

<style>
    #table-data .td-control, #table-control .td-control {
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

        $("#table-control").html($("#table-data").html());
        $("#table-head").html($("#table-data").html());

        $(".table-list").mCustomScrollbar({
            axis: "x",
            theme: "dark",
            mouseWheel: {enable: false}
        });


        $(".input-checkbox").on("click", function () {
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
            } else {
                $(this).addClass("active");
            }
        });

        $(".input-checkbox-all").on("click", function () {
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(".input-checkbox").removeClass("active");
            } else {
                $(this).addClass("active");
                $(".input-checkbox").addClass("active");
            }
        });

        // 审批单条
        $('#table-control ').on('click','a[name]',function(){
            var id=$(this).attr('id');
            var type=$(this).attr('name');

            $.post('/media/media-audit',{id:id,type:type},function(json){
                __BDP.alertBox('提示',json.msg,'','',function(){
                    if(json.r==1){
                        $('td[id="'+id+'"]').text(type=='yes'?'审批通过':'审批未通过');
                    }
                });
            },'json');
        });
        // 审批多条
        $('button[id^="batch_"]').on('click',function(){
            var id=$('div[name="select"].active').map(function(){
                return $(this).attr('id');
            }).get().join(',');
            var type=$(this).attr('id').split('_')[1]
            if(id==''){
                __BDP.alertBox('提示','请至少选择一条剧目','','',function(){
                });
                return false;
            }

            $.post('/media/media-audit',{id:id,type:type},function(json){
                __BDP.alertBox('提示',json.msg,'','',function(){
                    if(json.r==1){window.location.reload();}
                });
            },'json');
        });
        // 审批录入单
        $('#audit').on('click',function(){
            var id='<?php echo $input["input_id"];?>'
            $.post('/media/input-audit',{id:id},function(json){
                __BDP.alertBox('提示',json.msg,'','',function(){
                    if(json.r==1){window.location.href='/media/input-list';}
                });
            },'json');
        });
    });
</script>

</body>
</html>
