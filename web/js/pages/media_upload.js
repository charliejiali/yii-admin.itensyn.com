/**
 * Created by zhangjiali on 2018/11/16.
 */
$(document).ready(function(){
    var win={};
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

    $('#date').datepicker({
        dateFormat:'yy-mm-dd'
    });
    // 删除
    $('a[id^="delete_"]').on('click',function(){
        $.post('/media/delete-log',{media_id:$(this).attr('id').split('_')[1]},function(json){
            __BDP.alertBox('提示',json.msg,'','',function(){
                if(json.r=='1'){
                    window.location.reload();
                }
            });
        },'json')
    });
    // 模板下载
    $('#template').on('click',function(){
        window.open('/media_template.xlsx');
    });
    // 导出
    $('#export').on('click',function(){
        window.open('/media/export-upload');
    });
    // 上传数据
    $('#audit').on('click',function(){
        $.post('/media/input-add',{
            date:$('#date').val(),
            remark:$('#remark').val()
        },function(json){
            __BDP.alertBox("提示",json.msg,'','',function(){
                if(json.r==1){
                    window.location.reload();
                }
            });
        },'json');
    });

    // excel上传
    var uploader = new plupload.Uploader({
        browse_button: 'upload_excel', // this can be an id of a DOM element or the DOM element itself
        url: '/media/upload-excel',
        filters: {
            mime_types : [
                { title : "Excel files", extensions : "xls,xlsx" }
            ]
        }
    });
    uploader.init();
    uploader.bind('FilesAdded', function(up, files) {
        uploader.start();
    });
    uploader.bind('FileUploaded',function(up,file,obj){
        var json=$.parseJSON(obj.response);
        __BDP.alertBox('提示',json.msg,'','',function(){
            if(json.r=='1'){
                window.location.reload();
            }
        });
    });
    // 附件上传
    $('#table-data button[name="upload"]').each(function(){
        var button_id=$(this).attr('id');
        var temp=button_id.split('_');
        var type=temp[0];
        var media_id=temp[1];
        make_upload(button_id,media_id,type);
    });

    function make_upload(button_id,media_id,type){
        var mime_type={};
        switch(type){
            case "poster":
                mime_type={title:"Image files",extensions:"jpg,jpeg,png,gif"};
                break;
            case "resource":
                mime_type={title:"Excel files",extensions:"xls,xlsx"};
                break;
            case "video":
                mime_type={title:"Video files",extensions:"avi,wmv,mpeg,mp4,mov,mkv,flv,f4v,m4v,rmvb,rm,3gp,dat,mts,vob"};
                break;
        }

        var uploader = new plupload.Uploader({
            browse_button: button_id, // this can be an id of a DOM element or the DOM element itself
            url: '/media/upload-attach',
            multipart_params : {
                "type":type,
                "media_id":media_id
            },
            filters: {
                mime_types : [mime_type]
            }
        });

        uploader.init();

        uploader.bind('FilesAdded', function(up, files) {
            uploader.start();
        });
        uploader.bind('FileUploaded',function(up,file,obj){
            var json=$.parseJSON(obj.response);
            __BDP.alertBox('提示',json.msg,'','',function(){
                var img_id='#img_'+media_id;

                if($(img_id).length>0){
                    $(img_id).attr('src',json.path);
                }else{
                    $('#'+button_id).before('<img src="'+json.path+'" class="img-poster">');
                }
            });
        });
    }
});
