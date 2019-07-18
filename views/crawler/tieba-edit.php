<?= $this->render("../module/head_tag.php"); ?>
<div class="wrap">
    <div class="owl-mode">
        <?= $this->render("../module/mode.php",array("pageNavSub"=>$pageNavSub)); ?>
    </div>
    <div class="owl-content">
        <?= $this->render('../module/header'); ?>

        <div class="content">
            <h3 class="title"></h3>
            <div class="form-user-add">
                <form class="pure-form">
                    <table class="pure-table pure-table-none" style="width: 90%">
                        <tbody>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <h3 class="title">基本信息</h3></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="color-red">*</td>
                            <td>名称</td>
                            <td><input name="name" type="text" placeholder="" class="input-form" value="<?php echo count($old)>0?$old["name"]:"";?>"></td>
                        </tr>
                        <tr>
                            <td class="color-red">*</td>
                            <td>状态</td>
                            <td>
                                <select id="crawler_status">
                                    <?php foreach($crawler_status as $k=>$v){ ?>
                                        <option <?php if($status===intval($k)){echo "selected";}?> value="<?php echo $k;?>"><?php echo $v;?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="color-red"></td>
                            <td>关注度（万）</td>
                            <td><input name="follow" type="text" placeholder="" class="input-form" value="<?php echo count($old)>0?$old["follow"]:"";?>"></td>
                        </tr>
                        <tr>
                            <td class="color-red"></td>
                            <td>发帖量（万）</td>
                            <td><input name="post" type="text" placeholder="" class="input-form" value="<?php echo count($old)>0?$old["post"]:"";?>"></td>
                        </tr>
                        <tr>
                            <td class="color-red"></td>
                            <td>原著贴吧关注度与发帖量之比</td>
                            <td><input name="per" type="text" placeholder="" class="input-form" value="<?php echo count($old)>0?$old["per"]:"";?>"></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <div style="padding: 1em;">
                                    <br>
                                    <button id="submit" type="button" class="pure-btn btn-large btn-red">保 存</button>
                                    &nbsp; &nbsp; &nbsp; &nbsp;
                                    <button id="back" type="button" class="pure-btn btn-large btn-red">取 消</button>
                                    <br>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
                <br>
            </div>
            <!--           Teb END     tab-item-01-->
        </div>
        <?= $this->render('../module/footer'); ?>

<script type="text/javascript">
    var act='<?php echo $act;?>';


    $('#back').on('click',function(){
        window.location.href="/crawler/tieba-list";
    });

    $('#submit').on('click',function(){
        var input={}
        input['act']=act;

        $('input[name]').each(function(){
            input[$(this).attr('name')]=$(this).val();
        });
        input["status"]=$('#crawler_status option:selected').val()

        $.post('/crawler/edit-tieba',input,function(json){
            __BDP.alertBox("提示",json.msg,"","",function(){
                if(json.r==1){window.location.href='/crawler/tieba-list';}
            })
        },'json');
    });
</script>

