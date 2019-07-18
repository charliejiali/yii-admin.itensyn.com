var __uuid, __where;
var __mxid, __ak;
/**
 * Created by ewen on 2016/5/12.
 */

$(document).ready(function () {
    //检测屏幕方向
    resizeHandler();
    $(window).resize(function () {
        resizeHandler();
    });

    initInputSelect();

    $(document).on('click', '.popup-close', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });

    //$("img.lazy").lazyload({effect: "fadeIn"});

    $(".btn-go-top").on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 600);
    });


    if (!Modernizr.csspointerevents) {
        // not-supported
    }

    $(window).scroll(function (e) {
        var htmlHeight = document.body.scrollHeight || document.documentElement.scrollHeight;
        var clientHeight = document.body.clientHeight || document.documentElement.clientHeight;
        var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
        // console.log(htmlHeight, scrollTop);
        if (scrollTop > 300) {
            $(".btn-go-top").show();
        } else {
            $(".btn-go-top").hide();
        }
    });

    /*
     if (IETester() >= 9) {
     }
     */

    $(".btn-logout").on("click", function (e) {
        e.preventDefault();
        __BDP.alertBox("确认退出", "是否关闭当前页面？", "index.php");
    });

    $(".btn-pwd-change").on("click", function (e) {
        e.preventDefault();
        __BDP_API.openChangePwd();
    });

    // $(".tab-group").on("click", ".tab-menu", function (e) {
    //     e.preventDefault();
    //     var $tabGroup = $(this).parents(".tab-group");
    //     var tabActiveID = $(this).index();
    //     console.log(tabActiveID);
    //     $(this).siblings().removeClass("active");
    //     $(this).addClass("active");
    //     $tabGroup.find(".tab-item").removeClass("active");
    //     $tabGroup.find(".tab-item").eq(tabActiveID).addClass("active");
    // });
});

////////////// 显示游戏界面

var pageW = window.innerWidth;
var pageH = window.innerHeight;

function resizeHandler() {
    pageW = $(window).width();
    pageH = $(window).height();

    if (!Modernizr.csscalc) {
        // $(".page-in-one").height(pageH - 62);
    }
}

function initInputSelect() {
    $('.pure-form .select-group').on("click", ".option", function (e) {
            e.preventDefault();
            var curIndex = $(this).index();
            var _group_tag = $(this).parent();
            //console.log("_group_tag", curIndex);
            _group_tag.find(".option").each(function (index, element) {
                if (index == curIndex) {
                    $(element).addClass("active");
                } else {
                    $(element).removeClass("active");
                }
            });
        }
    );
}
