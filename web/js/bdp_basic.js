/**
 * @author Ewen
 * 初始化参数和基础函数定义
 * 必要任务设定
 */
var __BDP = {
    //定義米飯Cookie名稱
    COOKIE_NAME: "OWL_SYS_2016",
    COOKIE_USER: "OWL_SYS_USER",
    COOKIE_PATH: "./",
    // COOKIE_DOMAIN: ".itensyn.com",
    COOKIE_DOMAIN: "",
    // URL_BASE: "http://www.itensyn.com/",
    URL_BASE: "./",
    // URL_API: "http://www.itensyn.com/api/v1/",
    URL_API: "./api/v1/",
    URL_MY: "./",

    //檢查UUID
    checkUUID: function () {
        if (__BDP.isCookieExist()) {
            var __uuid = __BDP.readCookie("UUID");
            return __uuid;
        } else {
            return false;
        }
    },
    //寫入Cookie
    addCookie: function ($vname, $value) {
        var __obj = {};
        if (__BDP.isCookieExist()) {
            __obj = JSON.parse($.cookie(__BDP.COOKIE_NAME));
            __obj[$vname] = $value;
        } else {
            __obj[$vname] = $value;
        }
        //來個100年的Cookie
        //var expires = +(new Date()) + 100 * 12 * 30 * 24 * 3600 * 1000;
        $.cookie(__BDP.COOKIE_NAME, JSON.stringify(__obj), {expires: 720, path: __BDP.COOKIE_PATH, domain: __BDP.COOKIE_DOMAIN});
    },
    //讀取Cookie
    readCookie: function ($vname) {
        if (__BDP.isCookieExist()) {
            var __obj = JSON.parse($.cookie(__BDP.COOKIE_NAME));
            return __obj[$vname];
        }
    },

    //檢查是否存在米飯Cookie
    isCookieExist: function ($cookieName) {
        var cookieName = $cookieName || __BDP.COOKIE_NAME;
        if ($.cookie(cookieName) == undefined || $.cookie(cookieName) == null || $.cookie(cookieName) == "null") {
            return false;
        } else {
            return true;
        }
    },

    //清理用户登录信息
    clearUserInfo: function () {
        $.cookie(__BDP.COOKIE_USER, null, {expires: -1, path: __BDP.COOKIE_PATH, domain: __BDP.COOKIE_DOMAIN});
    },

    //寫入Cookie   (对象 ， 写入值， 保存时间（天数，默认1天）)
    addUserCookie: function ($vname, $value, $vtime) {
        var __obj;
        if (__BDP.isCookieExist(__BDP.COOKIE_USER)) {
            __obj = JSON.parse($.cookie(__BDP.COOKIE_USER));
        } else {
            __obj = new Object();
        }
        __obj[$vname] = $value;

        //默认保存7天的Cookie
        var expires = new Date();
        //var holdTime = $vtime || 7;
        //var expires = (+new Date()) + holdTime * 24 * 3600 * 1000;
        var _expires = $vtime || 7;
        $.cookie(__BDP.COOKIE_USER, JSON.stringify(__obj), {expires: _expires, path: __BDP.COOKIE_PATH, domain: __BDP.COOKIE_DOMAIN});
    },
    //讀取Cookie
    readUserCookie: function ($vname) {
        if (__BDP.isCookieExist(__BDP.COOKIE_USER)) {
            var __obj = JSON.parse($.cookie(__BDP.COOKIE_USER));
            return __obj[$vname];
        }
    },

    //清除Cookie
    clearCookie: function () {
        var _expires = -1;
        $.cookie(__BDP.COOKIE_NAME, null, {expires: _expires, path: __BDP.COOKIE_PATH, domain: __BDP.COOKIE_DOMAIN});
    },

    isMobile: {
        _ua: window.navigator.userAgent,
        Android: function () {
            return this._ua.match(/Android/i) ? true : false;
        },
        BlackBerry: function () {
            return this._ua.match(/BlackBerry/i) ? true : false;
        },
        iOS: function () {
            return this._ua.match(/iPhone|iPad|iPod/i) ? true : false;
        },
        Windows: function () {
            return this._ua.match(/IEMobile/i) ? true : false;
        },
        WeiXin: function () {
            return this._ua.match(/MicroMessenger/i) ? true : false;
        },
        any: function () {
            return (this.Android() || this.BlackBerry() || this.iOS() || this.Windows());
        }
    }
};


//__BDP.alertBox("提示", "您已经是写手了，可以直接发表文章！");
__BDP.alertBox = function ($aTitle, $aTxt, $aUrl, $target, $callback) {
    var tmpstr = '<div class="win win-alert"><h3 class="heading">' + $aTitle + '</h3><p class="box-alert">' + $aTxt + '</p><p class="box-footer"><a href="#" class="pure-btn btn-red popup-close">确 定</a></p></div>';
    if (__BDP_C.strLen($aTxt) < 44) {
        tmpstr = tmpstr.replace('box-alert', 'box-alert text-center');
    }
    var curPopup = $.magnificPopup.open({
        items: {
            src: tmpstr,
            type: 'inline'
        },
        mainClass: 'mfp-fade',
        midClick: true,
        closeBtnInside: true,
        closeOnBgClick: false,
        callbacks: {
            open: function () {
                console.log('Popup is open');
                $(".win-alert").on("click", ".pure-btn", eventJudge)
            },
            close: function () {
            },
            afterClose: function () {
                console.log('Popup is completely closed');
                $(".win-alert").off("click", ".pure-btn", eventJudge);
            }
        }
    });

    function eventJudge(e) {
        console.log("eventJudge");
        if ($aUrl != undefined && $aUrl != "") {
            if ($target != undefined) {
                window.open($aUrl, $target);
            } else {
                location.href = $aUrl;
            }
        }
        if (typeof($callback) == "function") {
            $callback();
        }
    }

};


/**
 * @author Ewen
 * 用户登录用 第三方API
 */

var __BDP_API = {

    LoginUID: "",
    initPwdChangeEvent: function () {
        $('#formPwdChange input').focus(function () {
            $(this).parent().find("span.input-error").remove();
        });
        $('#formPwdChange').submit(function (e) {
            e.preventDefault();
            $(this).find("span.input-error").remove();
            if (!__BDP_VDT.Validate(this)) {
                return false;
            } else {
                __BDP_API.sendPwdChangeData();
                return false;
            }
        });
    },
    sendPwdChangeData: function (e) {
        var obj_sendData = {
            PWD: __BDP_C.MD5($("#formPwdChange input[name='password']").val()),       //MD5密碼
            PWD: __BDP_C.MD5($("#formPwdChange input[name='passwordNew']").val())       //MD5新密碼
        };

        /*var __sendData = {
         type: "POST",
         url: __BDP.URL_API + "member/login.php",
         data: obj_loginData,
         cache: false,
         datatype: 'json',
         success: function (e) {
         var __data = e;
         if (__data['STATUS'] == 0) {
         var userMXID = __data['MXID'];
         var userAK = __data['AK'];
         __BDP.setUserInfo(userMXID, userAK);
         $('#formLogin .error-msg').hide();
         $.magnificPopup.close();
         if (__BDP_C.getQueryString("urlref") != null) {
         location.href = decodeURIComponent(__BDP_C.getQueryString("urlref"));
         }
         } else {
         $('#formLogin .error-msg').html("手机号码或密码错误，请检查!").show();
         }
         }
         };
         $.ajax(__sendData);*/

        //输出提示
        $.magnificPopup.close();
        __BDP.alertBox("提示", "密码已经修改成功，请牢记新密码！");

        console.log("提交密码修改");
    },
    logout: function ($redirect) {
        //提交退出信息
        console.log("提交退出信息");
        if (typeof ($redirect) == "string") {
            location.href = $redirect;
        } else if ((typeof ($redirect) == "boolean" && $redirect === true) || (typeof (isMember) == "boolean" && isMember === true)) {
            //如果是后台退出或指定强制退出 跳转到首页
            location.href = __BDP.URL_BASE;
        }
    },
    go_register: function (url) {
        var url = url || "apply.php";
        if (__BDP_C.getQueryString("urlref") != null) {
            location.href = __BDP.URL_BASE + url + "?urlref=" + encodeURIComponent(__BDP_C.getQueryString("urlref"));
        } else {
            location.href = __BDP.URL_BASE + url + "?urlref=" + encodeURIComponent(location.href);
        }
    },
    openChangePwd: function () {
        //location.href = __BDP.URL_BASE + "forgot_password.php?urlref=" + encodeURIComponent(location.href);
        $.magnificPopup.open({
            items: {
                src: __BDP.URL_BASE + 'module/popup/pwd_change.php'
            },
            mainClass: 'mfp-fade',
            type: 'ajax',
            midClick: true,
            closeBtnInside: true,
            closeOnBgClick: false,
            callbacks: {
                parseAjax: function (mfpResponse) {
                },
                ajaxContentAdded: function () {
                    __BDP_API.initPwdChangeEvent();
                }
            }
        });
    }
};
