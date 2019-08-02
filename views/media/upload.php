<?php
    $this->registerJsFile('/jquery-ui-1.12.1/jquery-ui.min.js');
    $this->registerCssFile('/jquery-ui-1.12.1/jquery-ui.min.css');
    $this->registerJsFile('/js/plupload.full.min.js');
    $this->registerJsFile('/js/pages/media_upload.js');

    $upload_buttons=array(
        "poster","resource","video"
    );
    $cols=array(
        "program_name","program_default_name", "type",
        "play_time", "platform", "start_time",
        "copyright", "start_type", "satellite",
        "creator", "content_type", "team",
        "intro", "play1", "play3", "play6"
    );

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
            <div>
                <button type="button" class="pure-btn <?php if($page_info["list_count"]==0||count($unvalids)>0){echo "pure-btn-disabled";}?> " id="<?php if($page_info["list_count"]!=0&&count($unvalids)===0){echo "audit";}?>" style="width: 100px; margin-right: 1em;">上传</button>
                <button id="export" type="button" class="pure-btn">导出录入单</button>
            </div>
            <br class="clear">
            <div class="form-eval">
                <div class="pure-g">
                    <div class="pure-u-1-3">
                        <label for="name">日　　期</label>
                        <input value="<?php echo date("Y-m-d");?>" id="date" type="text" placeholder="" class="input-label">
                    </div>
                    <div class="pure-u-1-3">
                        <label for="remark">备注信息</label>
                        <input id="remark" type="text" placeholder="" class="input-label" style="width: 170px;">
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <br><br>
            <div class="table-box">
                <div class="table-list" style="width: 100%; ">
                    <div id="table-data">
                        <table class="pure-table pure-table-line pure-table-striped pure-table-hover" style="width: 3200px;">
                            <thead>
                                <tr>
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
                                    <th>本季预估单集播放量</th>
                                    <th>剧集海报</th>
                                    <th>招商资源包</th>
                                    <th>视频</th>
                                    <th class="td-control" style="width:145px;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($list as $l){
                                    $media_id=$l["media_id"];
                            ?>
                                <tr>
                                <?php foreach($cols as  $key => $c){ ?>
                                    <td <?php if($key==0){echo ' class="td-head"';}?> style="<?php if(array_key_exists($media_id,$unvalids)&&in_array($c,$unvalids[$media_id])){echo "background-color:#FFFF00";}?>" ><?php echo $c=="intro"?csubstr($l["intro"],0,80):$l[$c];?></td>
                                <?php } ?>
                                <?php foreach($upload_buttons as $b){?>
                                    <td>
                                        <?php if($b=="poster"&&array_key_exists($media_id,$attachs)){ ?>
                                            <img id="img_<?php echo $media_id;?>" src="<?php echo Yii::$app->params['UPLOAD_URL'].$attachs[$media_id][$b];?>" class="img-poster">
                                        <?php } ?>
                                        <button name="upload" id="<?php echo $b.'_'.$media_id;?>" type="button" class="pure-btn btn-xsmall">上传</button>
                                    </td>
                                <?php } ?>
                                    <td class="td-control">
                                        <!-- <a id="edit_<?php echo $l["media_id"];?>" type="button" class="pure-btn btn-min">编辑</a> -->
                                        <a id="delete_<?php echo $media_id;?>" type="button" class="pure-btn btn-min">删除</a>
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-head" id="table-head" style="width: 120px;"></div>
            </div>
            <?= $this->render('../module/page',$page_info); ?>
        </div>
        <div class="content">
            <div class="tab-group">
                <div class="tab-menus">
                    <a href="#" class="tab-menu active"><span class="arrow-tag"></span>Excel批量导入</a>
                </div>
                <div class="tab-item active" id="tab-iten-02" style="height: 400px;">
                    <br><br><br><br>
                    <form class="pure-form">
                        <table class="pure-table pure-table-none" style="width: 50%;">
                            <tbody>
                            <tr>
                                <td>&nbsp;</td>
                                <td>第一步</td>
                                <td>
                                    <button id="template" type="button" class="pure-btn btn-large btn-red">导出Excel模板</button>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>第二步</td>
                                <td>
                                    <button id="upload_excel" type="button" class="pure-btn btn-large btn-red">导入Excel</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                    <br>
                </div>
            </div>
        </div>
        <?= $this->render('../module/footer'); ?>
