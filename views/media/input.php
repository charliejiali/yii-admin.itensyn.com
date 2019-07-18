<?php
$this->registerJsFile('/jquery-ui-1.12.1/jquery-ui.min.js');
$this->registerCssFile('/jquery-ui-1.12.1/jquery-ui.min.css');

$start_date=isset($filters["start_date"])?$filters["start_date"]:"";
$end_date=isset($filters["end_date"])?$filters["end_date"]:"";
$supplier=isset($filters["supplier"])?$filters["supplier"]:"";
?>
<?= $this->render("../module/head_tag.php"); ?>
<div class="wrap">
    <div class="owl-mode">
        <?= $this->render("../module/mode.php",array("pageNavSub"=>$pageNavSub)); ?>
    </div>
    <div class="owl-content">
        <?= $this->render('../module/header'); ?>
        <div class="content">
            <h3 class="title">待审核录入单</h3>
            <div class="form-eval">
                <div class="pure-g">
                    <div class="pure-u-1-3">
                        <label for="name">提交日期</label>
                        <input id="start_date" value="<?=$start_date;?>" type="text" placeholder="" class="input-label" style="width:98px;"> -
                        <input id="end_date" value="<?=$end_date;?>" type="text" placeholder="" class="input-label" style="width:98px">
                    </div>
                    <div class="pure-u-1-3">
                        <label for="company">媒体平台</label>
                        <select class="form-control" id="supplier">
                            <option value="">全部</option>
                            <?php foreach($platforms as $s){ ?>
                                <option value="<?php echo $s;?>" <?php if($s==$supplier){echo "selected";}?> ><?php echo $s;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="pure-u-1-3">
                        <button id="search" type="button" class="pure-btn btn-red" style="width:60px;padding-left: 1em; padding-right: 1em; ">查询</button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <br><br>
            <div class="table-box">
                <div class="table-list">
                    <div id="table-data">
                        <table class="pure-table pure-table-line pure-table-striped pure-table-hover"
                               style="width: 100%">
                            <thead>
                                <tr>
                                    <th>录入单号</th>
                                    <th>数量</th>
                                    <th>供应商</th>
                                    <th>提交日期</th>
                                    <th>备注</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($list as $l){ ?>
                                <tr v-for="l in list">
                                    <td><a name="audit" id="<?=$l["input_id"];?>" href="javascript:;">
                                            <?=$l["name"];?></a>
                                    </td>
                                    <td><?=$l["total"];?></td>
                                    <td><?=$l["supplier"];?></td>
                                    <td><?=$l["create_date"];?></td>
                                    <td><?=$l["remark"];?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        <?= $this->render('../module/page',$page_info); ?>
        </div>
        <?= $this->render('../module/footer'); ?>
<script>
    $(document).ready(function(){
        var win = {};


        function onResize() {
            win.w = $(window).width();
            win.h = $(window).height();
        }


        $(window).bind('resize', onResize);

        $('.form-eval select').each(function (i, e) {
            selectbox(this);
        });

        $('#start_date').datepicker({
            dateFormat:'yy-mm-dd'
        });

        $('#end_date').datepicker({
            dateFormat:'yy-mm-dd'
        });

        $('#search').on('click',function(){
            var params={};
            params['start_date']=$('#start_date').val();
            params['end_date']=$('#end_date').val();
            params['supplier']=$('#supplier').val();

            window.location.href='input-list?'+ $.param(params);
        });
        $('a[name="audit"]').on('click',function(){
            window.location.href='/media/input-audit-list?id='+$(this).attr('id');
        });


        onResize();
    });
</script>
