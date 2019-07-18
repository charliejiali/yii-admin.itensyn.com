/**
 * Created by ewen on 2016/5/12.
 */
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

    $(".form-content").mCustomScrollbar({
        axis: "y",
        theme: "dark",
        // scrollInertia: 160,
        mouseWheel: {enable: true}
    });

 
    // $(".input-checkbox").on("click", function () {
    //     if ($(this).hasClass("active")) {
    //         $(this).removeClass("active");
    //     } else {
    //         $(this).addClass("active");
    //     }
    // });

    $(".input-checkbox-all").on("click", function () {
        if ($(this).hasClass("active")) {
            $(this).removeClass("active");
            $(".input-checkbox").removeClass("active");
        } else {
            $(this).addClass("active");
            $(".input-checkbox").addClass("active");
        }
    });
});

var urlBase = 'ajax/';