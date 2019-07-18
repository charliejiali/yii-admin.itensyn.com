<div id="page" class="table-footer">
    <div class="page-control">
        每页显示<?php echo $pagecount;?>条 &nbsp; &nbsp;
        <a onclick="jump(1)" href="javascript:;" class="btn-page">首页</a>
        <a onclick="jump(<?=intval($page)-1;?>)" href="javascript:;" class="btn-page">上一页</a>
        <a onclick="jump(<?=intval($page)+1;?>)" href="javascript:;" class="btn-page">下一页</a>
        <a onclick="jump(<?=intval($page_count);?>)" href="javascript:;" class="btn-page">尾页</a>
        <input onkeydown="jumpto(this.value)" id="pageNum" type="text" value="<?php echo $page;?>" class="input-num" size="2">
    </div>
    记录共<?php echo $list_count;?>条，<?php echo $page_count;?>页
</div>
<script>
    var pathname=window.location.pathname;
    var search=window.location.search;
    var page_count='<?=$page_count;?>';

    function jump(page){
        if(page>0&&page<=page_count){
            make_url(pathname,search,page);
        }
    }
    function jumpto(page){
        if(event.keyCode==13){
            jump(page);
        }
    }
    function make_url(pathname,search,page){
        var param=[];
        if(search!=''){
            search=search.substr(1);
            var params=search.split('&');
            for(var i in params){
                var kv=params[i];
                var _kv=kv.split('=');
                if(_kv[0]!='p'){
                    param.push(kv);
                }
            }
        }
        param.push('p='+page);
        window.location.href=pathname+'?'+param.join('&')
    }
</script>
