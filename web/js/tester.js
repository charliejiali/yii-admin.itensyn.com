/**
 * Created by TW on 2014/5/27.
 */
function js_getDPI() {
    var arrDPI = new Array();
    if (window.screen.deviceXDPI != undefined) {
        arrDPI[0] = window.screen.deviceXDPI;
        arrDPI[1] = window.screen.deviceYDPI;
    }
    else {
        var tmpNode = document.createElement("DIV");
        tmpNode.style.cssText = "width:1in;height:1in;position:absolute;left:0px;top:0px;z-index:99;visibility:hidden";
        document.body.appendChild(tmpNode);
        arrDPI[0] = parseInt(tmpNode.offsetWidth);
        arrDPI[1] = parseInt(tmpNode.offsetHeight);
        tmpNode.parentNode.removeChild(tmpNode);
    }
    return arrDPI;
}

function BrowserTester() {
    var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
    var isOpera = userAgent.indexOf("Opera") > -1;
    if (isOpera) {
        return "Opera"
    }    //判断是否Opera浏览器
    if (userAgent.indexOf("Firefox") > -1) {
        return "FF";
    } //判断是否Firefox浏览器
    if (userAgent.indexOf("Safari") > -1) {
        return "Safari";
    } //判断是否Safari浏览器
    if (userAgent.indexOf("Trident") > -1 && userAgent.indexOf("MSIE") > -1 && !isOpera) {
        return "IE";
    } //判断是否IE浏览器
}


var NV = {};
var UA = navigator.userAgent.toLowerCase();
try {
    // NV.name = !-[1,] ? 'ie' :
    NV.name = (UA.indexOf("msie") > 0 || UA.indexOf("trident") > 0) ? 'ie' :
        (UA.indexOf("firefox") > 0) ? 'firefox' :
            (UA.indexOf("chrome") > 0) ? 'chrome' :
                window.opera ? 'opera' :
                    window.openDatabase ? 'safari' :
                        'unkonw';
} catch (e) {
}

try {
    NV.version = (NV.name == 'ie') ? UA.match(/rv:(\d+\.\d+)/)[1] :
        (NV.name == 'firefox') ? UA.match(/firefox\/([\d.]+)/)[1] :
            (NV.name == 'chrome') ? UA.match(/chrome\/([\d.]+)/)[1] :
                (NV.name == 'opera') ? UA.match(/opera.([\d.]+)/)[1] :
                    (NV.name == 'safari') ? UA.match(/version\/([\d.]+)/)[1] :
                        '0';
} catch (e) {
}

try {
    NV.shell = (UA.indexOf('360ee') > -1) ? '360极速浏览器' :
        (UA.indexOf('360se') > -1) ? '360安全浏览器' :
            (UA.indexOf('se') > -1) ? '搜狗浏览器' :
                (UA.indexOf('aoyou') > -1) ? '遨游浏览器' :
                    (UA.indexOf('theworld') > -1) ? '世界之窗浏览器' :
                        (UA.indexOf('worldchrome') > -1) ? '世界之窗极速浏览器' :
                            (UA.indexOf('greenbrowser') > -1) ? '绿色浏览器' :
                                (UA.indexOf('qqbrowser') > -1) ? 'QQ浏览器' :
                                    (UA.indexOf('baidu') > -1) ? '百度浏览器' :
                                        '未知或无壳';
} catch (e) {
}

function IETester(userAgent) {
    var UA = userAgent || navigator.userAgent;
    if (/msie/i.test(UA)) {
        return parseFloat(UA.match(/msie (\d+\.\d+)/i)[1]);
    } else if (~UA.toLowerCase().indexOf('trident') && ~UA.indexOf('rv')) {
        return parseFloat(UA.match(/rv:(\d+\.\d+)/)[1]);
    }
    return 999;
}