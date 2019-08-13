<?php
//include_once("include/Input.class.php");
//$unaudit_count=Input::get_unaudit();
$unaudit_count=0;
$pageNavId=$this->context->pageNavId;
?>
<a href="./" class="logo"></a>
<div class="pure-menu menu-mode">
    <ul class="pure-menu-list">
<!--        <li class="pure-menu-item --><?php //if ($pageNavId == 1) {echo " pure-menu-selected";} ?><!--"><a href="user_list.php" class="pure-menu-link"><span class="icon-tools ico-home"></span>用 户 管 理</a></li>-->
<!--        <li class="pure-menu-item top-ten --><?php //if ($pageNavId == 1) {echo " pure-menu-selected";} ?><!--">-->
<!--            <a href="user_list.php" class="pure-menu-link --><?php //if ($pageNavSub == 11) {echo " active";} ?><!--">用户列表</a>-->
<!--            <a href="user_add.php" class="pure-menu-link --><?php //if ($pageNavSub == 12) {echo " active";} ?><!--">新建用户</a>-->
<!--        </li>-->

        <li class="pure-menu-item <?php if ($pageNavId == 2) {echo " pure-menu-selected";} ?>"><a href="movie_log.php" class="pure-menu-link"><span class="icon-tools ico-movie"></span>媒 体 数 据</a>
            <?php if($unaudit_count>0){ ?>
                <div class="tag-notification"><?php echo $unaudit_count;?></div>
            <?php } ?>
        </li>
        <li class="pure-menu-item top-ten <?php if ($pageNavId == 2) {echo " pure-menu-selected";} ?>">
            <a href="/media/input-list" class="pure-menu-link <?php if ($pageNavSub == 21) {echo " active";} ?>">待审核录入单</a>
            <a href="/media/online-list" class="pure-menu-link <?php if ($pageNavSub == 22) {echo " active";} ?>">剧 目 列 表</a>
            <a href="/media/upload-list" class="pure-menu-link <?php if ($pageNavSub == 23) {echo " active";} ?>">补充媒体数据</a>
            <a href="/media/pre-delete-list" class="pure-menu-link <?php if ($pageNavSub == 24) {echo " active";} ?>">删 除 审 核</a>
            <a href="movie_list_delete.php" class="pure-menu-link <?php if ($pageNavSub == 25) {echo " active";} ?>">已删除剧目</a>
        </li>

<!--        <li class="pure-menu-item --><?php //if ($pageNavId == 3) {echo " pure-menu-selected";} ?><!--"><a href="/crawler/video-list?ps=待播出" class="pure-menu-link"><span class="icon-audit ico-recommend"></span>爬 虫 数 据</a></li>-->
<!--        <li class="pure-menu-item top-ten --><?php //if ($pageNavId == 3) {echo " pure-menu-selected";} ?><!--">-->
<!--            <a href="/crawler/video-list" class="pure-menu-link --><?php //if ($pageNavSub == 31) {echo " active";} ?><!--">待补充爬虫数据内容列表</a>-->
<!--            <a href="/crawler/weibo-list" class="pure-menu-link --><?php //if ($pageNavSub == 32) {echo " active";} ?><!--">微博数据</a>-->
<!--            <a href="/crawler/tieba-list" class="pure-menu-link --><?php //if ($pageNavSub == 33) {echo " active";} ?><!--">贴吧数据</a>-->
<!--        </li>-->
<!---->
<!--        <li class="pure-menu-item --><?php //if ($pageNavId == 4) {echo " pure-menu-selected";} ?><!--"><a href="media_download.php" class="pure-menu-link"><span class="icon-audit ico-data"></span>待人工采集数据</a></li>-->
<!--        <li class="pure-menu-item top-ten --><?php //if ($pageNavId == 4) {echo " pure-menu-selected";} ?><!--">-->
<!--            <a href="media_download.php" class="pure-menu-link --><?php //if ($pageNavSub == 41) {echo " active";} ?><!--">已爬完剧目列表下载</a>-->
<!--            <a href="tensyn_movie_edit.php" class="pure-menu-link --><?php //if ($pageNavSub == 42) {echo " active";} ?><!--">人工补充完剧目上传</a>-->
<!--        </li>-->
<!--        <li class="pure-menu-item --><?php //if ($pageNavId == 7) {echo " pure-menu-selected";} ?><!--"><a href="program_list.php" class="pure-menu-link"><span class="icon-audit ico-data"></span>正在评估剧目</a></li>-->
<!--        <li class="pure-menu-item --><?php //if ($pageNavId == 9) {echo " pure-menu-selected";} ?><!--"><a href="history_list.php" class="pure-menu-link"><span class="icon-audit ico-message"></span>历史剧目</a></li>-->
    </ul>
</div>
